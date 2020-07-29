<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Filesystem\Interfaces;

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;

interface DirectoryServiceInterface
{
    /**
     * @param EntityInterface $entity
     * @param string $mod
     *
     * @throws SerializeExceptionInterface
     */
    public static function forceChdir(EntityInterface $entity, $mod = ''): void;

    /**
     * @param string $fQCN
     * @param string $mod
     *
     * @return string
     */
    public static function getPathFromClassName(string $fQCN, string $mod = ''): string;

    /**
     * @param EntityInterface $entity
     * @param string $mod
     *
     * @return string
     */
    public static function getPath(EntityInterface $entity, $mod = ''): string;
}
