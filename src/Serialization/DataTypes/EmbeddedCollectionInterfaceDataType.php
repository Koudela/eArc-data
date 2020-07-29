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

use eArc\DataStore\Collection\EmbeddedCollection;
use eArc\DataStore\Collection\Interfaces\EmbeddedCollectionInterface;
use eArc\DataStore\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\DataStore\Entity\Interfaces\EntityBaseInterface;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;
use eArc\Serializer\Exceptions\SerializeException;
use eArc\Serializer\Services\FactoryService;
use eArc\Serializer\Services\SerializeService;
use ReflectionClass;
use ReflectionException;

class EmbeddedCollectionInterfaceDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $propertyValue instanceof EmbeddedCollectionInterface;
    }

    public function serialize(?object $entity, $propertyName, $propertyValue)
    {
        try {
            /** @var EmbeddedCollectionInterface $propertyValue */
            $collectionReflection = new ReflectionClass($propertyValue);
            $embeddedEntitiesValue = $collectionReflection->getProperty('items')->getValue($propertyValue);
            $embeddedEntities = [];
            foreach ($embeddedEntitiesValue as $embeddedEntity) {
                $embeddedEntities[] = di_get(SerializeService::class)->getAsArray($embeddedEntity);
            }

            return [
                'type' => get_class($propertyValue),
                'value' => [
                    'fQCN' => $collectionReflection->getProperty('fQCN')->getValue($propertyValue),
                    'embeddedEntities' => $embeddedEntities,
                ]
            ];
        } catch (ReflectionException $e) {
            throw new SerializeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function isResponsibleForDeserialization(?object $object, string $type, $value): bool
    {
        return is_subclass_of($type, EmbeddedCollectionInterface::class, true);
    }

    public function deserialize(?object $object, string $type, $value)
    {
        /** @var EntityBaseInterface $object */
        $embeddedCollection = new EmbeddedCollection($object, $value['fQCN']);

        foreach ($value['embeddedEntities'] as $embeddedEntityData) {
            $embeddedEntity = di_get(FactoryService::class)->initObject($value['fQCN']);
            di_get(FactoryService::class)->attachProperties($embeddedEntity, $embeddedEntityData);
            if ($embeddedEntity instanceof EmbeddedEntityInterface) {
                $embeddedCollection->add($embeddedEntity);
            }
        }

        return $embeddedCollection;
    }
}
