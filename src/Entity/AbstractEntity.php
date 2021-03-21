<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity;

use eArc\Data\Entity\Interfaces\EntityInterface;

abstract class AbstractEntity implements EntityInterface
{
    protected ?string $primaryKey;

    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }
}
