<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Serialization\DataTypes;

use eArc\DataStore\Collection\Collection;
use eArc\DataStore\Collection\Interfaces\CollectionInterface;
use eArc\DataStore\Entity\Interfaces\EntityBaseInterface;
use eArc\DataStore\Entity\Interfaces\Cascade\CascadePersistInterface;
use eArc\DataStore\Manager\DataStore;
use eArc\DataStore\Manager\Interfaces\EntityProxyInterface;
use eArc\DataStore\Manager\StaticEntitySaveStack;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;
use eArc\Serializer\Exceptions\SerializeException;
use ReflectionClass;
use ReflectionException;

class CollectionInterfaceDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $propertyValue instanceof CollectionInterface;
    }

    public function serialize(?object $object, $propertyName, $propertyValue)
    {
        try {
            /** @var CollectionInterface $propertyValue */
            $collectionReflection = new ReflectionClass($propertyValue);
            $items = $collectionReflection->getProperty('items')->getValue($propertyValue);
            /** @var EntityProxyInterface $item */
            foreach ($items as $item) {
                if (null === $item->getPrimaryKey()) {
                    di_static(StaticEntitySaveStack::class)::requirePrimaryKey($item->load());
                }
            }

            if ($object instanceof CascadePersistInterface) {
                if (array_key_exists($propertyName, $object::getCascadeOnPersistProperties())) {
                    foreach ($items as $item) {
                        if (di_get(DataStore::class)->isLoaded($item->getEntityName(), $item->getPrimaryKey())) {
                            di_static(StaticEntitySaveStack::class)::addToStack($item->load());
                        }
                    }
                }
            }

            if ($propertyValue instanceof CollectionInterface) {
                $entityArray[$propertyName] = [
                    'interface' => CollectionInterface::class,
                    'fQCN' => $propertyValue->getEntityName(),
                    'primaryKeys' => $propertyValue->find(),
                ];
            }
        } catch (ReflectionException $e) {
            throw new SerializeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function isResponsibleForDeserialization(?object $object, string $type, $value): bool
    {
        return is_subclass_of($type, CollectionInterface::class, true);
    }

    public function deserialize(?object $object, string $type, $value)
    {
        /** @var EntityBaseInterface $object */
        $collection = new Collection($object, $value['fQCN']);
        foreach ($value['primaryKeys'] as $primaryKey) {
            $collection->add($primaryKey);
        }

        return $collection;
    }
}
