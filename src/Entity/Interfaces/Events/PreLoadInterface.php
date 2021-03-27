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

interface PreLoadInterface
{
    /**
     * Will be called before loading the entity data.
     *
     * Hint: This method is static as the entity is not loaded yet.
     *
     * @param string $fQCN
     * @param string $primaryKey
     */
    public static function preLoad(string $fQCN, string $primaryKey): void;
}
