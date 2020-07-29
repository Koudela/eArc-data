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

interface NamingServiceInterface
{
    /**
     * @param string $fQCN
     * @param string $uid
     *
     * @return string
     */
    public static function getQualifiedFilename(string $fQCN, string $uid): string;

    /**
     * @param EntityInterface $entity
     *
     * @return string
     */
    public static function getQualifiedFilenameForEntity(EntityInterface $entity): string;
}
