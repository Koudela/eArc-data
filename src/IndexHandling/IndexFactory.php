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

use eArc\Data\IndexHandling\QualifiedValue;
use eArc\Data\IndexHandling\QueryRange;
use eArc\Data\IndexHandling\QueryValue;
use Redis;

interface IndexInterface
{
    public function addToIndex(QualifiedValue $value): bool;
    public function removeFromIndex(QualifiedValue $value): bool;
    public function queryIndex(QueryValue $value): array;
    public function cardinalityIndex(QueryValue $val): int;

    public function addToSortedIndex(QualifiedValue $value): bool;
    public function removeFromSortedIndex(QualifiedValue $value): bool;

    public function queryIndexRange(QueryRange $range): array;
    public function queryIndexRangeReversed(QueryRange $range): array;

    public function querySortedIndexRange(QueryRange $range): array;
    public function querySortedIndexRangeReversed(QueryRange $range): array;

    public function queryLexIndexRange(QueryRange $range): array;
    public function queryLexIndexRangeReversed(QueryRange $range): array;
}

/**
 * index/unique
 *
 * sort/limit
 */
class IndexFactory implements IndexInterface
{
    /** @var Redis */
    protected $redis;

    public function __construct()
    {
        $this->redis = new Redis();
    }

    public function addToIndex(QualifiedValue $val): bool
    {
        return (bool) $this->redis->sAdd($val->longKey(), $val->dataId());
    }

    public function removeFromIndex(QualifiedValue $val): bool
    {
        return (bool) $this->redis->sRem($val->longKey(), $val->dataId());
    }

    public function queryIndex(QueryValue $val): array
    {
        return $this->redis->sMembers($val->longKey());
    }

    public function cardinalityIndex(QueryValue $val): int
    {
        return $this->redis->sCard($val->longKey());
    }

    public function addToSortedIndex(QualifiedValue $val): bool
    {
        $this->redis->zAdd($val->shortKey(), $val->getSort(), $val->value());

        return $this->addToIndex($val);
    }

    public function removeFromSortedIndex(QualifiedValue $val): bool
    {
        $isSuccess = $this->removeFromIndex($val);

        if (0 !== $this->cardinalityIndex($val)) {
            return $isSuccess;
        }

        return (bool) $this->redis->zRem($val->shortKey(), $val->value()) && $isSuccess;
    }

    public function queryIndexRange(QueryRange $range): array
    {
        $items = [];

        foreach ($this->redis->zRange($range->shortKey(), $range->min(), $range->max()) as $value) {
            $items[$value] = $this->queryIndex($range->getQueryValue($value));
        }

        return $items;
    }

    public function queryIndexRangeReversed(QueryRange $range): array
    {
        $items = [];

        foreach ($this->redis->zRevRange($range->shortKey(), $range->min(), $range->max()) as $value) {
            $items[$value] = $this->queryIndex($range->getQueryValue($value));
        }

        return $items;
    }

    public function querySortedIndexRange(QueryRange $range): array
    {
        return $this->querySortedIndexRaw($range, 'ZRANGEBYSCORE');
    }

    public function querySortedIndexRangeReversed(QueryRange $range): array
    {
        return $this->querySortedIndexRaw($range, 'ZREVRANGEBYSCORE');
    }

    public function queryLexIndexRange(QueryRange $range): array
    {
        return $this->queryLexIndexRaw($range, 'ZRANGEBYLEX');
    }

    public function queryLexIndexRangeReversed(QueryRange $range): array
    {
        return $this->queryLexIndexRaw($range, 'ZREVRANGEBYLEX');
    }

    protected function queryLexIndexRaw(QueryRange $range, string $command): array
    {
        $min = is_null($range->min()) ? '-' : ($range->minIsIncluded() ? '[' : '(' ).$range->min();
        $max = is_null($range->max()) ? '+' : ($range->maxIsIncluded() ? '[' : '(' ).$range->max();
        $args = [$command, $range->shortKey(), $min, $max];

        return $this->queryIndexRangeRaw($range, $args);
    }

    protected function querySortedIndexRaw(QueryRange $range, string $command)
    {
        $min = is_null($range->min()) ? '-inf' : ($range->minIsIncluded() ? '' : '(') . $range->min();
        $max = is_null($range->max()) ? '+inf' : ($range->maxIsIncluded() ? '' : '(') . $range->max();
        $args = [$command, $range->shortKey(), $min, $max];

        return $this->queryIndexRangeRaw($range, $args);
    }

    protected function queryIndexRangeRaw(QueryRange $range, array $args): array
    {
        $items = [];

        if (!is_null($range->limit())) {
            array_push($args, 'LIMIT');
            array_push($args, $range->offset());
            array_push($args, $range->limit());
        }

        $range = call_user_func_array([$this->redis, 'rawCommand'], $args);

        foreach ($range as $value) {
            $items[$value] = $this->queryIndex($range->getQueryValue($value));
        }

        return $items;
    }
}
