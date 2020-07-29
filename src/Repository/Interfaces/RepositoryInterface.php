<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Repository\Interfaces;

use eArc\DataStore\Exceptions\Interfaces\QueryExceptionInterface;

interface RepositoryInterface extends RepositoryBaseInterface
{
    /**
     * Get the primary keys for the query based on the entities in the
     * repository. If the query is null the primary keys for all entities in the
     * repository are returned.
     *
     * @param string|null $query
     *
     * @return string[]
     *
     * @throws QueryExceptionInterface
     */
    public function find(?string $query = null): array;
}
