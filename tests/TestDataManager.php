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

use eArc\Data\Initializer;
use eArc\Data\ParameterInterface;
use eArc\DataTests\env\Counter;
use eArc\DataTests\env\TaggedService;
use MyCacheBridge;
use MyDataBaseBridge;
use MyEntity;
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

    public function assertionsLoadEvents(int $cnt, MyEntity $entity): void
    {
        self::assertEquals($cnt + 1, TaggedService::$preLoadCalledCorrectly);
        self::assertEquals($cnt + 2, MyEntity::$preLoadCalledCorrectly);
        self::assertEquals($cnt + 3, di_get(MyCacheBridge::class)->onLoadCalledCorrectly);
        self::assertEquals($cnt + 4, di_get(MyDataBaseBridge::class)->onLoadCalledCorrectly);
        self::assertEquals($cnt + 5, di_get(TaggedService::class)->postLoadCalledCorrectly);
        self::assertEquals($cnt + 6, $entity->postLoadCalledCorrectly);
        self::assertEquals($cnt + 7, di_get(MyCacheBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals($cnt + 8, di_get(MyDataBaseBridge::class)->postLoadCallablesCalledCorrectly);
        self::assertEquals(0, di_get(MyCacheBridge::class)->onPersistCalledCorrectly);
        self::assertEquals(0, di_get(MyDataBaseBridge::class)->onAutoPrimaryKeyCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onRemoveCalledCorrectly);
        self::assertEquals(0, di_get(MySearchIndexBridge::class)->onFindCalledCorrectly);
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
        #TODO
    }

    public function testImmutables(): void
    {
        #TODO
    }
}
