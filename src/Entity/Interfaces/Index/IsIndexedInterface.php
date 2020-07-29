<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Entity\Interfaces\Index;

interface IsIndexedInterface
{
    const TYPE_INDEX = 'index';
    const TYPE_UNIQUE = 'unique';

    /**
     * Returns an array with the indexed properties. The keys are the properties
     * and the value is the type of index.
     *
     * @return string[]
     */
    public static function getIndexedProperties(): array;
}
