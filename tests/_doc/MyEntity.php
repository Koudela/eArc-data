<?php /** @noinspection PhpIllegalPsrClassPathInspection */ declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace {

    use eArc\Data\Entity\AbstractEntity;
    use eArc\Data\Entity\Interfaces\EntityInterface;
    use eArc\Data\Entity\Interfaces\Events\PostLoadInterface;
    use eArc\Data\Entity\Interfaces\Events\PostPersistInterface;
    use eArc\Data\Entity\Interfaces\Events\PostRemoveInterface;
    use eArc\Data\Entity\Interfaces\Events\PreLoadInterface;
    use eArc\Data\Entity\Interfaces\Events\PrePersistInterface;
    use eArc\Data\Entity\Interfaces\Events\PreRemoveInterface;
    use eArc\Data\Entity\Interfaces\PrimaryKey\AutoPrimaryKeyInterface;
    use eArc\DataTests\env\Counter;

    class MyEntity extends AbstractEntity implements PreLoadInterface, PostLoadInterface, PrePersistInterface, PostPersistInterface, PreRemoveInterface, PostRemoveInterface, AutoPrimaryKeyInterface
    {
        public static int $preLoadCalledCorrectly = 0;
        public int $postLoadCalledCorrectly = 0;
        public int $prePersistCalledCorrectly = 0;
        public int $postPersistCalledCorrectly = 0;
        public static int $preRemoveCalledCorrectly = 0;
        public static int $postRemoveCalledCorrectly = 0;
        public static string|null $pk = null;

        public function __construct(...$args)
        {
            if (array_key_exists(0, $args)) {
                $this->setPrimaryKey($args[0]);
            }
            unset($args);
        }

        public function setPrimaryKey(string|null $primaryKey): void
        {
            $this->primaryKey = $primaryKey;
            self::$pk = $primaryKey;
        }

        public function postLoad(EntityInterface $entity): void
        {
            $this->postLoadCalledCorrectly = $entity === $this ? (++Counter::$cnt) : 0;
        }

        public function postPersist(EntityInterface $entity): void
        {
            $this->postPersistCalledCorrectly = $entity === $this ? (++Counter::$cnt) : 0;
        }

        public static function postRemove(string $fQCN, string $primaryKey): void
        {
            self::$postRemoveCalledCorrectly = ($fQCN === self::class && self::$pk === $primaryKey) ? (++Counter::$cnt) : 0;
        }

        public static function preLoad(string $fQCN, string $primaryKey): void
        {
            self::$preLoadCalledCorrectly = ($fQCN === self::class && (self::$pk === $primaryKey || self::$pk === null)) ? (++Counter::$cnt) : 0;
        }

        public function prePersist(EntityInterface $entity): void
        {
            $this->prePersistCalledCorrectly = $entity === $this ? (++Counter::$cnt) : 0;
        }

        public static function preRemove(string $fQCN, string $primaryKey): void
        {
            self::$preRemoveCalledCorrectly = ($fQCN === self::class && self::$pk === $primaryKey) ? (++Counter::$cnt) : 0;
        }
    }
}


