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

use eArc\DataStore\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\DataStore\Exceptions\Interfaces\HomogeneityExceptionInterface;
use eArc\DataStore\Repository\Interfaces\EmbeddedRepositoryInterface;

interface EmbeddedCollectionInterface extends EmbeddedRepositoryInterface, CollectionBaseInterface
{
    /**
     * @param EmbeddedEntityInterface $embeddedEntity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function add(EmbeddedEntityInterface $embeddedEntity): self;

    /**
     * @param EmbeddedEntityInterface $embeddedEntity
     *
     * @return $this
     *
     * @throws HomogeneityExceptionInterface
     */
    public function remove(EmbeddedEntityInterface $embeddedEntity): self;

    /**
     * @param EmbeddedEntityInterface $embeddedEntity
     *
     * @return bool
     */
    public function has(EmbeddedEntityInterface $embeddedEntity): bool;

}
