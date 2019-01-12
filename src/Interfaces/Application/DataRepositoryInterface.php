<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/earc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Interfaces\Application;

use eArc\Data\Interfaces\Exceptions\NoDataExceptionInterface;
use eArc\Data\Interfaces\Exceptions\DataExistsExceptionInterface;

/**
 * Data repository interface.
 */
interface DataRepositoryInterface
{
    /**
     * Get the object related by the identifier to the persisted data.
     *
     * @param string $identifier
     *
     * @return DataInterface
     *
     * @throws NoDataExceptionInterface
     */
    public function find(string $identifier): DataInterface;

    /**
     * Get all objects persisted data exists.
     *
     * @return DataInterface[]
     */
    public function findAll(): array;

    /**
     * Create a data object. An identifier may be supplied for the object/data.
     *
     * @param string $identifier
     *
     * @return DataInterface
     *
     * @throws DataExistsExceptionInterface
     */
    public function create(?string $identifier = null): DataInterface;

    /**
     * Update the persisted data the object belongs to.
     *
     * @param DataInterface $data
     *
     * @throws NoDataExceptionInterface
     */
    public function update(DataInterface $data): void;

    /**
     * Delete the persisted data the identifier relates to.
     *
     * @param string $identifier
     */
    public function delete(string $identifier): void;

    /**
     * Update the persisted data the objects belong to.
     *
     * @param DataInterface[] $dataObjects
     *
     * @throws NoDataExceptionInterface
     */
    public function batchUpdate(array $dataObjects): void;

    /**
     * Delete the persisted data the identifiers relate to.
     *
     * @param string[] $identifiers
     */
    public function batchDelete(array $identifiers): void;

}
