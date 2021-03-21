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

interface PostPersistInterface
{
    /**
     * Returns an iterable of callbacks. These will be called after persisting
     * the entity.
     *
     * Hint: Use this interface to cascade persistence.
     *
     * @return callable[]
     */
    public function getPostPersistCallables(): iterable;
}
