<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager\Interfaces;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Exceptions\Interfaces\NoDataExceptionInterface;

interface DataStoreInterface
{
    const LOAD_FLAG_USE_FIRST_LEVEL_CACHE_ONLY = 1;
    const LOAD_FLAG_SKIP_FIRST_LEVEL_CACHE = 2;

    /**
     * Get the entity objects the fully qualified class name and the primary keys
     * relate to. If the `LOAD_FLAG_USE_FIRST_LEVEL_CACHE_ONLY` flag is used only
     * entities loaded already are returned. The
     * `LOAD_FLAG_LOAD_FLAG_SKIP_FIRST_LEVEL_CACHE` does not look up the already
     * loaded entities and does not add the loaded entities to the already loaded
     * ones.
     *
     * @param string $fQCN
     * @param string[] $primaryKeys
     * @param int $flag
     *
     * @return EntityInterface[]
     *
     * @throws NoDataExceptionInterface
     */
    public function load(string $fQCN, array $primaryKeys, int $flag = 0): array;

    /**
     * Checks whether the entity object the fully qualified class name and
     * the primary key relates to is loaded.
     *
     * @param string $fQCN
     * @param string $primaryKey
     *
     * @return bool
     */
    public function isLoaded(string $fQCN, string $primaryKey): bool;

    /**
     * Adds an entity to the data store.
     *
     * @param EntityInterface $entity
     */
    public function attach(EntityInterface $entity): void;

    /**
     * Clears the entity from the data store cache for the `load` and `isLoaded` methods.
     *
     * @param string|null $fQCN
     * @param string[]|null $primaryKeys
     */
    public function detach(string|null $fQCN = null, array|null $primaryKeys = null): void;

    /**
     * Deletes the persisted data the entities relate to and clears the entities from
     * data store cache. To delete immutable entities the force parameter has to
     * been set to true.
     *
     * @param EntityInterface[] $entities
     * @param bool $force
     */
    public function delete(array $entities, bool $force = false): void;

    /**
     * Deletes the persisted data the fully qualified class name and the primary
     * keys relate to. If present in the data store cache it also purges the entity
     * from it. To delete immutable entities the force parameter has to been set
     * to true.
     *
     * @param string $fQCN
     * @param string[] $primaryKeys
     * @param bool $force
     */
    public function remove(string $fQCN, array $primaryKeys, bool $force = false): void;
}
