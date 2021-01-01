<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager\Interfaces;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;

interface EntitySaveStackInterface
{
    /**
     * This is the only method of the earc/data allowed to generate new
     * primary keys. On calling the method the entity gets its primary key and
     * is added to the entity save stack.
     *
     * This method is only allowed to be called from within the persisting
     * process.
     *
     * @param EntityInterface $entity
     *
     * @throws SerializeExceptionInterface
     */
    public static function requirePrimaryKey(EntityInterface $entity): void;

    /**
     * @param EntityInterface $entity
     */
    public static function addToStack(EntityInterface $entity): void;

    /**
     * This method is called every time an entity is saved. It updates the
     * indices, removes the entity from the entity save stack and starts the
     * saving process for the next entity if the entity save stack is not empty.
     *
     * @param EntityInterface $entity
     *
     * @throws SerializeExceptionInterface
     */
    public static function afterEntitySaved(EntityInterface $entity): void;

    /**
     * This method is called every time an entity is removed.
     *
     * @throws SerializeExceptionInterface
     *
     * @param EntityInterface $entity
     */
    public static function afterEntityRemoved(EntityInterface $entity): void;
}
