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
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;

interface DataStoreInterface
{
    /**
     * Get the entity object the fully qualified class name and the primary key
     * relates to. If the useDataStoreOnly flag is set and the entity is not loaded
     * already null is returned.
     *
     * @param string $fQCN
     * @param string $primaryKey
     * @param bool $useDataStoreOnly
     *
     * @return EntityInterface
     *
     * @throws NoDataExceptionInterface|SerializeExceptionInterface
     */
    public function load(string $fQCN, string $primaryKey, bool $useDataStoreOnly = false): EntityInterface|null;

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
     * Clears the entity from the data store cache for the `load` and `isLoaded` methods.
     *
     * @param string|null $fQCN
     * @param string[]|null $primaryKeys
     */
    public function detach(string|null $fQCN = null, array|null $primaryKeys = null): void;

    /**
     * Deletes the persisted data the entity relates to and clears the entity from
     * data store cache. To delete immutable entities the force parameter has to
     * been set to true.
     *
     * @param EntityInterface $entity
     * @param bool $force
     */
    public function delete(EntityInterface $entity, bool $force = false): void;

    /**
     * Deletes the persisted data the fully qualified class name and the primary
     * key relates to. If present in the data store cache it also purges the entity
     * from it. To delete immutable entities the force parameter has to been set
     * to true.
     *
     * @param string $fQCN
     * @param string $primaryKey
     */
    public function remove(string $fQCN, string $primaryKey, bool $force = false): void;
}
