<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data;

use BootstrapEArcData;

abstract class Initializer
{
    public static function init(): void
    {
        if (!function_exists('data_load')) {
            include __DIR__ . '/../bootstrap/BootstrapEArcData.php';

            BootstrapEArcData::init();
        }
    }
}
