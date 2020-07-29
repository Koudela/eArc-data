<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Filesystem;

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\DataStore\Filesystem\Interfaces\NamingServiceInterface;

abstract class StaticNamingService implements NamingServiceInterface
{
    public static function getQualifiedFilename(string $fQCN, string $primaryKey): string
    {
        return di_static(StaticDirectoryService::class)::getPathFromClassName($fQCN)
            .self::getFilename($primaryKey);
    }

    public static function getQualifiedFilenameForEntity(EntityInterface $entity): string
    {
        return di_static(StaticDirectoryService::class)::getPath($entity)
            .self::getFilename($entity->getPrimaryKey());
    }

    private static function getFilename(string $primaryKey): string
    {
        return $primaryKey.'.txt';
    }
}
