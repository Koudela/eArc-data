<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace {

    use eArc\Data\Entity\Interfaces\EntityInterface;
    use eArc\Data\Exceptions\DataException;
    use eArc\Data\Exceptions\Interfaces\QueryExceptionInterface;
    use eArc\Data\Exceptions\QueryException;
    use eArc\Data\Manager\DataStore;
    use eArc\Data\Manager\EntitySaveStack;
    use eArc\Data\Manager\Interfaces\Events\OnFindInterface;
    use eArc\Data\ParameterInterface;
    use eArc\DI\DI;

    abstract class BootstrapEArcData
    {
        /**
         * Registers the functions `data_load`, `data_load_batch`, `data_persist`,
         * `data_persist_batch`, `data_delete`, `data_delete_batch`, `data_remove`,
         * `data_remove_batch`, `data_schedule`, `data_schedule_batch`, `data_detach`
         * and `data_find`, `data_find_entities`.
         */
        public static function init(): void
        {
            if (!function_exists('di_get')) {
                DI::init();
            }

            if (!function_exists('data_load')) {
                function data_load(string $fQCN, string $primaryKey, bool $useDataStoreOnly = false): mixed
                {
                    $result = di_get(DataStore::class)->load($fQCN, [$primaryKey], $useDataStoreOnly);

                    return array_pop($result);
                }
            }

            if (!function_exists('data_load_batch')) {
                function data_load_batch(string $fQCN, array $primaryKeys, bool $useDataStoreOnly = false): array
                {
                    return di_get(DataStore::class)->load($fQCN, $primaryKeys, $useDataStoreOnly);
                }
            }

            if (!function_exists('data_persist')) {
                function data_persist(EntityInterface|null $entity = null): void
                {
                    di_get(EntitySaveStack::class)->persist(is_null($entity) ? [] : [$entity]);
                }
            }

            if (!function_exists('data_persist_batch')) {
                function data_persist_batch(array $entities): void
                {
                    di_get(EntitySaveStack::class)->persist($entities);
                }
            }

            if (!function_exists('data_delete')) {
                function data_delete(EntityInterface $entity, bool $force = false): void
                {
                    di_get(DataStore::class)->delete([$entity], $force);
                }
            }

            if (!function_exists('data_delete_batch')) {
                function data_delete_batch(array $entities, bool $force = false): void
                {
                    di_get(DataStore::class)->delete($entities, $force);
                }
            }

            if (!function_exists('data_remove')) {
                function data_remove(string $fQCN, string $primaryKey): void
                {
                    di_get(DataStore::class)->remove($fQCN, [$primaryKey]);
                }
            }

            if (!function_exists('data_remove_batch')) {
                function data_remove_batch(string $fQCN, array $primaryKeys): void
                {
                    di_get(DataStore::class)->remove($fQCN, $primaryKeys);
                }
            }

            if (!function_exists('data_schedule')) {
                function data_schedule(EntityInterface $entity): void
                {
                    di_get(EntitySaveStack::class)->schedule($entity);
                }
            }

            if (!function_exists('data_schedule_batch')) {
                function data_schedule_batch(array $entities): void
                {
                    foreach ($entities as $entity) {
                        di_get(EntitySaveStack::class)->schedule($entity);
                    }
                }
            }

            if (!function_exists('data_detach')) {
                function data_detach(string|null $fQCN = null, array|null $primaryKeys = null): void
                {
                    di_get(DataStore::class)->detach($fQCN, $primaryKeys);
                }
            }

            if (!function_exists('data_find')) {
                /**
                 * @param string $fQCN
                 * @param array $keyValuePairs
                 *
                 * @return string[]
                 *
                 * @throws QueryExceptionInterface
                 */
                function data_find(string $fQCN, array $keyValuePairs = []): array
                {
                    foreach (di_get_tagged(ParameterInterface::TAG_ON_FIND) as $service => $args) {
                        $service = di_get($service);
                        if (!$service instanceof OnFindInterface) {
                            throw new DataException(sprintf(
                                '{18360d2b-e609-43f3-b08a-927347df7de8} Service %s tagged by the interface %s has to implement it.',
                                $service,
                                OnFindInterface::class
                            ));
                        }

                        $result = $service->onFind($fQCN, $keyValuePairs);
                        if (is_array($result)) {
                            return $result;
                        }
                    }

                    throw new QueryException('{fa2b3bb2-c6a9-4117-ae8b-57f9463a3f2d} No Service was found that could respond to the search.');
                }

                if (!function_exists('data_find_entities')) {
                    function data_find_entities(string $fQCN, array $keyValuePairs = []): array
                    {
                        return data_load_batch($fQCN, data_find($fQCN, $keyValuePairs));
                    }
                }
            }
        }
    }
}
