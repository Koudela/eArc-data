<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Manager;

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\DataStore\IndexHandling\IndexEventHandler;
use eArc\DataStore\IndexHandling\PrimaryKeyGenerator;
use eArc\DataStore\Manager\Interfaces\EntitySaveStackInterface;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;
use eArc\Serializer\Exceptions\SerializeException;
use ReflectionClass;
use ReflectionException;

abstract class StaticEntitySaveStack implements EntitySaveStackInterface
{
    /** @var UniqueEntityProxy[] */
    protected static $notPersistedEntities = [];
    /** @var UniqueEntityProxy[][] */
    protected static $persistedEntities = [];

    /** @var EntityInterface[] */
    protected static $entitySaveStack = [];

    public static function requirePrimaryKey(EntityInterface $entity): void
    {
        if (null !== $entity->getPrimaryKey()) {
            throw new SerializeException(sprintf(
                'An entity is not allowed to require a second primary key. Entity class: %s. Current primary key %s',
                get_class($entity),
                $entity->getPrimaryKey()
            ));
        }

        $primaryKey = di_static(PrimaryKeyGenerator::class)::getNextPrimaryKey($entity);
        self::setPrimaryKey($entity, $primaryKey);
        self::addToStack($entity);
    }

    public static function addToStack(EntityInterface $entity): void
    {
        self::$entitySaveStack[spl_object_id($entity)] = $entity;
    }

    public static function beforeEntitySaved(EntityInterface $entity): void
    {
        di_static(IndexEventHandler::class)::beforeEntitySaved($entity);
    }

    public static function afterEntitySaved(EntityInterface $entity): void
    {
        unset(self::$entitySaveStack[spl_object_id($entity)]);

        if ($nextEntity = reset(self::$entitySaveStack)) {
            data_save($nextEntity);
        }
    }

    public static function afterEntityRemoved(EntityInterface $entity): void
    {
        di_static(IndexEventHandler::class)::afterEntityRemoved($entity);
        self::setPrimaryKey($entity, null);
    }

    /**
     * @param EntityInterface $entity
     * @param string|null $primaryKey
     *
     * @throws SerializeExceptionInterface
     */
    private static function setPrimaryKey(EntityInterface $entity, ?string $primaryKey): void
    {
        $oldPrimaryKey = $entity->getPrimaryKey();

        if ($primaryKey === null && $oldPrimaryKey === null) {
            return;
        }

        if ($primaryKey !== null && $oldPrimaryKey !== null) {
            throw new SerializeException(sprintf(
                'Primary keys are not allowed to change. Tried to change a primary key from %s to %s for entity %s.',
                $oldPrimaryKey,
                $primaryKey,
                get_class($entity)
            ));
        }

        try {
            $primaryKeyProperty = (new ReflectionClass($entity))->getProperty('primaryKey');
            $primaryKeyProperty->setAccessible(true);
            $primaryKeyProperty->setValue($entity, $primaryKey);

            if (null === $oldPrimaryKey) {
                if (isset(self::$notPersistedEntities[spl_object_id($entity)])) {
                    $proxy = self::$notPersistedEntities[spl_object_id($entity)];
                    self::$persistedEntities[get_class($entity)][$primaryKey] = $proxy;
                    unset(self::$notPersistedEntities[spl_object_id($entity)]);
                }
            } else {
                if (isset(self::$persistedEntities[get_class($entity)][$oldPrimaryKey])) {
                    $proxy = self::$persistedEntities[get_class($entity)][$oldPrimaryKey];
                    self::$notPersistedEntities[spl_object_id($entity)] = $proxy;
                    unset(self::$persistedEntities[get_class($entity)][$oldPrimaryKey]);
                }
            }

            if (isset($proxy)) {
                $reflectionProperty = (new ReflectionClass($proxy))->getProperty('primaryKey');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($proxy, $entity->getPrimaryKey());
            }
        } catch (ReflectionException $e) {
            throw new SerializeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
