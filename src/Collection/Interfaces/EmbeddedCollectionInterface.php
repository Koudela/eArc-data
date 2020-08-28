<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Collection\Interfaces;

use eArc\Data\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\Data\Exceptions\Interfaces\HomogeneityExceptionInterface;
use eArc\Data\Repository\Interfaces\EmbeddedRepositoryInterface;

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
