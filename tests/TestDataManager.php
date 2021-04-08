<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataTests;

use eArc\Data\Entity\GenericMutableEntityReference;
use eArc\Data\Exceptions\DataException;
use eArc\Data\Initializer;
use eArc\Data\Manager\DataStore;
use eArc\Data\Manager\Interfaces\DataStoreInterface;
use eArc\Data\ParameterInterface;
use eArc\DataTests\env\Counter;
use eArc\DataTests\env\ImmutableAutoPrimaryKeyEntity;
use eArc\DataTests\env\ImmutableEntity;
use eArc\DataTests\env\ImmutableProxy;
use eArc\DataTests\env\ReverencedByGenericImmutableEntity;
use eArc\DataTests\env\ReverencedImmutableEntity;
use eArc\DataTests\env\TaggedService;
use Exception;
use MyCacheBridge;
use MyDataBaseBridge;
use MyEmbeddedEntity;
use MyEntity;
use MyRootEntity;
use MySearchIndexBridge;
use PHPUnit\Framework\TestCase;

class TestDataManager extends TestCase
{
    public function init(): void
    {
        Initializer::init();

        foreach (glob(__DIR__ . '/_doc/*.php') as $filename) {
            include_once $filename;
        }

        di_clear_cache();

        di_tag(ParameterInterface::TAG_ON_LOAD, MyCacheBridge::class);
        di_tag(ParameterInterface::TAG_ON_LOAD, MyDataBaseBridge::class);

        di_tag(ParameterInterface::TAG_ON_PERSIST, MyCacheBridge::class);
        di_tag(ParameterInterface::TAG_ON_PERSIST, MyDataBaseBridge::class);
        di_tag(ParameterInterface::TAG_ON_PERSIST, MySearchIndexBridge::class);

        di_tag(ParameterInterface::TAG_ON_REMOVE, MyCacheBridge::class);
        di_tag(ParameterInterface::TAG_ON_REMOVE, MyDataBaseBridge::class);
        di_tag(ParameterInterface::TAG_ON_REMOVE, MySearchIndexBridge::class);

        di_tag(ParameterInterface::TAG_ON_FIND, MySearchIndexBridge::class);
        di_tag(ParameterInterface::TAG_ON_FIND, MyDataBaseBridge::class);

        di_tag(ParameterInterface::TAG_ON_AUTO_PRIMARY_KEY, MyDataBaseBridge::class);

        di_tag(ParameterInterface::TAG_PRE_LOAD, TaggedService::class);
        di_tag(ParameterInterface::TAG_POST_LOAD, TaggedService::class);
        di_tag(ParameterInterface::TAG_PRE_PERSIST, TaggedService::class);
        di_tag(ParameterInterface::TAG_POST_PERSIST, TaggedService::class);
        di_tag(ParameterInterface::TAG_PRE_REMOVE, TaggedService::class);
        di_tag(ParameterInterface::TAG_POST_REMOVE, TaggedService::class);
    }

    public function testPersistEvents()
    {
        $this->init();

        $entity = new MyEntity();
        $entity->setPrimaryKey('my-pk-1');

        Counter::$cnt = 0;
        data_persist($entity);
        $this->assertionsPersistEvents(0, $entity);
        data_persist_batch([$entity]);
        $this->assertionsPersistEvents(7, $entity);
        data_schedule($entity);
        data_persist();
        $this->assertionsPersistEvents(14, $entity);
        data_schedule_batch([$entity]);
        data_persist_batch([]);
        $this->assertionsPersistEvents(21, $entity);
    }

    public function assertionsPersistEvents(int $cnt, MyEntity $entity)
    {
        self::assertEquals($cnt + 1, di_get(TaggedService::class)->prePersistCalledCorrectly);
        self::assertEquals($cnt + 2, $entity->prePersistCalledCorrectly);
        self::assertEquals($cnt + 3, di_get(MyCacheBridge::class)->onPersistCalledCorrectly);
        self::assertEquals($cnt + 4, di_get(MyDataBaseBridge::class)->onPersistCalledCorrectly);
        self::assertEquals($cnt + 5, di_get(MySearchIndexBridge::class)->onPersistCalledCorrectly);
        self::assertEquals($cnt + 6, di_get(TaggedService::class)->postPersistCalledCorrectly);
        self::assertEquals($cnt + 7, $entity->postPersistCalledCorrectly);
        self::assertEquals(0, di_get(MyCacheBridge::class)->onLoadCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->onAutoPrimaryKeyCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onRemoveCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onFindCalledCorrectly);
    }

    public function testLoadEvents(): void
    {
        $this->init();

        Counter::$cnt = 0;
        $entity = data_load(MyEntity::class, 'my-pk-1');
        $this->assertionsLoadEvents(0, $entity);
        data_load(MyEntity::class, 'my-pk-1');
        data_load_batch(MyEntity::class, ['my-pk-1'])['my-pk-1'];
        $this->assertionsLoadEvents(0, $entity);
        data_detach();
        $entity = data_load_batch(MyEntity::class, ['my-pk-1'])['my-pk-1'];
        $this->assertionsLoadEvents(8, $entity);
    }

