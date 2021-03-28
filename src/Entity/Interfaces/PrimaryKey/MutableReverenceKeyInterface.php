<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces\PrimaryKey;

/**
 * If you use a chain of immutables to keep track of the changes to an
 * entity, you can not reference the entity via the primary key. You can not
 * use another key either, because you can not remove it from the older ones.
 * You may attach a counter an search for the maximal value, but that may
 * slow down loading significantly as the chain grows.
 *
 * The solution is a second mutable entity which updates its reverence.
 *
 * By implementing the mutable reverence key interface the update of the mutable
 * entity is done automatically by earc/data.
 */
interface MutableReverenceKeyInterface extends PrimaryKeyInterface
{
    /**
     * Get the primary key of the mutable reference entity.
     *
     * @return string
     */
    public function getMutableReverenceKey(): string;

    /**
     * Get the fully qualified class name of the mutable reference entity
     * (implementing the MutableEntityReferenceInterface).
     *
     * @return string
     */
    public function getMutableReverenceClass(): string;
}
