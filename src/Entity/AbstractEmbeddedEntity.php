<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity;

use eArc\Data\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\Data\Entity\Interfaces\EntityBaseInterface;
use eArc\Data\Entity\Interfaces\EntityInterface;

abstract class AbstractEmbeddedEntity implements EmbeddedEntityInterface
{
    /** @var EntityBaseInterface */
    protected $ownerEntity;

    public function getRootEntity(): EntityInterface
    {
        $entity = $this->getOwnerEntity();

        while ($entity instanceof EmbeddedEntityInterface) {
            $entity = $entity->getOwnerEntity();
        }

        return $entity;
    }

    public function getOwnerEntity(): EntityBaseInterface
    {
        return $this->ownerEntity;
    }
 }
