<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces\Cascade;

interface CascadeDeleteInterface
{
    /**
     * Returns an array with the cascaded properties.
     *
     * @return string[]
     */
    public static function getCascadeOnDeleteProperties(): array;
}
