<?php declare(strict_types=1);
/**
* e-Arc Framework - the explicit Architecture Framework
*
* @package earc/data
* @link https://github.com/Koudela/eArc-data/
* @copyright Copyright (c) 2019-2021 Thomas Koudela
* @license http://opensource.org/licenses/MIT MIT License
*/

namespace eArc\Data\IndexHandling;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Index\IsIndexedInterface;
use eArc\Data\IndexHandling\Interfaces\IndexInterface;
use eArc\Serializer\Exceptions\SerializeException;
use ReflectionClass;
use ReflectionException;

class IndexEventHandler
{
    /**
     * This method is called every time before(!) an entity is saved. The
     * ordering matters, since a unique index violation has to be detected
     * before the entity is saved.
     *
     * @param EntityInterface $entity
     *
     * @throws SerializeException|ReflectionException
     */
    public static function beforeEntitySaved(EntityInterface $entity): void
    {
        if ($entity instanceof IsIndexedInterface) {
            $reflectionEntity = new ReflectionClass($entity);
            foreach ($entity::getIndexedProperties() as $propertyName => $type) {
                $property = $reflectionEntity->getProperty($propertyName);
                $property->setAccessible(true);
                $value = (string) $property->getValue($entity);
                di_get(IndexInterface::class)::updateIndex($type, $entity, $propertyName, $value);
            }
        }
    }

    /**
     * This method is called every time an entity is removed.
     *
     * @param EntityInterface $entity
     *
     * @throws SerializeException
     */
    public static function afterEntityRemoved(EntityInterface $entity): void
    {
        if ($entity instanceof IsIndexedInterface) {
            foreach ($entity::getIndexedProperties() as $propertyName => $type) {
                di_get(IndexInterface::class)->updateIndex($type, $entity, $propertyName, null);
            }
        }
    }
}