    public function assertionsLoadEvents(int $cnt, MyEntity|null $entity): void
    {
        self::assertEquals($cnt + 1, TaggedService::$preLoadCalledCorrectly);
        self::assertEquals($cnt + 2, MyEntity::$preLoadCalledCorrectly);
        self::assertEquals($cnt + 3, di_get(MyCacheBridge::class)->onLoadCalledCorrectly);
        self::assertEquals($cnt + 4, di_get(MyDataBaseBridge::class)->onLoadCalledCorrectly);
        self::assertEquals($cnt + 5, di_get(TaggedService::class)->postLoadCalledCorrectly);
        if (!is_null($entity)) {
            self::assertEquals($cnt + 6, $entity->postLoadCalledCorrectly);
        }
        self::assertEquals($cnt + 7, di_get(MyCacheBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals($cnt + 8, di_get(MyDataBaseBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals(0, di_get(MyCacheBridge::class)->onPersistCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->onAutoPrimaryKeyCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onRemoveCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onFindCalledCorrectly);
    }

    public function testLoadFlags(): void
    {
        $this->init();

        Counter::$cnt = 0;
        $entity = data_load(MyEntity::class, 'my-pk-1');
        $this->assertionsLoadEvents(0, $entity);
        $entity = data_load(MyEntity::class, 'my-pk-1');
        $this->assertionsLoadEvents(0, $entity);
        $entity = data_load(MyEntity::class, 'my-pk-2', DataStoreInterface::LOAD_FLAG_USE_FIRST_LEVEL_CACHE_ONLY);
        $this->assertionsLoadEvents(0, $entity);
        $entity = data_load(MyEntity::class, 'my-pk-1', DataStoreInterface::LOAD_FLAG_SKIP_FIRST_LEVEL_CACHE);
        $this->assertionsLoadEvents(8, $entity);
    }

    public function testRemoveEvents(): void
    {
        $this->init();

        $entity = new MyEntity();
        $entity->setPrimaryKey('my-pk-1');

        Counter::$cnt = 0;
        data_remove($entity::class, $entity->getPrimaryKey());
        $this->assertionsRemoveEvents(0, $entity);
        data_remove_batch($entity::class, [$entity->getPrimaryKey()]);
        $this->assertionsRemoveEvents(7, $entity);
        data_delete($entity);
        $this->assertionsRemoveEvents(14, $entity);
        data_delete_batch([$entity]);
        $this->assertionsRemoveEvents(21, $entity);
    }

    public function assertionsRemoveEvents(int $cnt, MyEntity $entity)
    {
        self::assertEquals($cnt + 1, TaggedService::$preRemoveCalledCorrectly);
        self::assertEquals($cnt + 2, $entity::$preRemoveCalledCorrectly);
        self::assertEquals($cnt + 3, di_get(MyCacheBridge::class)->onRemoveCalledCorrectly);
        self::assertEquals($cnt + 4, di_get(MyDataBaseBridge::class)->onRemoveCalledCorrectly);
        self::assertEquals($cnt + 5, di_get(MySearchIndexBridge::class)->onRemoveCalledCorrectly);
        self::assertEquals($cnt + 6, TaggedService::$postRemoveCalledCorrectly);
        self::assertEquals($cnt + 7, $entity::$postRemoveCalledCorrectly);
        self::assertEquals(0, di_get(MyCacheBridge::class)->onLoadCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->onAutoPrimaryKeyCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onPersistCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onFindCalledCorrectly);

    }

    public function testFindEvents(): void
    {
        $this->init();

        Counter::$cnt = 0;
        data_find(MyEntity::class, []);
        $this->assertionsFindEvents(0);
        data_find_entities(MyEntity::class, []);
        $this->assertionsFindEvents(2);
    }

    public function assertionsFindEvents(int $cnt): void
    {
        self::assertEquals($cnt + 1, di_get(MySearchIndexBridge::class)->onFindCalledCorrectly);
        self::assertEquals($cnt + 2, di_get(MyDataBaseBridge::class)->onFindCalledCorrectly);
        self::assertEquals(0, di_get(MyCacheBridge::class)->onLoadCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->onAutoPrimaryKeyCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onPersistCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onRemoveCalledCorrectly);
    }

    public function testAutoPrimaryKeyEvents(): void
    {
        $this->init();

        Counter::$cnt = 0;
        $entity = new MyEntity();
        data_persist($entity);
        $this->assertionsAutoPrimaryKeyEvents(0, $entity);
    }

    public function assertionsAutoPrimaryKeyEvents(int $cnt, MyEntity $entity): void
    {
        self::assertEquals($cnt + 1, di_get(TaggedService::class)->prePersistCalledCorrectly);
        self::assertEquals($cnt + 2, $entity->prePersistCalledCorrectly);
        self::assertEquals($cnt + 3, di_get(MyDataBaseBridge::class)->onAutoPrimaryKeyCalledCorrectly);
        self::assertEquals(0, di_get(MyCacheBridge::class)->onLoadCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->onFindCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onFindCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onRemoveCalledCorrectly);
    }

    public function testFindInEmbeddedCollections(): void
    {
        $this->init();

        $entity = new MyRootEntity();
        $collection = $entity->getMyEmbeddedEntityCollection();
        $collection->add(new MyEmbeddedEntity('Max', 'Berlin', 42));
        $collection->add(new MyEmbeddedEntity('Moritz', 'London', 23));
        $collection->add(new MyEmbeddedEntity('Silvana', 'Madrid', 32));
        $collection->add(new MyEmbeddedEntity('Max', 'Budapest', 55));
        $collection->add(new MyEmbeddedEntity('Toni', 'London', 47));

        $cnt = 0;
        foreach ($collection->findBy(['name' => ['Max', 'Moritz']]) as $item) {
            $cnt++;
            self::assertTrue($item->getName() === 'Max' || $item->getName() === 'Moritz');
        }
        self::assertEquals(3, $cnt);

        $cnt = 0;
        foreach ($collection->findBy(['city' => ['London'], 'age' => [21, 22, 23]]) as $item) {
            $cnt++;
            self::assertTrue($item->getCity() === 'London' && $item->getAge() === 23);
        }
        self::assertEquals(1, $cnt);

        $cnt = 0;
        foreach ($collection->findBy(['name' => ['Max', 'Moritz'], 'city' => ['London'], 'age' => [50]]) as $item) {
            $cnt++;
            self::assertTrue($item instanceof MyEmbeddedEntity);
        }
        self::assertEquals(0, $cnt);
    }

    public function testImmutables(): void
    {
        $this->init();

        $immutable = new ImmutableEntity('pk-not-persisted');
        try {
            data_persist($immutable);
            $exception = null;
        } catch (Exception $exception) {
        } finally {
            self::assertNull($exception);
        }

        $immutable = new ImmutableEntity('pk-already-persisted');
        try {
            data_persist($immutable);
            $exception = null;
        } catch (Exception $exception) {
        } finally {
            self::assertTrue($exception instanceof DataException);
            self::assertTrue(str_contains($exception->getMessage(), '{d98eb0dc-9cb1-490f-807d-e5089ee85112}'));
        }

        $immutable = new ImmutableAutoPrimaryKeyEntity('pk-already-persisted');
        try {
            data_persist($immutable);
            $exception = null;
        } catch (Exception $exception) {
        } finally {
            self::assertNull($exception);
            self::assertNotEquals('pk-already-persisted', $immutable->getPrimaryKey());
        }

        $immutable = new ReverencedImmutableEntity('pk-already-persisted');
        $proxy = new ImmutableProxy('proxy-key');
        di_get(DataStore::class)->attach($proxy);
        $immutable->setProxy($proxy);
        try {
            data_persist($immutable);
            $exception = null;
        } catch (Exception $exception) {
        } finally {
            self::assertNull($exception);
            self::assertNotEquals('pk-already-persisted', $immutable->getPrimaryKey());
            self::assertEquals($proxy->getLastPersistedReferencePK(), $immutable->getPrimaryKey());
        }

        $immutable = new ReverencedImmutableEntity('pk-not-persisted');
        $proxy = new ImmutableProxy('proxy-key');
        di_get(DataStore::class)->attach($proxy);
        $immutable->setProxy($proxy);
        try {
            data_persist($immutable);
            $exception = null;
        } catch (Exception $exception) {
        } finally {
            self::assertNull($exception);
            self::assertEquals('pk-not-persisted', $immutable->getPrimaryKey());
            self::assertEquals($proxy->getLastPersistedReferencePK(), $immutable->getPrimaryKey());
        }
        $immutable->setPrimaryKey('second-pk-not-persisted');
        try {
            data_persist($immutable);
            $exception = null;
        } catch (Exception $exception) {
        } finally {
            self::assertNull($exception);
            self::assertEquals('second-pk-not-persisted', $immutable->getPrimaryKey());
            self::assertEquals($proxy->getLastPersistedReferencePK(), $immutable->getPrimaryKey());
        }

        $immutableUG = new ReverencedByGenericImmutableEntity('pk-not-persisted');
        data_persist($immutableUG);
        /** @var GenericMutableEntityReference $genericEntity */
        $genericEntity = data_load($immutableUG->getMutableReverenceClass(), $immutableUG->getMutableReverenceKey());
        self::assertSame($immutableUG, $genericEntity->getMutableReverenceTarget());
    }
}
