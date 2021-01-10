<?php declare(strict_types=1);
/**
* e-Arc Framework - the explicit Architecture Framework
*
* @package earc/data
* @link https://github.com/Koudela/eArc-data/
* @copyright Copyright (c) 2019-2021 Thomas Koudela
* @license http://opensource.org/licenses/MIT MIT License
*/

namespace eArc\Data\IndexHandling\UseFilesystem;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Index\IsIndexedInterface;
use eArc\Data\Filesystem\StaticDirectoryService;
use eArc\Data\IndexHandling\Interfaces\IndexInterface;
use eArc\Serializer\Exceptions\SerializeException;

class IndexFilesystem implements IndexInterface
{
    public function updateIndex(string $type, EntityInterface $entity, string $propertyName, $value): void
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

    public function queryIndex(string $dataCategory, string $dataProperty, string $value): string
    {
        // TODO: Implement queryIndex() method.
    }

    public function updateSortedIndex(string $type, EntityInterface $entity, string $propertyName, ?float $sort = 0): void
    {
        // TODO: Implement updateSortedIndex() method.
    }

    public function querySortedIndex(string $dataCategory, string $dataProperty, string $value, ?float $min = null, ?float $max = null): string
    {
        // TODO: Implement querySortedIndex() method.
    }
}
