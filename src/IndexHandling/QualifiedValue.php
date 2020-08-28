<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/query-language
 * @link https://github.com/Koudela/eArc-query-language/
 * @copyright Copyright (c) 2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\IndexHandling\Interfaces;

use Redis;

interface IndexInterface
{
    public function updateIndex(string $dataId, string $dataCategory, string $dataProperty, ?string $value): void;
    public function queryIndex(string $dataCategory, string $dataProperty, string $value): string;

    public function updateSortedIndex(string $dataId, string $dataCategory, string $dataProperty, ?string $value, int $sort = 0): void;
    public function querySortedIndex(string $dataCategory, string $dataProperty, string $value, ?int $min = null, ?int $max = null): string;
}

class IndexFactory implements IndexInterface {
    /** @var Redis */
    protected $redis;
    public function __construct()
    {
        $this->redis = new Redis();
    }

    public function updateIndex(string $dataCategory, string $dataId, string $dataProperty, ?string $value, ?int $sort = null): void
    {
        $redis = new Redis();
        $redis->zAdd()
        $redis->set();
        $redis->setRange();
        $redis->brPop()
        // TODO: Implement updateIndex() method.
    }

    public function queryIndex(string $dataCategory, string $dataProperty, string $value, ?int $min = null, ?int $max = null): string
    {
        // TODO: Implement queryIndex() method.
    }

    public function addToSortedIndex(string $dataCategory, string $dataProperty, string $dataId, $value): void
    {
        $val = $this->getValFromValue($value);
        $sort = $this->getSortFromValue($value);
        $key = $this->getShortKey($dataCategory, $dataProperty);
        $longKey = $this->getLongKey($dataCategory, $dataProperty, $value);

        $this->redis->zAdd($key, $sort, $value);

        if ($arrIds = $this->redis->get($longKey)) {
            $ids = unserialize($arrIds);
        } else {
            $ids = [];
        }
        $ids[$dataId] = $dataId;
        $this->redis->set($longKey, serialize($ids));
    }

    public function removeFromSortedIndex(string $dataCategory, string $dataProperty, string $dataId, $value): void
    {
        // EntityClass PropertyName => IndexName
        // PrimaryKey
        // Value
        $val = $this->getValFromValue($value);
        $key = $this->getShortKey($dataCategory, $dataProperty);
        $longKey = $this->getLongKey($dataCategory, $dataProperty, $val);

        if ($arrIds = $this->redis->get($longKey)) {
            $ids = unserialize($arrIds);
            unset($ids[$dataId]);
            if (empty($ids)) {
                $this->redis->del($longKey);
                $this->redis->zRem($key, $val);
            } else {
                $this->redis->set($longKey, serialize($ids));
            }
        }
    }

    public function querySortedIndex(string $dataCategory, string $dataProperty, string $value, ?int $min = null, ?int $max = null): string
    {
        // TODO: Implement querySortedIndex() method.
    }

    protected function getShortKey(string $dataCategory, string $dataProperty)
    {
        return $dataCategory.':'.$dataProperty;
    }

    protected function getLongKey(string $dataCategory, string $dataProperty, string $value)
    {
        return $this->getShortKey($dataCategory, $dataProperty).':'.$value;
    }

    protected function getSortFromValue($value): int
    {
        return 0;
    }

    protected function getValFromValue($value): string
    {
        return (string) $value;
    }
}
