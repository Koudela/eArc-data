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

abstract class AbstractValue
{
    protected $dataCategory;
    protected $dataProperty;
    protected $shortKey;

    public function __construct(string $dataCategory, string $dataProperty)
    {
        $this->dataCategory = $dataCategory;
        $this->dataProperty = $dataProperty;
        $this->shortKey = $dataCategory.':'.$dataProperty;
    }

    public function shortKey(): string
    {
        return $this->shortKey;
    }
}
