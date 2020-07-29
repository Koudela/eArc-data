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
use eArc\DataStore\Exceptions\Interfaces\NoDataExceptionInterface;
use eArc\DataStore\Exceptions\Interfaces\QueryExceptionInterface;
use eArc\DataStore\Manager\Interfaces\DataStoreInterface;
use eArc\DataStore\Query\QueryService;
use eArc\DataStore\Repository\GenericRepository;
use eArc\DataStore\Repository\Interfaces\RepositoryInterface;
use eArc\DataStore\Filesystem\StaticPersistenceService;
use eArc\DataStore\Serialization\DataTypes\CollectionInterfaceDataType;
use eArc\DataStore\Serialization\DataTypes\EmbeddedCollectionInterfaceDataType;
use eArc\DataStore\Serialization\DataTypes\EmbeddedEntityInterfaceDataType;
use eArc\DataStore\Serialization\DataTypes\EntityInterfaceDataType;
use eArc\DataStore\Serialization\DataTypes\EntityProxyInterfaceDataType;
use eArc\DataStore\Serialization\DataTypes\PrimaryKeyDataType;
use eArc\Serializer\Api\Interfaces\SerializerInterface;
use eArc\Serializer\DataTypes\ArrayDataType;
use eArc\Serializer\DataTypes\ClassDataType;
use eArc\Serializer\DataTypes\ObjectDataType;
use eArc\Serializer\DataTypes\SimpleDataType;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;

class DataStore implements DataStoreInterface
{
    /** @var EntityInterface[][] */
    protected static $entities = [];

    public function load(string $fQCN, string $primaryKey): EntityInterface
    {
        if (!$this->isLoaded($fQCN, $primaryKey)) {
            self::$entities[$fQCN][$primaryKey] = di_static(StaticPersistenceService::class)::load($fQCN, $primaryKey);
        }

        return self::$entities[$fQCN][$primaryKey];
    }

    public function isLoaded(string $fQCN, string $primaryKey): bool
    {
        return isset($this->entities[$fQCN][$primaryKey]);
    }

    public function getRepository(string $fQCN): RepositoryInterface
    {
        /** @var GenericRepository[] */
        static $repositories = [];

        if (!array_key_exists($fQCN, $repositories)) {
            $repositories[$fQCN] = new GenericRepository($fQCN);
        }

        return $repositories[$fQCN];
    }

    public function init(): void
    {
        di_tag(CollectionInterfaceDataType::class, SerializerInterface::class);
        di_tag(EmbeddedCollectionInterfaceDataType::class, SerializerInterface::class);
        di_tag(EmbeddedEntityInterfaceDataType::class, SerializerInterface::class);
        di_tag(EntityInterfaceDataType::class, SerializerInterface::class);
        di_tag(EntityProxyInterfaceDataType::class, SerializerInterface::class);
        di_tag(PrimaryKeyDataType::class, SerializerInterface::class);

        di_tag(SimpleDataType::class, SerializerInterface::class);
        di_tag(ArrayDataType::class, SerializerInterface::class);
        di_tag(ClassDataType::class, SerializerInterface::class);
        di_tag(ObjectDataType::class, SerializerInterface::class);

        if (!function_exists('data_load')) {
            /**
             * Get the entity object the fully qualified class name and the
             * primary key relates to.
             *
             * @param string $fQCN
             * @param string $primaryKey
             *
             * @return EntityInterface
             *
             * @throws NoDataExceptionInterface|SerializeExceptionInterface
             */
            function data_load(string $fQCN, string $primaryKey): EntityInterface
            {
                return di_get(DataStore::class)->load($fQCN, $primaryKey);
            }
        }

        if (!function_exists('data_save')) {
            /**
             * Creates/updates the persisted data the entity relates to. May create
             * the primary key if the entity was not persisted yet.
             *
             * @param EntityInterface $entity
             *
             * @return EntityInterface
             *
             * @throws SerializeExceptionInterface
             */
            function data_save(EntityInterface $entity): EntityInterface
            {
                di_static(StaticPersistenceService::class)::persist($entity);

                return $entity;
            }
        }

        if (!function_exists('data_delete')) {
            /**
             * Deletes the persisted data the entity relates to.
             *
             * @param EntityInterface $entity
             *
             * @return EntityInterface
             *
             * @throws NoDataExceptionInterface|SerializeExceptionInterface
             */
            function data_delete(EntityInterface $entity): EntityInterface
            {
                di_static(StaticPersistenceService::class)::remove($entity);

                return $entity;
            }
        }

        if (!function_exists('data_remove')) {
            /**
             * Deletes the persisted data the fully qualified class name and
             * the primary key relates to.
             *
             * @param string $fQCN
             * @param string $primaryKey
             *
             * @throws NoDataExceptionInterface|SerializeExceptionInterface
             */
            function data_remove(string $fQCN, string $primaryKey): void
            {
                $entity = di_get(DataStore::class)->load($fQCN, $primaryKey);
                di_static(StaticPersistenceService::class)::remove($entity);
            }
        }

        if (!function_exists('data_find')) {
            /**
             * Get the primary keys for the query based on the fully qualified
             * class name. If the query is null the primary keys for all
             * entities of the class are returned.
             *
             * @param string $fQCN
             * @param string|null $query
             * @param array|null $allowedPrimaryKeys
             *
             * @return array
             *
             * @throws QueryExceptionInterface
             */
            function data_find(string $fQCN, ?string $query, ?array $allowedPrimaryKeys = null): array
            {
                return di_get(QueryService::class)->find($fQCN, $query, $allowedPrimaryKeys);
            }
        }
    }

    public function reset(?string $fQCN = null, ?string $primaryKey = null): void
    {
        if (null !== $fQCN) {
            if (null !== $primaryKey) {
                self::$entities[$fQCN][$primaryKey] = [];

                return;
            }

            self::$entities[$fQCN] = [];

            return;
        }

        self::$entities = [];
    }
}