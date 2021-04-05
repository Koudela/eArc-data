<?php /** @noinspection PhpIllegalPsrClassPathInspection */ declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataTests\env;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Events\PostLoadInterface;
use eArc\Data\Entity\Interfaces\Events\PostPersistInterface;
use eArc\Data\Entity\Interfaces\Events\PostRemoveInterface;
use eArc\Data\Entity\Interfaces\Events\PreLoadInterface;
use eArc\Data\Entity\Interfaces\Events\PrePersistInterface;
use eArc\Data\Entity\Interfaces\Events\PreRemoveInterface;

class TaggedService implements PreLoadInterface, PostLoadInterface, PrePersistInterface, PostPersistInterface, PreRemoveInterface, PostRemoveInterface
{
    public static int $preLoadCalledCorrectly = 0;
    public int $postLoadCalledCorrectly = 0;
    public int $prePersistCalledCorrectly = 0;
    public int $postPersistCalledCorrectly = 0;
    public static int $preRemoveCalledCorrectly = 0;
    public static int $postRemoveCalledCorrectly = 0;
    public static string|null $pk = null;

    public function postLoad(EntityInterface $entity): void
    {
        $this->postLoadCalledCorrectly = (++Counter::$cnt);
    }

    public function postPersist(EntityInterface $entity): void
    {
        $this->postPersistCalledCorrectly = (++Counter::$cnt);
    }

    public static function postRemove(string $fQCN, string $primaryKey): void
    {
        self::$postRemoveCalledCorrectly = (++Counter::$cnt);
    }

    public static function preLoad(string $fQCN, string $primaryKey): void
    {
        self::$preLoadCalledCorrectly = (++Counter::$cnt);
    }

    public function prePersist(EntityInterface $entity): void
    {
        $this->prePersistCalledCorrectly = (++Counter::$cnt);
    }

    public static function preRemove(string $fQCN, string $primaryKey): void
    {
        self::$preRemoveCalledCorrectly = (++Counter::$cnt);
    }
}
