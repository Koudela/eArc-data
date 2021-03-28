<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager\Interfaces\Events;

use eArc\Data\Entity\Interfaces\EntityInterface;

interface OnPersistInterface
{
    /**
     * Will be called in order to save the data of one or more entities.
     *
     * All tagged Services are called. Thus there can be services persisting to
     * database(s), services persisting to search indices and services caching
     * the entity by shared memory, redis server or other means.
     *
     * @param EntityInterface[] $entities
     */
    public function onPersist(array $entities): void;
}
