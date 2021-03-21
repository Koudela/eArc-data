<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Collection\Interfaces;

use eArc\Data\Entity\Interfaces\EntityBaseInterface;
use IteratorAggregate;

interface CollectionBaseInterface extends IteratorAggregate
{
    /**
     * @return EntityBaseInterface The entity the collection is a property of.
     */
    public function getOwner(): EntityBaseInterface;

    /**
     * Hint: Consider using the iterator instead.
     *
     * @return EntityBaseInterface[] The items of the collection as array.
     */
    public function asArray(): array;
}
