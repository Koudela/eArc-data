<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data;

use function eArc\Data\events\components\earc_data\getClassName;
use eArc\Data\Interfaces\Application\DataInterface;
use eArc\Data\Interfaces\Persistence\DataFactoryInterface;
use eArc\Data\Interfaces\Persistence\PersistableDataInterface;

/**
 * Data factory.
 */
class DataFactory implements DataFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function make(PersistableDataInterface $persistableData): DataInterface
    {
        $dataClass = getClassName(DataInterface::class);

        return new $dataClass($persistableData);
    }
}