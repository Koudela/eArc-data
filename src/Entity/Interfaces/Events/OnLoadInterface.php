<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces\Events;

interface OnLoadInterface
{
    /**
     * Returns an iterable of callbacks. These will be called post load.
     *
     * Hint: Use this interface emulate the constructor as earc/data creates
     * entities from data without calling the constructor.
     *
     * @return callable[]
     */
    public function getOnLoadCallables(): iterable;
}
