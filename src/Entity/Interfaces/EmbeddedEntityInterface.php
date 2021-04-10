<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces;

interface EmbeddedEntityInterface extends EntityBaseInterface
{
    /**
     * @return EntityInterface|null The entity that is reverenced in the database by
     * a primary key and embeds the embedded entity in its property tree. `null`
     * if there is none.
     */
    public function getRootEntity(): EntityInterface|null;

    /**
     * @return EntityInterface|EmbeddedEntityInterface|null The entity that has this
     * embedded entity as one of its properties. `null` if it is not embedded yet.
     */
    public function getOwnerEntity(): EntityInterface|EmbeddedEntityInterface|null;

    /**
     * @param $ownerEntity EntityInterface|EmbeddedEntityInterface|null The entity that has this
     * embedded entity as one of its properties.
     */
    public function setOwnerEntity(EntityInterface|EmbeddedEntityInterface|null $ownerEntity);
}
