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

    use eArc\Data\Entity\Interfaces\PrimaryKey\AutoPrimaryKeyInterface;
    use eArc\Data\Manager\Interfaces\Events\OnAutoPrimaryKeyInterface;
    use eArc\Data\Manager\Interfaces\Events\OnFindInterface;
    use eArc\Data\Manager\Interfaces\Events\OnLoadInterface;
    use eArc\Data\Manager\Interfaces\Events\OnPersistInterface;
    use eArc\Data\Manager\Interfaces\Events\OnRemoveInterface;
    use eArc\DataTests\env\Counter;
    use eArc\DataTests\env\EmptyEntity;
    use eArc\DataTests\env\ImmutableEntity;

    class MyDatabaseBridge implements OnLoadInterface, OnPersistInterface, OnRemoveInterface, OnFindInterface, OnAutoPrimaryKeyInterface
    {
        public int $onLoadCalledCorrectly = 0;
        public int $postLoadCallablesCalledCorrectly = 0;
        public int $onPersistCalledCorrectly = 0;
        public int $onRemoveCalledCorrectly = 0;
        public int $onFindCalledCorrectly = 0;
        public int $onAutoPrimaryKeyCalledCorrectly = 0;

        public function onLoad(string $fQCN, array $primaryKeys, array &$postLoadCallables): array
        {
            $this->onLoadCalledCorrectly = (++Counter::$cnt);

            $postLoadCallables[] = function (array $entities) {
                $this->postLoadCallablesCalledCorrectly = (++Counter::$cnt);
            };

            if ($fQCN === MyEntity::class) {
                return [0 => new MyEntity(array_values($primaryKeys)[0])];
            } elseif (array_key_exists('pk-already-persisted', $primaryKeys)) {
                return [0 => new ImmutableEntity(array_values($primaryKeys)[0])];
            } else {
                return [];
            }
        }

        public function onPersist(array $entities): void
        {
            $this->onPersistCalledCorrectly = (++Counter::$cnt);
        }

        public function onRemove(string $fQCN, array $primaryKeys): void
        {
            $this->onRemoveCalledCorrectly = (++Counter::$cnt);
        }

        public function onAutoPrimaryKey(AutoPrimaryKeyInterface $entity): string|null
        {
            $this->onAutoPrimaryKeyCalledCorrectly = (++Counter::$cnt);

            return md5(microtime(true).rand(0, 65536));
        }

        public function onFind(string $fQCN, array $keyValuePairs): array|null
        {
            $this->onFindCalledCorrectly = (++Counter::$cnt);

            return [];
        }
    }
}
