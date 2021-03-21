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
         * Registers the functions `data_load`, `data_persist`, `data_delete`,
         * `data_remove`, `data_schedule`, `data_detach` and `data_find`.
         */
        public static function init(): void
        {
            if (!function_exists('di_get')) {
                DI::init();
            }

            if (!function_exists('data_load')) {
                function data_load(string $fQCN, string $primaryKey, bool $useDataStoreOnly = false): null|EntityInterface
                {
                    return di_get(DataStore::class)->load($fQCN, $primaryKey, $useDataStoreOnly);
                }
            }

            if (!function_exists('data_persist')) {
                function data_persist(EntityInterface|null $entity = null): string|null
                {
                    return di_get(EntitySaveStack::class)->persist($entity);
                }
            }

            if (!function_exists('data_delete')) {
                function data_delete(EntityInterface $entity, bool $force = false): void
                {
                    di_static(DataStore::class)->delete($entity, $force);
                }
            }

            if (!function_exists('data_remove')) {
                function data_remove(string $fQCN, string $primaryKey): void
                {
                    di_get(DataStore::class)->remove($fQCN, $primaryKey);
                }
            }

            if (!function_exists('data_schedule')) {
                function data_schedule(EntityInterface $entity): void
                {
                    di_get(EntitySaveStack::class)->schedule($entity);
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
                 * Returns the primary keys for the key value pairs based on the
                 * properties of the entities from the class. If the key value
                 * pairs are empty all primary keys are returned. Key value pairs
                 * are joint via logic `AND`. Value arrays are interpreted as `IN`.
                 * Not all key value pairs or value arrays may be supported. It
                 * depends on the used infrastructure, the setting (for example
                 * the usable sql indices) and the implementation of the bridge.
                 * If one or more key value pairs are not supported a query
                 * exception is thrown.
                 *
                 * Beside this function there may be more ways to search for entities.
                 * These are not part of the earc/data abstraction.
                 *
                 * @param string $fQCN
                 * @param string[] $keyValuePairs
                 *
                 * @return string[]
                 *
                 * @throws QueryExceptionInterface
                 */
                function data_find(string $fQCN, array $keyValuePairs = []): array
                {
                    foreach (di_get_tagged(ParameterInterface::TAG_ON_FIND) as $service) {
                        $service = di_get($service);
                        if (!$service instanceof OnFindInterface) {
                            throw new DataException(sprintf(
                                '{18360d2b-e609-43f3-b08a-927347df7de8} Services tagged by the %s have to implement it.',
                                OnFindInterface::class
                            ));
                        }
                        foreach ($service->getOnFindCallables() as $callable) {
                            $result = $callable->findBy($fQCN, $keyValuePairs);

                            if (is_array($result)) {
                                return $result;
                            }
                        }
                    }

                    throw new QueryException('{fa2b3bb2-c6a9-4117-ae8b-57f9463a3f2d} No Service was found that could respond to the search.');
                }
            }
        }
    }
}
