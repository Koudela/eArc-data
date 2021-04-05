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

    use eArc\Data\Manager\Interfaces\Events\OnLoadInterface;
    use eArc\Data\Manager\Interfaces\Events\OnPersistInterface;
    use eArc\Data\Manager\Interfaces\Events\OnRemoveInterface;
    use eArc\DataTests\env\Counter;

    class MyCacheBridge implements OnLoadInterface, OnPersistInterface, OnRemoveInterface
    {
        public int $onLoadCalledCorrectly = 0;
        public int $postLoadCallablesCalledCorrectly = 0;
        public int $onPersistCalledCorrectly = 0;
        public int $onRemoveCalledCorrectly = 0;

        public function onLoad(string $fQCN, array $primaryKeys, array &$postLoadCallables): array
        {
            $this->onLoadCalledCorrectly = (++Counter::$cnt);

            $postLoadCallables[] = function (array $entities) {
                $this->postLoadCallablesCalledCorrectly = (++Counter::$cnt);
            };

            return [];
        }

        public function onPersist(array $entities): void
        {
            $this->onPersistCalledCorrectly = (++Counter::$cnt);
        }

        public function onRemove(string $fQCN, array $primaryKeys): void
        {
            $this->onRemoveCalledCorrectly = (++Counter::$cnt);
        }
    }
}


