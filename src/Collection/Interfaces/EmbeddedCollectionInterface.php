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

use eArc\Data\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\Data\Exceptions\Interfaces\HomogeneityExceptionInterface;
use eArc\Data\Repository\Interfaces\EmbeddedRepositoryInterface;

interface EmbeddedCollectionInterface extends EmbeddedRepositoryInterface, CollectionBaseInterface
{
    /**
     * Adds an embedded entity to the members of the collection. If the embedded
     * entity is already a member this method fails silently.
     *
     * @param EmbeddedEntityInterface $embeddedEntity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function add(EmbeddedEntityInterface $embeddedEntity): self;

    /**
     * Removes an embedded entity from the members of the collection. If the embedded
     * entity is not a member of the collection, this method fails silently.
     *
     * @param EmbeddedEntityInterface $embeddedEntity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function remove(EmbeddedEntityInterface $embeddedEntity): self;

    /**
     * Checks whether an embedded entity is member of the collection.
     *
     * @param EmbeddedEntityInterface $embeddedEntity
     *
     * @return bool
     */
    public function has(EmbeddedEntityInterface $embeddedEntity): bool;

}
