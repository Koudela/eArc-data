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

/**
 * A Repository is always related to a entity class
 */
interface RepositoryBaseInterface
{
    /**
     * Get the fully qualified class name of the entity the repository is
     * related to.
     */
    public function getEntityName(): string;
}
