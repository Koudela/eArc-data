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

class QualifiedValue extends QueryValue
{
    protected $dataId;
    protected $newValue;

    public function __construct(string $dataId, string $dataCategory, string $dataProperty, $oldValue, $newValue)
    {
        $this->dataId = $dataId;
        $this->newValue = $newValue;

        parent::__construct($dataCategory, $dataProperty, $oldValue);
    }

    public function dataId(): string
    {
        return $this->dataId;
    }

    public function newLongKey(): string
    {
        static $longKeyNew;

        if (!isset($longKeyNew)) {
            $longKeyNew = $this->shortKey().':'.$this->NewValue();
        }

        return $longKeyNew;
    }

    public function newValue()
    {
        return $this->newValue;
    }
}
