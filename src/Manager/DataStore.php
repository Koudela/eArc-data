<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager;

use eArc\Data\Entity\Interfaces\Events\OnDeleteInterface;
use eArc\Data\Entity\Interfaces\Events\OnLoadInterface;
use eArc\Data\Entity\Interfaces\Events\OnPersistInterface;
use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Exceptions\Interfaces\NoDataExceptionInterface;
use eArc\Data\Manager\Interfaces\DataStoreInterface;
use eArc\Data\Query\QueryFactory;
use eArc\Data\Filesystem\StaticPersistenceService;
use eArc\QueryLanguage\Collector\QueryInitializerExtended;
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

    public function init(): void
    {
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
                $entity = di_get(DataStore::class)->load($fQCN, $primaryKey);

                if ($entity instanceof OnLoadInterface) {
                    foreach ($entity->getOnLoadCallables() as $callable) {
                        $callable();
                    }
                }

                return $entity;
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
                if ($entity instanceof OnPersistInterface) {
                    foreach ($entity->getOnPersistCallables() as $callable) {
                        $callable();
                    }
                }

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
                if ($entity instanceof OnDeleteInterface) {
                    foreach ($entity->getOnDeleteCallables() as $callable) {
                        $callable();
                    }
                }

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
             * @return EntityInterface
             *
             * @throws NoDataExceptionInterface|SerializeExceptionInterface
             */
            function data_remove(string $fQCN, string $primaryKey): EntityInterface
            {
                $entity = di_get(DataStore::class)->load($fQCN, $primaryKey);

                return data_delete($entity);
            }
        }

        if (!function_exists('data_find')) {
            /**
             * Get the primary keys for the query based on the fully qualified
             * class name. If the query is null the primary keys for all
             * entities of the class are returned.
             *
             * @param string $fQCN
             * @param array $keyValuePairs
             * @param array|null $allowedPrimaryKeys
             *
             * @return string[]
             */
            function data_find(string $fQCN, array $keyValuePairs = [], ?array $allowedPrimaryKeys = null): iterable
            {
                return di_get(QueryFactory::class)->findBy($fQCN, $keyValuePairs, $allowedPrimaryKeys);
            }
        }

        if (!function_exists('data_query')) {
            /**
             * Get the query builder based on the fully qualified class name. If
             * the query shall be executed on a subset pass the relevant primary
             * keys as second parameter.
             *
             * @param string $fQCN
             * @param array|null $allowedPrimaryKeys
             *
             * @return QueryInitializerExtended
             */
            function data_query(string $fQCN, ?array $allowedPrimaryKeys = null): QueryInitializerExtended
            {
                return di_get(QueryFactory::class)->select($allowedPrimaryKeys)->from($fQCN);
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
