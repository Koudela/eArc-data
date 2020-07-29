<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Collection\Interfaces;

use eArc\DataStore\Entity\Interfaces\EntityBaseInterface;
use IteratorAggregate;

interface CollectionBaseInterface extends IteratorAggregate
{
    /**
     * @return EntityBaseInterface
     */
    public function getOwner(): EntityBaseInterface;

    /**
     * @return array
     */
    public function asArray(): array;
}
