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

interface OnRemoveInterface
{
    /**
     * Returns an iterable of callbacks. These will be called for removing the
     * entity from the data.
     *
     * All callables of all tagged Services are called. Thus there can be services
     * removing the data from database(s), services removing the data from search
     * indices and services removing the data from shared memory cache, redis server
     * or other means.
     *
     * @return callable[]
     */
    public function getOnRemoveCallables(): iterable;
}
