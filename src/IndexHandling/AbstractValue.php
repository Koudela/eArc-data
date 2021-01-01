<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\IndexHandling;

class QueryValue
{
    protected $dataCategory;
    protected $dataProperty;
    protected $value;
    protected $shortKey;

    public function __construct(string $dataCategory, string $dataProperty, $value)
    {
        $this->dataCategory = $dataCategory;
        $this->dataProperty = $dataProperty;
        $this->value = $value;
        $this->shortKey = $dataCategory.':'.$dataProperty;
    }

    public function shortKey(): string
    {
        return $this->shortKey;
    }

    public function longKey(): string
    {
        static $longKey;

        if (!isset($longKey)) {
            $longKey = $this->shortKey().':'.$this->value();
        }

        return $longKey;
    }

    public function value(): string
    {
        static $stringValue;

        if (!isset($stringValue)) {
            $stringValue = (string) $this->value;
        }

        return $stringValue;
    }

    public function getSort(): int
    {
        static $sort;

        if (!isset($sort)) {
            $sort = 0;
        }

        return $sort;
    }
}
