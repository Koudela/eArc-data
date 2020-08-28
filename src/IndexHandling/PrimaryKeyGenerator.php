<?php declare(strict_types=1);
/**
* e-Arc Framework - the explicit Architecture Framework
*
* @package earc/data
* @link https://github.com/Koudela/eArc-data/
* @copyright Copyright (c) 2019-2020 Thomas Koudela
* @license http://opensource.org/licenses/MIT MIT License
*/

namespace eArc\Data\IndexHandling;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\AutoincrementPrimaryKeyInterface;
use eArc\Data\Filesystem\StaticDirectoryService;
use eArc\Serializer\Exceptions\Interfaces\SerializeExceptionInterface;
use eArc\Serializer\Exceptions\SerializeException;

class PrimaryKeyGenerator
{
    public static function getNextPrimaryKey(EntityInterface $entity): string
    {
        return $entity instanceof AutoincrementPrimaryKeyInterface ?
            self::getAutoIncrementId($entity) : self::getUuid();
    }

    /**
     * @param EntityInterface $entity
     *
     * @return string
     *
     * @throws SerializeExceptionInterface
     */
    protected static function getAutoIncrementId(EntityInterface $entity): string
    {
        di_static(StaticDirectoryService::class)::forceChdir($entity, '@generator');

        $filename = 'auto-increment-id.txt';

        if (!$handle = fopen($filename, 'c+')) {
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

        $content = (string) fgets($handle);
        $id = !strlen($content) ? '0' : (string) ((int) $content + 1);

        rewind($handle);
        fputs($handle, $id);

        flock($handle, LOCK_UN);
        fclose($handle);

        return $id;
    }

    protected static function getUuid(): string
    {
        return exec('cat /proc/sys/kernel/random/uuid');
    }
}
