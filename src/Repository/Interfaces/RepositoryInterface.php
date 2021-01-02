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

use eArc\QueryLanguage\Collector\QueryInitializerExtended;

interface RepositoryInterface extends RepositoryBaseInterface
{
    /**
     * Get the primary keys for key value pairs based on the entities in the
     * repository. If the key value pairs are empty the primary keys for all
     * entities in the repository are returned.
     *
     * @param string[] $keyValuePairs
     *
     * @return string[]
     */
    public function findBy(array $keyValuePairs = []): iterable;

    /**
     * Get the query builder based on the repository.
     *
     * @return QueryInitializerExtended
     */
    public function getQueryBuilder(): QueryInitializerExtended;
}
