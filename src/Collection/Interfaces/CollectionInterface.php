<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Collection\Interfaces;

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\DataStore\Exceptions\Interfaces\HomogeneityExceptionInterface;
use eArc\DataStore\Repository\Interfaces\RepositoryInterface;

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
