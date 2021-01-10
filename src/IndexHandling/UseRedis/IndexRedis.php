<?php declare(strict_types=1);
/**
* e-Arc Framework - the explicit Architecture Framework
*
* @package earc/data
* @link https://github.com/Koudela/eArc-data/
* @copyright Copyright (c) 2019-2021 Thomas Koudela
* @license http://opensource.org/licenses/MIT MIT License
*/

namespace eArc\Data\IndexHandling\UseRedis;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Index\IsIndexedInterface;
use eArc\Data\Exceptions\UniqueIndexViolationException;
use eArc\Data\IndexHandling\Interfaces\IndexInterface;
use eArc\Data\IndexHandling\UseRedis\Helper\QualifiedValue;
use eArc\Data\IndexHandling\UseRedis\Helper\RedisBridge;

class IndexRedis implements IndexInterface
{
    /** @var RedisBridge */
    protected $redisBridge;

    public function __construct()
    {
        $this->redisBridge = di_get(RedisBridge::class);
    }

    public function updateIndex(string $type, EntityInterface $entity, string $propertyName, $oldValue, $newValue): void
    {
        $value = new QualifiedValue($entity->getPrimaryKey(), get_class($entity), $propertyName, $oldValue, $newValue);

        if ($type === IsIndexedInterface::TYPE_UNIQUE) {
            $result = $this->redisBridge->queryIndex($value);
            if (count($result) > 1 || count($result) === 1 && reset($result) !== $entity->getPrimaryKey()) {
                throw new UniqueIndexViolationException(sprintf(
                    '[%s] from (%s) is violated by new key %s',
                    implode(',', $result),
                    $value->longKey(),
                    $entity->getPrimaryKey()
                ));
            }
            if (count($result) === 0) {
                $this->redisBridge->removeFromIndex($value);
            }
        }

        $this->redisBridge->addToIndex($value);
    }

    public function queryIndex(string $dataCategory, string $dataProperty, string $value, ?int $min = null, ?int $max = null): string
    {
        // TODO: Implement queryIndex() method.
    }

    public function querySortedIndex(string $dataCategory, string $dataProperty, string $value, ?float $min = null, ?float $max = null): string
    {
        // TODO: Implement querySortedIndex() method.
    }

    public function updateSortedIndex(string $type, EntityInterface $entity, string $propertyName, ?float $sort = 0): void
    {
        $value = new QualifiedValue($entity->getPrimaryKey(), get_class($entity), $propertyName, null, $sort);

        if ($type === IsIndexedInterface::TYPE_UNIQUE) {
            $this->redisBridge->queryIndexRange($value->getSingleRange());
        }

        $this->redisBridge->addToSortedIndex($value);
    }
}
