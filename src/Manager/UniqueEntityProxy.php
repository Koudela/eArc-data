<?php /** @noinspection PhpUnusedPrivateMethodInspection */
declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Manager;

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\DataStore\Exceptions\HomogeneityException;
use eArc\DataStore\Manager\Interfaces\EntityProxyInterface;
use InvalidArgumentException;

final class UniqueEntityProxy extends StaticEntitySaveStack implements EntityProxyInterface
{
    /** @var string|null */
    private $primaryKey;
    /** @var string */
    private $fQCN;
    /** @var EntityInterface|null */
    private $entity;

    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }

    public function getEntityName(): string
    {
        return $this->fQCN;
    }

    public function load(?string $typeHint = null): EntityInterface
    {
        if (null !== $typeHint && $this->fQCN !== $typeHint) {
            throw new HomogeneityException(sprintf(
                'Type hint %s does not match class %s used by entity proxy.',
                $typeHint,
                $this->fQCN
            ));
        }

        if (null === $this->entity) {
            $this->entity = data_load($this->fQCN, $this->primaryKey);
        }

        return $this->entity;
    }


    public static function getInstance($identifier, ?string $fQCN = null): UniqueEntityProxy
    {
        if ($identifier instanceof EntityInterface) {
            $fQCN = get_class($identifier);
            $entity = $identifier;
            $identifier = $entity->getPrimaryKey();
        }

        if (is_string($identifier)) {
            if (!isset(self::$persistedEntities[$fQCN][$identifier])) {
                self::$persistedEntities[$fQCN][$identifier] = new UniqueEntityProxy($identifier, $fQCN);
            }

            return self::$persistedEntities[$fQCN][$identifier];
        }

        if ($identifier instanceof EntityInterface) {
            if (!isset(self::$notPersistedEntities[spl_object_id($entity)])) {
                self::$notPersistedEntities[spl_object_id($entity)] = new UniqueEntityProxy($entity, $fQCN);
            }

            return self::$notPersistedEntities[spl_object_id($entity)];
        }

        throw new InvalidArgumentException('The identifier has to be a primary key or an entity.');
    }

    /**
     * @param string|EntityInterface $identifier Primary key or entity
     * @param string|null $fQCN Has to be set if the primary key is used as
     *        identifier, is ignored otherwise.
     */
    private function __construct($identifier, ?string $fQCN = null)
    {
        if ($identifier instanceof EntityInterface) {
            $this->primaryKey = $identifier->getPrimaryKey();
            $this->fQCN = get_class($identifier);
            $this->entity = $identifier;

            return;
        }

        if (is_string($identifier)) {
            $this->primaryKey = $identifier;
            $this->fQCN = $fQCN;

            if (null === $this->fQCN) {
                throw new InvalidArgumentException(
                    'The fully qualified class name cannot be null if the primary key is used as identifier.'
                );
            }

            return;
        }

        throw new InvalidArgumentException('The identifier has to be a primary key or an entity.');
    }

    private function __clone() {}
    private function __wakeup() {}
}
