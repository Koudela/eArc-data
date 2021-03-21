<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager\Interfaces\Events;

interface OnAutoPrimaryKeyInterface
{
    /**
     * Returns an iterable of callbacks. These will be called in order to
     * generate a primary key, if a entity without primary key and with
     * `AutoPrimaryKeyInterface` is persisted.
     *
     * As soon as the one callable of one tagged Service returns an string result
     * the other registered services and callables are skipped. Thus there is only
     * on generated key even if more callables are applicable.
     *
     * @return callable[]
     */
    public function getOnAutoPrimaryKeyCallables(): iterable;
}
