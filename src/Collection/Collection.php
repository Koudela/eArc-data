<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Collection;

use eArc\Data\Collection\Interfaces\CollectionInterface;
use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Exceptions\HomogeneityException;

class Collection extends AbstractBaseCollection implements CollectionInterface
{
    public function asArray(): array
    {
        $entities = [];

        foreach ($this->items as $primaryKey) {
            $entities[$primaryKey] = data_load($this->fQCN, $primaryKey);
        }

        return $entities;
    }

    public function add(string $primaryKey): CollectionInterface
    {
        $this->items[$primaryKey] = $primaryKey;

        return $this;
    }

    public function addEntity(EntityInterface $entity): CollectionInterface
    {
        if ($this->fQCN !== get_class($entity)) {
            throw new HomogeneityException(sprintf(
                '{64cf0af1-b856-4bb7-8eb0-bb516cee12c6} Entity of class %s cannot be added to a collection of type %s.',
                get_class($entity),
                $this->fQCN
            ));
        }

        return $this->add($entity->getPrimaryKey());
    }

    public function remove(string $primaryKey): CollectionInterface
    {
        unset($this->items[$primaryKey]);

        return $this;
    }

    public function removeEntity(EntityInterface $entity): CollectionInterface
    {
        if ($this->fQCN !== get_class($entity)) {
            throw new HomogeneityException(sprintf(
                '{4fae51e4-aefd-4f54-a120-58db244f8138} Entity of class %s cannot be removed from a collection of type %s.',
                get_class($entity),
                $this->fQCN
            ));
        }

        return $this->remove($entity->getPrimaryKey());
    }

    public function has(string $primaryKey): bool
    {
        return array_key_exists($primaryKey, $this->items);
    }

    public function hasEntity(EntityInterface $entity): bool
    {
        return $this->has($entity->getPrimaryKey());
    }

    public function getPrimaryKeys(): array
    {
        return array_keys($this->items);
    }
}
