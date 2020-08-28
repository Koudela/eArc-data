<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Filesystem\Interfaces;

use eArc\Data\Entity\Interfaces\EntityInterface;

interface NamingServiceInterface
{
    /**
     * @param string $fQCN
     * @param string $primaryKey
     *
     * @return string
     */
    public static function getQualifiedFilename(string $fQCN, string $primaryKey): string;

    /**
     * @param EntityInterface $entity
     *
     * @return string
     */
    public static function getQualifiedFilenameForEntity(EntityInterface $entity): string;
}
