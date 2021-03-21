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

interface OnPersistInterface
{
    /**
     * Returns an iterable of callbacks. These will be called in order to save the
     * data of the entity.
     *
     * All callables of all tagged Services are called. Thus there can be services
     * persisting to database(s), services persisting to search indices and services
     * caching the entity by shared memory, redis server or other means.
     *
     * @return callable[]
     */
    public function getOnPersistCallables(): iterable;
}
