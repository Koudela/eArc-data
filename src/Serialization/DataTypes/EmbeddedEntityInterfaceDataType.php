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

use eArc\DataStore\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\Serializer\DataTypes\Interfaces\DataTypeInterface;
use eArc\Serializer\Services\FactoryService;
use eArc\Serializer\Services\SerializeService;

class EmbeddedEntityInterfaceDataType implements DataTypeInterface
{
    public function isResponsibleForSerialization(?object $object, $propertyName, $propertyValue): bool
    {
        return $propertyValue instanceof EmbeddedEntityInterface;
    }

    public function serialize(?object $object, $propertyName, $propertyValue)
    {
        /** @var EmbeddedEntityInterface $propertyValue */
        return [
            'type' => get_class($propertyValue),
            'value' => di_get(SerializeService::class)->getAsArray($propertyValue),
        ];
    }

    public function isResponsibleForDeserialization(?object $object, string $type, $value): bool
    {
        return is_subclass_of($type, EmbeddedEntityInterface::class, true);
    }

    public function deserialize(?object $object, string $type, $value)
    {
        $object = di_get(FactoryService::class)->initObject($type);

        return di_get(FactoryService::class)->attachProperties($object, $value);
    }
}
