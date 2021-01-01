<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Filesystem;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Exceptions\Interfaces\NoDataExceptionInterface;
use eArc\Data\Exceptions\NoDataException;
use eArc\Data\Filesystem\Interfaces\PersistenceInterface;
use eArc\Data\Manager\StaticEntitySaveStack;
use eArc\Data\Serialization\SerializerType;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;
use eArc\Serializer\Exceptions\SerializeException;
use eArc\Serializer\Services\FactoryService;
use eArc\Serializer\Services\SerializeService;

abstract class StaticPersistenceService implements PersistenceInterface
{
    /**
     * @param EntityInterface $entity
     *
     * @throws SerializeExceptionInterface
     */
    public static function persist(EntityInterface $entity): void
    {
        $array = di_get(SerializeService::class)->getAsArray($entity, di_get(SerializerType::class));

        di_static(StaticEntitySaveStack::class)::beforeEntitySaved($entity);
        di_static(StaticDirectoryService::class)::forceChdir($entity);
        file_put_contents(di_static(StaticNamingService::class)::getQualifiedFilenameForEntity($entity), json_encode($array), LOCK_EX);
        di_static(StaticEntitySaveStack::class)::afterEntitySaved($entity);
    }

    public static function remove(EntityInterface $entity): void
    {
        unlink(di_static(StaticNamingService::class)::getQualifiedFilenameForEntity($entity));
        di_static(StaticEntitySaveStack::class)::afterEntityRemoved($entity);
    }

    public static function load(string $fQCN, string $primaryKey): EntityInterface
    {
        if (!class_exists($fQCN)) {
            throw new SerializeException(sprintf(
                'Only entities mapped by existing classes can be loaded. Class %s does not exists.',
                $fQCN
            ));
        }

        if (!is_subclass_of($fQCN, EntityInterface::class, true)) {
            throw new SerializeException(sprintf(
                'Only entities can be loaded. But %s does not implement %s.',
                $fQCN,
                EntityInterface::class
            ));
        }

        $rawContent = self::loadRawContent($fQCN, $primaryKey);

        $entity = di_get(FactoryService::class)->initObject($fQCN);
        di_get(FactoryService::class)->attachProperties($entity, $rawContent, di_get(SerializerType::class));

        if (!$entity instanceof $fQCN) {
            throw new SerializeException('Loading does not yield the correct entity class.');
        }

        return $entity;
    }

    /**
     * @param string $fQCN
     * @param string $primaryKey
     *
     * @return array
     *
     * @throws NoDataExceptionInterface|SerializeExceptionInterface
     */
    protected static function loadRawContent(string $fQCN, string $primaryKey): array
    {
        $absoluteFilePath = di_static(StaticNamingService::class)::getQualifiedFilename($fQCN, $primaryKey);

        if (!$content = file_get_contents($absoluteFilePath)) {
            throw new NoDataException(sprintf('Failed to load data for %s - %s.', $fQCN, $primaryKey));
        }

        if (!$array = json_decode($content, true)) {
            throw new SerializeException(sprintf('Failed to decode data for %s - %s.', $fQCN, $primaryKey));
        }

        return $array;
    }
}
