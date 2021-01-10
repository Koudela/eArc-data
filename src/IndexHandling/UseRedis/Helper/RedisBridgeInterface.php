<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\IndexHandling\UseRedis\Helper;

interface RedisBridgeInterface
{
    public function addToIndex(QualifiedValue $value): bool;
    public function removeFromIndex(QualifiedValue $value): bool;

    public function queryIndex(QueryValue $val): array;
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
