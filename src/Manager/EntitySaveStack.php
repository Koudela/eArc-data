<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Events\PostPersistInterface;
use eArc\Data\Entity\Interfaces\Events\PrePersistInterface;
use eArc\Data\Entity\Interfaces\ImmutableEntityInterface;
use eArc\Data\Entity\Interfaces\MutableEntityReferenceInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\AutoPrimaryKeyInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;
use eArc\Data\Exceptions\DataException;
use eArc\Data\Exceptions\NoDataException;
use eArc\Data\Manager\Interfaces\EntitySaveStackInterface;
use eArc\Data\Manager\Interfaces\Events\OnAutoPrimaryKeyInterface;
use eArc\Data\Manager\Interfaces\Events\OnPersistInterface;
use eArc\Data\ParameterInterface;
use function data_persist;

class EntitySaveStack implements EntitySaveStackInterface
{
    /** @var EntityInterface[] */
    protected array $entitySaveStack = [];

    public function schedule(EntityInterface $entity): void
    {
        $this->entitySaveStack[spl_object_id($entity)] = $entity;
    }

    public function persist(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->schedule($entity);
        }

        $entities = $this->entitySaveStack;

        $this->entitySaveStack = [];

        $this->save($entities);
    }

    /**
     * @param EntityInterface[] $entities
     */
    protected function save(array $entities): void
    {
        foreach ($entities as $entity) {
            foreach (di_get_tagged(ParameterInterface::TAG_PRE_PERSIST) as $service => $args) {
                $service = di_get($service);
                if (!$service instanceof PrePersistInterface) {
                    throw new DataException(sprintf(
                        '{2da5c06b-62fd-4118-8b9c-f95ef379b024} Services tagged by the %s have to implement it.',
                        PrePersistInterface::class
                    ));
                }
                $service->prePersist($entity);
            }

            if ($entity instanceof PrePersistInterface) {
                /** @var $entity EntityInterface|PrePersistInterface */
                $entity->prePersist($entity);
            }

            if ($entity instanceof ImmutableEntityInterface && !is_null($entity->getPrimaryKey())) {
                try {
                    data_load(get_class($entity), $entity->getPrimaryKey());
                    if ($entity instanceof AutoPrimaryKeyInterface) {
                        $entity->setPrimaryKey(null);
                    } else {
                        throw new DataException(sprintf(
                            '{d98eb0dc-9cb1-490f-807d-e5089ee85112} Data exists already for immutable entity %s and primary key %s.',
                            get_class($entity),
                            $entity->getPrimaryKey()
                        ));
                    }
                } catch (NoDataException $noDataException) {
                    // we can go on and save the immutable entity as the primary key is not in use
                    unset($noDataException);
                }
            }

            if (is_null($entity->getPrimaryKey())) {
                $this->generatePrimaryKey($entity);
            }
        }

        foreach (di_get_tagged(ParameterInterface::TAG_ON_PERSIST) as $service => $args) {
            $service = di_get($service);
            if (!$service instanceof OnPersistInterface) {
                throw new DataException(sprintf(
                    '{f88ee6b9-4314-4efe-887a-d930c46d30c4} Services tagged by the %s have to implement it.',
                    OnPersistInterface::class
                ));
            }
            $service->onPersist($entities);
        }

        foreach ($entities as $entity) {
            if (is_null($entity->getPrimaryKey()) || $entity->getPrimaryKey()==='') {
                throw new DataException(sprintf(
                    '{4be1fe92-0afe-4a2f-8d6c-549fc30e24d0} The entity has no primary key and was probably not saved.'
                ));
            }
            $this->entitySaveStack[$entity::class][$entity->getPrimaryKey()] = $entity;

            if ($entity instanceof MutableReverenceKeyInterface) {
                $this->processMutableReverenceKeyInterface($entity);
            }

            foreach (di_get_tagged(ParameterInterface::TAG_POST_PERSIST) as $service => $args) {
                $service = di_get($service);
                if (!$service instanceof PostPersistInterface) {
                    throw new DataException(sprintf(
                        '{2651f432-fa70-4d1f-85ce-416afaf7b296} Services tagged by the %s have to implement it.',
                        PostPersistInterface::class
                    ));
                }
                $service->postPersist($entity);
            }

            if ($entity instanceof PostPersistInterface) {
                /** @var $entity PostPersistInterface|EntityInterface */
                $entity->postPersist($entity);
            }
        }
    }

    protected function generatePrimaryKey(EntityInterface $entity): void
    {
        if ($entity instanceof AutoPrimaryKeyInterface) {
            foreach (di_get_tagged(ParameterInterface::TAG_ON_AUTO_PRIMARY_KEY) as $service => $args) {
                $service = di_get($service);
                if (!$service instanceof OnAutoPrimaryKeyInterface) {
                    throw new DataException(sprintf(
                        '{ac171999-74a1-43b4-a1b1-a6a47cc7a2cb} Services tagged by the %s have to implement it.',
                        OnAutoPrimaryKeyInterface::class
                    ));
                }
                $primaryKey = $service->onAutoPrimaryKey($entity);
                if (!is_null($primaryKey)) {
                    $entity->setPrimaryKey($primaryKey);

                    break;
                }
            }
        }

        if (is_null($entity->getPrimaryKey())) {
            throw new DataException(sprintf(
                '{bd5a6da9-1c3b-4b9c-8004-84913ba96425} Primary key has not been set for class %s.',
                get_class($entity)
            ));
        }
    }

    protected function processMutableReverenceKeyInterface(MutableReverenceKeyInterface $entity): void
    {
        $reverenceEntityClass = $entity->getMutableReverenceClass();
        if (!$reverenceEntityClass instanceof MutableEntityReferenceInterface) {
            throw new DataException(sprintf(
                '{8c33749e-f11b-4f64-9054-c409ad637fb2} The `getMutableReverenceClass()` method of the %s has to return the fully qualified class name of a class implementing the %s.',
                MutableReverenceKeyInterface::class,
                MutableEntityReferenceInterface::class
            ));
        }
        if ($reverenceEntityClass instanceof ImmutableEntityInterface) {
            throw new DataException(sprintf(
                '{8c33749e-f11b-4f64-9054-c409ad637fb2} The `getMutableReverenceClass()` method of the %s must not return the fully qualified class name of a class implementing the %s.',
                MutableReverenceKeyInterface::class,
                ImmutableEntityInterface::class

            ));
        }

        /** @var MutableEntityReferenceInterface $reverenceEntity */
        $reverenceEntity = data_load($entity->getMutableReverenceClass(), $entity->getMutableReverenceKey());

        $reverenceEntity->setMutableReverenceTarget($entity);
        data_persist($reverenceEntity);
    }
}
