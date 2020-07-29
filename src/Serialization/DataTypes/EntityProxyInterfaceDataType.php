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

use eArc\DataStore\Entity\Interfaces\Cascade\CascadePersistInterface;
use eArc\DataStore\Manager\DataStore;
use eArc\DataStore\Manager\Interfaces\EntityProxyInterface;
use eArc\DataStore\Manager\StaticEntitySaveStack;
use eArc\DataStore\Manager\UniqueEntityProxy;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;
use eArc\Serializer\Exceptions\SerializeException;

class EntityProxyInterfaceDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $propertyValue instanceof EntityProxyInterface;
    }

    public function serialize(?object $object, $propertyName, $propertyValue)
    {
        /** @var EntityProxyInterface $propertyValue */
        if (null === $propertyValue->getPrimaryKey()) {
            $entity = $propertyValue->load();
            di_static(StaticEntitySaveStack::class)::requirePrimaryKey($entity);
        }

        if ($object instanceof CascadePersistInterface) {
            if (array_key_exists($propertyName, $object::getCascadeOnPersistProperties())) {
                if (di_get(DataStore::class)->isLoaded($propertyValue->getEntityName(), $propertyValue->getPrimaryKey())) {
                    di_static(StaticEntitySaveStack::class)::addToStack($propertyValue->load());
                }
            }
        }

        return [
            'type' => get_class($propertyValue),
            'value' => [
                $propertyValue->getEntityName(),
                $propertyValue->getPrimaryKey(),
            ],
        ];
    }

    public function isResponsibleForDeserialization(?object $object, string $type, $value): bool
    {
        return is_subclass_of($type, EntityProxyInterface::class, true);
    }

    public function deserialize(?object $object, string $type, $value)
    {
        if ($type !== UniqueEntityProxy::class) {
            throw new SerializeException(sprintf(
                'Unknown entity proxy (%s). Replace EntityProxyInterfaceDataType.',
                $type
            ));
        }

        $fQCN = $value[0];
        $primaryKey = $value[1];

        return UniqueEntityProxy::getInstance($primaryKey, $fQCN);
    }
}
