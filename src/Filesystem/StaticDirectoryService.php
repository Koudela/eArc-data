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
use eArc\DataStore\Filesystem\Interfaces\DirectoryServiceInterface;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;
use eArc\Serializer\Exceptions\SerializeException;

abstract class StaticDirectoryService implements DirectoryServiceInterface
{
    public static function forceChdir(EntityInterface $entity, $mod = ''): void
    {
        $absolutePath = self::getPath($entity, $mod);

        if (!is_dir($absolutePath)) {
            if (!mkdir($absolutePath, 0777, true)) {
                throw new SerializeException(sprintf('Cannot make dir %s.', $absolutePath));
            }
        }

        self::chdir($absolutePath);
    }

    public static function getPathFromClassName(string $fQCN, string $mod = ''): string
    {
        return di_param('earc.data.path').str_replace('\\', '/', $fQCN).$mod.'/';
    }

    public static function getPath(EntityInterface $entity, $mod = ''): string
    {
        return self::getPathFromClassName(get_class($entity), $mod);
    }

    /**
     * @param string $absolutePath
     *
     * @throws SerializeExceptionInterface
     */
    private static function chdir(string $absolutePath)
    {
        if (!chdir($absolutePath)) {
            throw new SerializeException(sprintf('Cannot change to dir %s.', $absolutePath));
        }
    }
}
