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

use eArc\DataStore\Manager\UniqueEntityProxy;
use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;
use eArc\Serializer\Exceptions\SerializeException;

class EntityInterfaceDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $propertyValue instanceof EntityInterface;
    }

    public function serialize(?object $object, $propertyName, $propertyValue)
    {
        throw new SerializeException(sprintf(
            'A property with a direct reference to an entity is disallowed. Use an unique proxy (%s) for %s instead.',
            UniqueEntityProxy::class,
            get_class($propertyValue)
        ));
    }

    public function isResponsibleForDeserialization(?object $object, string $type, $value): bool
    {
        return is_subclass_of($type, EntityInterface::class, true);
    }

    public function deserialize(?object $object, string $type, $value)
    {
        throw new SerializeException(sprintf(
            'A property with a direct reference to an entity is disallowed. Use an unique proxy (%s) for %s instead.',
            UniqueEntityProxy::class,
            $type
        ));
    }
}
