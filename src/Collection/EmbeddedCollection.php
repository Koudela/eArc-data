<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Collection;

use eArc\Data\Collection\Interfaces\EmbeddedCollectionInterface;
use eArc\Data\Entity\Interfaces\EmbeddedEntityInterface;
use eArc\Data\Exceptions\HomogeneityException;
use eArc\Data\Exceptions\QueryException;
use ReflectionClass;
use ReflectionException;

class EmbeddedCollection extends AbstractBaseCollection implements EmbeddedCollectionInterface
{
    public function findBy(array $keyValuePairs): array
    {
        try {
            $reflectionProperties = (new ReflectionClass($this->getEntityName()))->getProperties();
        } catch (ReflectionException $e) {
            throw new QueryException('{ab89a36b-b06e-4946-91af-409115b837dc} '.$e->getMessage(), $e->getCode(), $e);
        }
        $indexedProperties = [];

        foreach ($reflectionProperties as $reflectionProperty) {
            $indexedProperties[$reflectionProperty->getName()] = $reflectionProperty;
            $reflectionProperty->setAccessible(true);
        }

        $items = $this->items;

        foreach ($keyValuePairs as $key => $value) {
            if (!array_key_exists($key, $indexedProperties)) {
                throw new QueryException(sprintf('{56c6e168-8dd4-46b6-b3ee-096155473f50} %s is not a property of %s.', $key, $this->fQCN));
            }
            if (is_array($value)) {
                foreach ($items as $itemKey => $item) {
                    if (!in_array($indexedProperties[$key]->getValue($item), $value)) {
                        unset($items[$itemKey]);
                    }
                }
            } else {
                foreach ($items as $itemKey => $item) {
                    if ($indexedProperties[$key]->getValue($item) !== $value) {
                        unset($items[$itemKey]);
                    }
                }
            }
        }

        return $items;
    }

    public function add(EmbeddedEntityInterface $embeddedEntity): EmbeddedCollectionInterface
    {
        if ($this->fQCN !== $embeddedEntity::class) {
            throw new HomogeneityException(sprintf(
                '{4ba1156d-a0aa-48fa-906b-4a30d5d003a3} Embedded entity of type %s cannot be added to a embedded collection of type %s.',
                $embeddedEntity::class,
                $this->fQCN
            ));
        }

        $this->items[spl_object_id($embeddedEntity)] = $embeddedEntity;

        $embeddedEntity->setOwnerEntity($this);

        return $this;
    }

    public function remove(EmbeddedEntityInterface $embeddedEntity): EmbeddedCollectionInterface
    {
        if ($this->fQCN !== get_class($embeddedEntity)) {
            throw new HomogeneityException(sprintf(
                '{1b761414-78c1-48e3-be5f-107b45b5bc86} Embedded entity of type %s cannot be removed from a embedded collection of type %s.',
                get_class($embeddedEntity),
                $this->fQCN
            ));
        }

        unset($this->items[spl_object_id($embeddedEntity)]);

        $embeddedEntity->setOwnerEntity(null);

        return $this;
    }

    public function has(EmbeddedEntityInterface $embeddedEntity): bool
    {
        return key_exists(spl_object_id($embeddedEntity), $this->items);
    }
}
