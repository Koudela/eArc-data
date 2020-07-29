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

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\DataStore\Manager\StaticEntitySaveStack;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;

class PrimaryKeyDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $object instanceof EntityInterface && $propertyName === 'primaryKey';
    }

    public function serialize(?object $object, $propertyName, $propertyValue)
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

    public function deserialize(?object $object, string $type, $value)
    {
    }
}
