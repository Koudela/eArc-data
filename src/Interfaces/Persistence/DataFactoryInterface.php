<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Interfaces\Persistence;

use eArc\Data\Interfaces\Application\DataInterface;

/**
 * Data factory interface.
 */
interface DataFactoryInterface
{
    /**
     * Transforms the persistable data object into a data object.
     *
     * @param PersistableDataInterface $persistableData
     *
     * @return DataInterface
     */
    public function make(PersistableDataInterface $persistableData): DataInterface;
}
