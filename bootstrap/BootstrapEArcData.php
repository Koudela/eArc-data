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
    use eArc\Data\Manager\DataStore;
    use eArc\Data\Manager\EntitySaveStack;
    use eArc\DI\DI;

    abstract class BootstrapEArcData
    {
        /**
         * Registers the functions `data_load`, `data_persist`, `data_delete`,
         * `data_remove`, `data_schedule` and `data_detach`.
         */
        public static function init(): void
        {
            if (!function_exists('di_get')) {
                DI::init();
            }

            if (!function_exists('data_load')) {
                function data_load(string $fQCN, string $primaryKey): EntityInterface
                {
                    return di_get(DataStore::class)->load($fQCN, $primaryKey);
                }
            }

            if (!function_exists('data_persist')) {
                function data_persist(EntityInterface|null $entity): void
                {
                    di_get(EntitySaveStack::class)->persist($entity);
                }
            }

            if (!function_exists('data_delete')) {
                function data_delete(EntityInterface $entity): void
                {
                    di_static(DataStore::class)->delete($entity);
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
                function data_detach(string $fQCN, ?array $primaryKeys = null): void
                {
                    di_get(DataStore::class)->detach($fQCN, $primaryKeys);
                }
            }
        }
    }
}
