<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Filesystem\Interfaces;

use eArc\Data\Entity\Interfaces\EntityInterface;
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
