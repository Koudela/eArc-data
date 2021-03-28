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
use eArc\Data\Entity\Interfaces\ImmutableEntityInterface;
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

    public function load(string $fQCN, array $primaryKeys, bool $useDataStoreOnly = false): array
    {
        $entities = [];
        $primaryKeysToLoad = [];

        foreach (array_flip($primaryKeys) as $primaryKey => $value) {
            if ($this->isLoaded($fQCN, $primaryKey)) {
                $entities[$primaryKey] = $this->entities[$fQCN][$primaryKey];
            } else {
                if ($useDataStoreOnly) {
                    continue;
                }

                $primaryKeysToLoad[$primaryKey] = $primaryKey;

                foreach (di_get_tagged(ParameterInterface::TAG_PRE_LOAD) as $service) {
                    $service = di_static($service);
                    if (!$service instanceof PreLoadInterface) {
                        throw new DataException(sprintf(
                            '{4c316e5c-d82d-4562-9eca-c029a2ed44fe} Services tagged by the %s have to implement it.',
                            PreLoadInterface::class
                        ));
                    }
                    $service::preLoad($fQCN, $primaryKey);
                }

                if ($fQCN instanceof PreLoadInterface) {
                    /** @var $fQCN string|PreLoadInterface */
                    $fQCN::preLoad($fQCN, $primaryKey);
                }
            }
        }

        $primaryKeysNotLoaded = $primaryKeysToLoad;

        foreach (di_get_tagged(ParameterInterface::TAG_ON_LOAD) as $service) {
            $service = di_get($service);
            if (!$service instanceof OnLoadInterface) {
                throw new DataException(sprintf(
                    '{c3680a93-cc0e-47d4-8d87-573398fcbc0d} Services tagged by the %s have to implement it.',
                    OnLoadInterface::class
                ));
            }

            foreach ($service->onLoad($fQCN, $primaryKeysNotLoaded) as $entity) {
                if ($entity instanceof EntityInterface) {
                    $primaryKey = $entity->getPrimaryKey();
                    $entities[$primaryKey] = $entity;

                    unset($primaryKeysNotLoaded[$primaryKey]);
                }
            }

            if (empty($primaryKeysNotLoaded)) {
                break;
            }
        }

        if (!empty($primaryKeysNotLoaded)) {
            throw new NoDataException(sprintf(
                '{33248032-3410-4e3d-af50-5dd3c810e750} Failed to load data for %s and primary keys [%s].',
                $fQCN,
                implode(', ', $primaryKeysNotLoaded)
            ));
        }

        foreach ($primaryKeysToLoad as $primaryKey) {
            $entity = $entities[$primaryKey];

            foreach (di_get_tagged(ParameterInterface::TAG_POST_LOAD) as $service) {
                $service = di_get($service);
                if (!$service instanceof PostLoadInterface) {
                    throw new DataException(sprintf(
                        '{44655d29-29aa-4e15-8807-353b49495573} Services tagged by the %s have to implement it.',
                        PostLoadInterface::class
                    ));
                }
                $service->postLoad($entity);
            }

            if ($entity instanceof PostLoadInterface) {
                /** @var $entity EntityInterface|PostLoadInterface */
                $entity->postLoad($entity);
            }

            $this->entities[$fQCN][$primaryKey] = $entity;
        }

        return $entities;
    }

    public function isLoaded(string $fQCN, string $primaryKey): bool
    {
        return isset($this->entities[$fQCN][$primaryKey]);
    }

    public function detach(string|null $fQCN = null, array|null $primaryKeys = null): void
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

    public function delete(array $entities, bool $force = false): void
    {
        $sortedEntities = [];

        foreach ($entities as $entity) {
            $sortedEntities[$entity::class][] = $entity->getPrimaryKey();
        }

        foreach ($sortedEntities as $fQCN => $primaryKeys) {
            $this->remove($fQCN, $primaryKeys, $force);
        }
    }

    public function remove(string $fQCN, array $primaryKeys, bool $force = false): void
    {
        if (!$force && $fQCN instanceof ImmutableEntityInterface) {
            throw new DataException(
                '{2055c126-1148-4c8b-ab1e-e607e9e919d4} The force flag has to been set in order to remove the data of an immutable entity.'
            );
        }

        foreach ($primaryKeys as $primaryKey) {
            foreach (di_get_tagged(ParameterInterface::TAG_PRE_REMOVE) as $service) {
                $service = di_static($service);
                if (!$service instanceof PreRemoveInterface) {
                    throw new DataException(sprintf(
                        '{19afae2d-5f2e-4308-9182-2e817f8c6349} Services tagged by the %s have to implement it.',
                        PreRemoveInterface::class
                    ));
                }
                $service::preRemove($fQCN, $primaryKey);
            }

            if ($fQCN instanceof PreRemoveInterface) {
                /** @var $fQCN string|PreRemoveInterface */
                $fQCN::preRemove($fQCN, $primaryKey);
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
            $service->onRemove($fQCN, $primaryKeys);
        }

        foreach ($primaryKeys as $primaryKey) {
            unset($this->entities[$fQCN][$primaryKey]);

            foreach (di_get_tagged(ParameterInterface::TAG_POST_REMOVE) as $service) {
                $service = di_static($service);
                if (!$service instanceof PostRemoveInterface) {
                    throw new DataException(sprintf(
                        '{174e73d8-efd9-475f-85d2-b1bfe3d4d008} Services tagged by the %s have to implement it.',
                        PostRemoveInterface::class
                    ));
                }
                $service::postRemove($fQCN, $primaryKey);
            }

            if ($fQCN instanceof PostRemoveInterface) {
                /** @var string|PostRemoveInterface $fQCN */
                $fQCN::postRemove($fQCN, $primaryKey);
            }
        }
    }
}
