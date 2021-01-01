<?php declare(strict_types=1);
/**
* e-Arc Framework - the explicit Architecture Framework
*
* @package earc/data
* @link https://github.com/Koudela/eArc-data/
* @copyright Copyright (c) 2019-2021 Thomas Koudela
* @license http://opensource.org/licenses/MIT MIT License
*/

namespace eArc\Data\IndexHandling;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Index\IsIndexedInterface;
use eArc\Data\Filesystem\StaticDirectoryService;
use eArc\Serializer\Exceptions\SerializeException;
use ReflectionClass;
use ReflectionException;

class IndexEventHandler
{
    /**
     * This method is called every time before(!) an entity is saved. The
     * ordering matters, since a unique index violation has to be detected
     * before the entity is saved.
     *
     * @param EntityInterface $entity
     *
     * @throws SerializeException|ReflectionException
     */
    public static function beforeEntitySaved(EntityInterface $entity): void
    {
        if ($entity instanceof IsIndexedInterface) {
            $reflectionEntity = new ReflectionClass($entity);
            foreach ($entity::getIndexedProperties() as $propertyName => $type) {
                $property = $reflectionEntity->getProperty($propertyName);
                $property->setAccessible(true);
                $value = (string) $property->getValue($entity);
                self::updateIndex($type, $entity, $propertyName, $value);
            }
        }
    }

    protected static function updateIndex(string $type, EntityInterface $entity, string $propertyName, $value)
    {
        di_static(StaticDirectoryService::class)::forceChdir($entity, '@'.$type);
        $filename = $propertyName.'.txt';

        if (!$handle = fopen($filename, 'c^+')) {
            throw new SerializeException(sprintf(
                'Cannot open file %s.',
                getcwd().'/'.$filename
            ));
        }

        if (!flock($handle, LOCK_EX)) {
            throw new SerializeException(sprintf(
                'Cannot acquire lock for %s.',
                getcwd().'/'.$filename
            ));
        }

        self::updateIndexCSV($handle,$type === IsIndexedInterface::TYPE_UNIQUE, $propertyName, $entity->getPrimaryKey(), $value);

        flock($handle, LOCK_UN);
        fclose($handle);
    }

    protected static function updateIndexCSV($handle, bool $unique, string $propertyName, string $primaryKey, ?string $value)
    {
        $index = [];

        while ($array = fgetcsv($handle)) {
            foreach (array_keys($array, $primaryKey, true) as $key) {
                if ($key !== 0) {
                    unset($array[$key]);
                }
            }

            if ($array[0] === $value) {
                $array[] = $primaryKey;
            }

            if (count($array) > 1) {
                $index[$array[0]] = $array;
            }

            if ($unique && count($array) > 2) {
                throw new SerializeException(sprintf(
                    'Unique index %s for value %s is violated by primary key %s (old) and %s (new)',
                    $propertyName,
                    $value,
                    $array[1],
                    $primaryKey
                ));
            }
        }

        if (null !== $value && !array_key_exists($value, $index)) {
            $index[$value] = [$value, $primaryKey];
        }

        ftruncate($handle, 0);
        rewind($handle);

        foreach ($index as $line) {
            fputcsv($handle, $line);
        }
    }

    /**
     * This method is called every time an entity is removed.
     *
     * @param EntityInterface $entity
     *
     * @throws SerializeException
     */
    public static function afterEntityRemoved(EntityInterface $entity): void
    {
        if ($entity instanceof IsIndexedInterface) {
            foreach ($entity::getIndexedProperties() as $propertyName => $type) {
                self::updateIndex($type, $entity, $propertyName, null);
            }
        }
    }
}
