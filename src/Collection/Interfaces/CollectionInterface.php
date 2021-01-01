<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Collection\Interfaces;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Exceptions\Interfaces\HomogeneityExceptionInterface;
use eArc\Data\Repository\Interfaces\RepositoryInterface;

interface CollectionInterface extends RepositoryInterface, CollectionBaseInterface
{
    /**
     * @param string $primaryKey
     *
     * @return $this
     */
    public function add(string $primaryKey): self;

    /**
     * @param EntityInterface $entity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function addEntity(EntityInterface $entity): self;

    /**
     * @param string $primaryKey
     *
     * @return $this
     */
    public function remove(string $primaryKey): self;

    /**
     * @param EntityInterface $entity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function removeEntity(EntityInterface $entity): self;

    /**
     * @param string $primaryKey
     *
     * @return bool
     */
    public function has(string $primaryKey): bool;

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function hasEntity(EntityInterface $entity): bool;
}
