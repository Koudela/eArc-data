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
use eArc\Data\Exceptions\Interfaces\NoDataExceptionInterface;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;

interface PersistenceInterface
{
    /**
     * @param string $fQCN
     * @param string $primaryKey
     *
     * @return EntityInterface
     *
     * @throws NoDataExceptionInterface|SerializeExceptionInterface
     */
    public static function load(string $fQCN, string $primaryKey): EntityInterface;

    /**
     * @param EntityInterface $entity
     *
     * @throws SerializeExceptionInterface
     */
    public static function persist(EntityInterface $entity): void;

    /**
     * @param EntityInterface $entity
     *
     * @throws NoDataExceptionInterface
     */
    public static function remove(EntityInterface $entity): void;
}
