<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Query\Interfaces;

interface QueryServiceInterface
{
    /**
     * Get the primary keys for the query based on the fully qualified class
     * name. If the query is null the primary keys for all entities represented
     * by the fully qualified class name are returned.
     *
     * @param string $fQCN
     * @param string|null $query
     *
     * @return string[]
     */
    public function find(string $fQCN, ?string $query = null): array;

    /**
     * Get the primary keys for the key value pairs based on the fully qualified
     * class name. If the key value pairs are empty the primary keys for all
     * entities represented by the fully qualified class name are returned.
     *
     * @param string $fQCN
     * @param array $keyValuePairs
     *
     * @return string[]
     */
    public function findBy(string $fQCN, array $keyValuePairs): array;
}
