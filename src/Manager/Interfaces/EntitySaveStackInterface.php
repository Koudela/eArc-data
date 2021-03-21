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

interface EntitySaveStackInterface
{
    /**
     * Schedules entities to save them later.
     *
     * @param EntityInterface $entity
     */
    public function schedule(EntityInterface $entity): void;

    /**
     * Creates/updates the persisted data the entity relates to. May create
     * the primary key if the entity was not persisted yet.
     *
     * If entities are scheduled they will be saved first.
     *
     * Can be called with the null argument to save the scheduled entities only.
     *
     * @param EntityInterface|null $entity
     */
    public function persist(EntityInterface|null $entity): void;
}
