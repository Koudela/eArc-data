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

use eArc\Data\Entity\Interfaces\Events\PostLoadInterface;
use eArc\Data\Entity\Interfaces\Events\PostRemoveInterface;
use eArc\Data\Entity\Interfaces\Events\PreLoadInterface;
use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Events\PreRemoveInterface;
use eArc\Data\Exceptions\DataException;
use eArc\Data\Exceptions\NoDataException;
use eArc\Data\Manager\Interfaces\DataStoreInterface;
use eArc\Data\Manager\Interfaces\Events\OnLoadInterface;
use eArc\Data\Manager\Interfaces\Events\OnRemoveInterface;
use eArc\Data\ParameterInterface;

class DataStore implements DataStoreInterface
{
    /** @var EntityInterface[][] */
    protected array $entities = [];

    public function load(string $fQCN, string $primaryKey): EntityInterface
    {
        if (!$this->isLoaded($fQCN, $primaryKey)) {
            foreach (di_get_tagged(ParameterInterface::TAG_PRE_LOAD) as $service) {
                $service = di_static($service);
                if (!$service instanceof PreLoadInterface) {
                    throw new DataException(sprintf(
                        '{4c316e5c-d82d-4562-9eca-c029a2ed44fe} Services tagged by the %s have to implement it.',
                        PreLoadInterface::class
                    ));
                }
                foreach ($service::getPreLoadCallables() as $callable) {
                    $callable($fQCN, $primaryKey);
                }
            }

            if ($fQCN instanceof PreLoadInterface) {
                foreach ($fQCN::getPreLoadCallables() as $callable) {
                    $callable($fQCN, $primaryKey);
                }
            }

            foreach (di_get_tagged(ParameterInterface::TAG_ON_LOAD) as $service) {
                $service = di_get($service);
                if (!$service instanceof OnLoadInterface) {
                    throw new DataException(sprintf(
                        '{c3680a93-cc0e-47d4-8d87-573398fcbc0d} Services tagged by the %s have to implement it.',
                        OnLoadInterface::class
                    ));
                }
                foreach ($service->getOnLoadCallables() as $callable) {
                    $entity = $callable($fQCN, $primaryKey);
                    if ($entity instanceof EntityInterface) {
                        break;
                    }
                }
            }

            if (!isset($entity) || !$entity instanceof EntityInterface) {
                throw new NoDataException(sprintf('{33248032-3410-4e3d-af50-5dd3c810e750} Failed to load data for %s - %s.', $fQCN, $primaryKey));
            }
            $this->entities[$fQCN][$primaryKey] = $entity;

            foreach (di_get_tagged(ParameterInterface::TAG_POST_LOAD) as $service) {
                $service = di_get($service);
                if (!$service instanceof PostLoadInterface) {
                    throw new DataException(sprintf(
                        '{44655d29-29aa-4e15-8807-353b49495573} Services tagged by the %s have to implement it.',
                        PostLoadInterface::class
                    ));
                }
                foreach ($service->getPostLoadCallables() as $callable) {
                    $callable($entity);
                }
            }

            if ($entity instanceof PostLoadInterface) {
                foreach ($entity->getPostLoadCallables() as $callable) {
                    $callable($entity);
                }
            }
        }

        return $this->entities[$fQCN][$primaryKey];
    }

    public function isLoaded(string $fQCN, string $primaryKey): bool
    {
        return isset($this->entities[$fQCN][$primaryKey]);
    }

    public function detach(?string $fQCN = null, ?array $primaryKeys = null): void
    {
        if (null!==$fQCN) {
            if (null!==$primaryKeys) {
                foreach ($primaryKeys as $primaryKey) {
                    unset($this->entities[$fQCN][$primaryKey]);
                }

                return;
            }

            $this->entities[$fQCN] = [];

            return;
        }

        $this->entities = [];
    }

    public function delete(EntityInterface $entity): void
    {
        $this->remove($entity::class, $entity->getPrimaryKey());
    }

    public function remove(string $fQCN, string $primaryKey): void
    {
        unset($this->entities[$fQCN][$primaryKey]);

        foreach (di_get_tagged(ParameterInterface::TAG_PRE_REMOVE) as $service) {
            $service = di_static($service);
            if (!$service instanceof PreRemoveInterface) {
                throw new DataException(sprintf(
                    '{19afae2d-5f2e-4308-9182-2e817f8c6349} Services tagged by the %s have to implement it.',
                    PreRemoveInterface::class
                ));
            }
            foreach ($service::getPreRemoveCallables() as $callable) {
                $callable($fQCN, $primaryKey);
            }
        }

        if ($fQCN instanceof PreRemoveInterface) {
            foreach ($fQCN::getPreRemoveCallables() as $callable) {
                $callable($fQCN, $primaryKey);
            }
        }

        foreach (di_get_tagged(ParameterInterface::TAG_ON_REMOVE) as $service) {
            $service = di_get($service);
            if (!$service instanceof OnRemoveInterface) {
                throw new DataException(sprintf(
                    '{c2275908-ae39-4114-b391-c0c20a5f91c8} Services tagged by the %s have to implement it.',
                    OnRemoveInterface::class
                ));
            }
            foreach ($service->getOnRemoveCallables() as $callable) {
                $callable($fQCN, $primaryKey);
            }
        }

        foreach (di_get_tagged(ParameterInterface::TAG_POST_REMOVE) as $service) {
            $service = di_static($service);
            if (!$service instanceof PostRemoveInterface) {
                throw new DataException(sprintf(
                    '{174e73d8-efd9-475f-85d2-b1bfe3d4d008} Services tagged by the %s have to implement it.',
                    PostRemoveInterface::class
                ));
            }
            foreach ($service::getPostRemovedCallables() as $callable) {
                $callable($fQCN, $primaryKey);
            }
        }

        if ($fQCN instanceof PostRemoveInterface) {
            foreach ($fQCN::getPostRemovedCallables() as $callable) {
                $callable($fQCN, $primaryKey);
            }
        }
    }
}
