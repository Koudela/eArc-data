<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity;

use eArc\Data\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\Data\Entity\Interfaces\EntityInterface;

trait EmbeddedEntityTrait
{
    protected EntityInterface|EmbeddedEntityInterface|null $ownerEntity;

    public function getRootEntity(): EntityInterface|null
    {
        $entity = $this->getOwnerEntity();

        while ($entity instanceof EmbeddedEntityInterface) {
            $entity = $entity->getOwnerEntity();
        }

        return $entity;
    }

    public function getOwnerEntity(): EntityInterface|EmbeddedEntityInterface|null
    {
        return $this->ownerEntity;
    }

    public function setOwnerEntity(EntityInterface|EmbeddedEntityInterface|null $ownerEntity)
    {
        $this->ownerEntity = $ownerEntity;
    }
}
