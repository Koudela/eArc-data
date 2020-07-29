<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Manager\Interfaces;

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\Serializer\Exceptions\SerializeException;

interface EntitySaveStackInterface
{
    /**
     * This is the only method of the earc/data-store allowed to generate new
     * primary keys. On calling the method the entity gets its primary key and
     * is added to the entity save stack.
     *
     * This method is only allowed to be called from within the persisting
     * process.
     *
     * @param EntityInterface $entity
     *
     * @throws SerializeException
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
     * @throws SerializeException
     */
    public static function afterEntitySaved(EntityInterface $entity): void;

    /**
     * This method is called every time an entity is removed.
     *
     * @throws SerializeException
     *
     * @param EntityInterface $entity
     */
    public static function afterEntityRemoved(EntityInterface $entity): void;
}
