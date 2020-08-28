<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Repository\Interfaces;

use eArc\Data\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\QueryLanguage\Exception\QueryException;

interface EmbeddedRepositoryInterface extends RepositoryBaseInterface
{
    /**
     * Get the embedded entities for the key value pairs based on the entities
     * in the repository. If the key value pairs are empty all the embedded
     * entities from the repository are returned.
     *
     * @param string[] $keyValuePairs
     *
     * @return EmbeddedEntityInterface[]
     *
     * @throws QueryException
     */
    public function findBy(array $keyValuePairs): array;
}
