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

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Manager\StaticEntitySaveStack;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;
use eArc\Serializer\SerializerTypes\Interfaces\SerializerTypeInterface;

class PrimaryKeyDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $object instanceof EntityInterface && $propertyName === 'primaryKey';
    }

    public function serialize(?object $object, $propertyName, $propertyValue, SerializerTypeInterface $serializerType)
    {
        /** @var EntityInterface $object */
        if (null === $object->getPrimaryKey()) {
            di_static(StaticEntitySaveStack::class)::requirePrimaryKey($object);
        }

        return $object->getPrimaryKey();
    }

    public function isResponsibleForDeserialization(?object $object, string $type, $value): bool
    {
        return false;
    }

    public function deserialize(?object $object, string $type, $value, SerializerTypeInterface $serializerType)
    {
    }
}
