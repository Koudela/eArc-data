<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Filesystem;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Filesystem\Interfaces\DirectoryServiceInterface;
use eArc\Data\ParameterInterface;
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
        return di_param(ParameterInterface::DATA_PATH).str_replace('\\', '/', $fQCN).$mod.'/';
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
