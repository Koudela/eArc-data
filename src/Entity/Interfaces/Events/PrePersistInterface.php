<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces\Events;

use eArc\Data\Entity\Interfaces\EntityInterface;

interface PrePersistInterface
{
    /**
     * Will be called before persisting the entity.
     *
     * @param EntityInterface $entity
     */
    public function prePersist(EntityInterface $entity): void;
}
