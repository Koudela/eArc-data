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

class QueryValue extends AbstractValue
{
    protected $value;

    public function __construct(string $dataCategory, string $dataProperty, $value)
    {
        $this->value = $value;

        parent::__construct($dataCategory, $dataProperty);
    }

    public function longKey(): string
    {
        static $longKey;

        if (!isset($longKey)) {
            $longKey = $this->shortKey().':'.$this->value();
        }

        return $longKey;
    }

    public function value()
    {
        return $this->value;
    }

    public function getSingleRange(): QueryRange
    {
        return new QueryRange($this->dataCategory, $this->dataProperty, $this->value);
    }
}
