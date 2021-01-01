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
use eArc\Data\Repository\Interfaces\RepositoryInterface;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;

interface DataStoreInterface
{
    /**
     * Get the entity object the fully qualified class name and the primary key
     * relates to.
     *
     * @param string $fQCN
     * @param string $primaryKey
     *
     * @return EntityInterface
     *
     * @throws NoDataExceptionInterface|SerializeExceptionInterface
     */
    public function load(string $fQCN, string $primaryKey): EntityInterface;

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
     * Get the repository the fully qualified class name relates to.
     *
     * @param string $fQCN
     *
     * @return RepositoryInterface
     */
    public function getRepository(string $fQCN): RepositoryInterface;

    /**
     * Registers the functions `data_load`, `data_save`, `data_delete`,
     * `data_remove` and `data_find`.
     */
    public function init(): void;

    /**
     * Clears the entity cache for the `load` and `isLoaded` methods.
     *
     * @param string|null $fQCN
     * @param string|null $primaryKey
     */
    public function reset(?string $fQCN = null, ?string $primaryKey = null): void;
}
