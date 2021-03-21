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
use eArc\Data\Exceptions\DataException;
use eArc\Data\Manager\Interfaces\EntitySaveStackInterface;
use eArc\Data\Manager\Interfaces\Events\OnPersistInterface;
use eArc\Data\ParameterInterface;

class EntitySaveStack implements EntitySaveStackInterface
{
    /** @var EntityInterface[] */
    protected array $entitySaveStack = [];

    public function schedule(EntityInterface $entity): void
    {
        $this->entitySaveStack[spl_object_id($entity)] = $entity;
    }

    public function persist(EntityInterface|null $entity): void
    {
        foreach ($this->entitySaveStack as $key => $item) {
            $this->save($item);

            unset($this->entitySaveStack[$key]);
        }

        if (!is_null($entity)) {
            $this->save($entity);
        }
    }

    protected function save(EntityInterface $entity): void
    {
        foreach (di_get_tagged(ParameterInterface::TAG_PRE_PERSIST) as $service) {
            $service = di_get($service);
            if (!$service instanceof PrePersistInterface) {
                throw new DataException(sprintf(
                    '{2da5c06b-62fd-4118-8b9c-f95ef379b024} Services tagged by the %s have to implement it.',
                    PrePersistInterface::class
                ));
            }
            foreach ($service->getPrePersistCallables() as $callable) {
                $callable($entity);
            }
        }

        if ($entity instanceof PrePersistInterface) {
            foreach ($entity->getPrePersistCallables() as $callable) {
                $callable($entity);
            }
        }

        foreach (di_get_tagged(ParameterInterface::TAG_ON_PERSIST) as $service) {
            $service = di_get($service);
            if (!$service instanceof OnPersistInterface) {
                throw new DataException(sprintf(
                    '{f88ee6b9-4314-4efe-887a-d930c46d30c4} Services tagged by the %s have to implement it.',
                    OnPersistInterface::class
                ));
            }
            foreach ($service->getOnPersistCallables() as $callable) {
                $callable($entity);
            }
        }

        if (is_null($entity->getPrimaryKey())) {
            throw new DataException(sprintf(
                '{4be1fe92-0afe-4a2f-8d6c-549fc30e24d0} The entity has no primary key and was probably not saved.'
            ));
        }
        $this->entitySaveStack[$entity::class][$entity->getPrimaryKey()] = $entity;

        foreach (di_get_tagged(ParameterInterface::TAG_POST_PERSIST) as $service) {
            $service = di_get($service);
            if (!$service instanceof PostPersistInterface) {
                throw new DataException(sprintf(
                    '{2651f432-fa70-4d1f-85ce-416afaf7b296} Services tagged by the %s have to implement it.',
                    PostPersistInterface::class
                ));
            }
            foreach ($service->getPostPersistCallables() as $callable) {
                $callable($entity);
            }
        }

        if ($entity instanceof PostPersistInterface) {
            foreach ($entity->getPostPersistCallables() as $callable) {
                $callable($entity);
            }
        }
    }
}
