<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Serialization\DataTypes;

use eArc\Data\Collection\Collection;
use eArc\Data\Collection\Interfaces\CollectionInterface;
use eArc\Data\Entity\Interfaces\EntityBaseInterface;
use eArc\Data\Entity\Interfaces\Events\OnPersistInterface;
use eArc\Data\Manager\DataStore;
use eArc\Data\Manager\StaticEntitySaveStack;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;
use eArc\Serializer\Exceptions\SerializeException;
use eArc\Serializer\SerializerTypes\Interfaces\SerializerTypeInterface;
use ReflectionClass;
use ReflectionException;
use function eArc\Data\Manager\data_load;

class CollectionInterfaceDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $propertyValue instanceof CollectionInterface;
    }

    public function serialize(?object $object, $propertyName, $propertyValue, SerializerTypeInterface $serializerType)
    {
        try {
            /** @var CollectionInterface $propertyValue */
            $collectionReflection = new ReflectionClass($propertyValue);
            $items = $collectionReflection->getProperty('items')->getValue($propertyValue);

            if ($propertyValue instanceof CollectionInterface) {
                $entityArray[$propertyName] = [
                    'interface' => CollectionInterface::class,
                    'fQCN' => $propertyValue->getEntityName(),
                    'primaryKeys' => $items,
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

    public function deserialize(?object $object, string $type, $value, SerializerTypeInterface $serializerType): Collection
    {
        /** @var EntityBaseInterface $object */
        $collection = new Collection($object, $value['fQCN']);

        foreach ($value['primaryKeys'] as $primaryKey) {
            $collection->add($primaryKey);
        }

        return $collection;
    }
}
