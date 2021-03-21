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
use eArc\Data\Repository\Interfaces\RepositoryBaseInterface;

interface CollectionInterface extends RepositoryBaseInterface, CollectionBaseInterface
{
    /**
     * Add a member to the collection via its primary key. If the related entity
     * is a member already, this method fails silently.
     *
     * @param string $primaryKey
     *
     * @return $this
     */
    public function add(string $primaryKey): self;

    /**
     * Add the entity as a member to the collection. If the entity is a member
     * already, this method fails silently.
     *
     * @param EntityInterface $entity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function addEntity(EntityInterface $entity): self;

    /**
     * Removes a member from the collection via its primary key. If there is
     * no member with this primary key, this method fails silently.
     *
     * @param string $primaryKey
     *
     * @return $this
     */
    public function remove(string $primaryKey): self;

    /**
     * Removes the entity from the collection. If the entity is not member of the
     * collection, this method fails silently.
     *
     * @param EntityInterface $entity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function removeEntity(EntityInterface $entity): self;

    /**
     * Checks whether a primary key points at a member of the collection.
     *
     * @param string $primaryKey
     *
     * @return bool
     */
    public function has(string $primaryKey): bool;

    /**
     * Checks whether a entity is a member of the collection.
     *
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function hasEntity(EntityInterface $entity): bool;

    /**
     * Returns the primary keys of all members.
     *
     * @return string[]
     */
    public function getPrimaryKeys(): array;
}
