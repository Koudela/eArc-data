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

class QueryRange extends AbstractValue
{
    protected $min;
    protected $max;
    protected $minIsIncluded;
    protected $maxIsIncluded;
    protected $limit;
    protected $offset;

    public function __construct(
        string $dataCategory,
        string $dataProperty,
        $min,
        $max = null,
        bool $minIsIncluded = true,
        bool $maxIsIncluded = true,
        ?int $limit = null,
        ?int $offset = null
    ) {
        parent::__construct($dataCategory, $dataProperty);

        $this->min = $min;
        $this->max = $max;
        $this->minIsIncluded = $minIsIncluded;
        $this->maxIsIncluded = $maxIsIncluded;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function min(): int
    {
        return $this->min;
    }

    public function max(): int
    {
        return $this->max();
    }

    public function getQueryValue($value): QueryValue
    {
        return new QueryValue($this->dataCategory, $this->dataProperty, $value);
    }

    public function minIsIncluded(): bool
    {
        return $this->minIsIncluded;
    }

    public function maxIsIncluded(): bool
    {
        return $this->maxIsIncluded;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }
}
