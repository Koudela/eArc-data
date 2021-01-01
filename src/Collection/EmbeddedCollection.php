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
use eArc\QueryLanguage\Exception\QueryException;
use ReflectionClass;
use ReflectionException;

class EmbeddedCollection extends AbstractBaseCollection implements EmbeddedCollectionInterface
{
    public function findBy(array $keyValuePairs): array
    {
        try {
            $reflectionProperties = (new ReflectionClass($this->getEntityName()))->getProperties();
        } catch (ReflectionException $e) {
            throw new QueryException($e->getMessage(), $e->getCode(), $e);
        }
        $indexedProperties = [];

        foreach ($reflectionProperties as $reflectionProperty) {
            $indexedProperties[$reflectionProperty->getName()] = $reflectionProperty;
            $reflectionProperty->setAccessible(true);
        }

        $items = $this->items;

        foreach ($keyValuePairs as $key => $value) {
            if (!array_key_exists($key, $indexedProperties)) {
                throw new QueryException(sprintf('%s is not a property of %s.', $key, $this->fQCN));
            }
            foreach ($items as $itemKey => $item) {
                if ($indexedProperties[$key]->getValue($item) !== $value) {
                    unset($items[$itemKey]);
                }
            }
        }

        return $items;
    }

    public function add(EmbeddedEntityInterface $embeddedEntity): EmbeddedCollectionInterface
    {
        if ($this->fQCN !== get_class($embeddedEntity)) {
            throw new HomogeneityException(sprintf(
                'Embedded entity of type %s cannot be added to a embedded collection of type %s.',
                get_class($embeddedEntity),
                $this->fQCN
            ));
        }

        $this->items[spl_object_id($embeddedEntity)] = $embeddedEntity;

        return $this;
    }

    public function remove(EmbeddedEntityInterface $embeddedEntity): EmbeddedCollectionInterface
    {
        if ($this->fQCN !== get_class($embeddedEntity)) {
            throw new HomogeneityException(sprintf(
                'Embedded entity of type %s cannot be removed from a embedded collection of type %s.',
                get_class($embeddedEntity),
                $this->fQCN
            ));
        }

        unset($this->items[spl_object_id($embeddedEntity)]);

        return $this;
    }

    public function has(EmbeddedEntityInterface $embeddedEntity): bool
    {
        return key_exists(spl_object_id($embeddedEntity), $this->items);
    }
}
