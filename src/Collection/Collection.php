<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Collection;

use eArc\DataStore\Collection\Interfaces\CollectionInterface;
use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\DataStore\Manager\UniqueEntityProxy;
use eArc\DataStore\Exceptions\HomogeneityException;
use function eArc\DataStore\Manager\data_find;

class Collection extends AbstractBaseCollection implements CollectionInterface
{
    public function find(?string $query = null): array
    {
        $allPrimaryKeys = [];

        /** @var UniqueEntityProxy $item */
        foreach ($this->items as $item) {
            // TODO: better solution
            if (null !== $item->getPrimaryKey()) {
                $allPrimaryKeys[$item->getPrimaryKey()] = $item->getPrimaryKey();
            }

        }

        return data_find($this->fQCN, $query, $allPrimaryKeys);
    }

    public function add(string $primaryKey): CollectionInterface
    {
        $uniqueEntityProxy = UniqueEntityProxy::getInstance($primaryKey, $this->fQCN);

        $this->items[spl_object_id($uniqueEntityProxy)] = $uniqueEntityProxy;

        return $this;
    }

    public function addEntity(EntityInterface $entity): CollectionInterface
    {
        if ($this->fQCN !== get_class($entity)) {
            throw new HomogeneityException(sprintf(
                'Entity of class %s cannot be added to a collection of type %s.',
                get_class($entity),
                $this->fQCN
            ));
        }

        $uniqueEntityProxy = UniqueEntityProxy::getInstance($entity, $this->fQCN);

        $this->items[spl_object_id($uniqueEntityProxy)] = $uniqueEntityProxy;

        return $this;
    }

    public function remove(string $primaryKey): CollectionInterface
    {
        $uniqueEntityProxy = UniqueEntityProxy::getInstance($primaryKey, $this->fQCN);

        unset($this->items[spl_object_id($uniqueEntityProxy)]);

        return $this;
    }

    public function removeEntity(EntityInterface $entity): CollectionInterface
    {
        if ($this->fQCN !== get_class($entity)) {
            throw new HomogeneityException(sprintf(
                'Entity of class %s cannot be removed from a collection of type %s.',
                get_class($entity),
                $this->fQCN
            ));
        }

        $uniqueEntityProxy = UniqueEntityProxy::getInstance($entity, $this->fQCN);

        unset($this->items[spl_object_id($uniqueEntityProxy)]);

        return $this;
    }

    public function has(string $primaryKey): bool
    {
        $uniqueEntityProxy = UniqueEntityProxy::getInstance($primaryKey, $this->fQCN);

        return array_key_exists(spl_object_id($uniqueEntityProxy), $this->items);
    }

    public function hasEntity(EntityInterface $entity): bool
    {
        $uniqueEntityProxy = UniqueEntityProxy::getInstance($entity, $this->fQCN);

        return array_key_exists(spl_object_id($uniqueEntityProxy), $this->items);
    }
}
