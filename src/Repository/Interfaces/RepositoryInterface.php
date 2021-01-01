<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Repository\Interfaces;

use eArc\QueryLanguage\Exception\QueryException;

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
     * @throws QueryException
     */
    public function find(?string $query = null): array;
}
