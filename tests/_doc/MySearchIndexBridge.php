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

    use eArc\Data\Manager\Interfaces\Events\OnFindInterface;
    use eArc\Data\Manager\Interfaces\Events\OnPersistInterface;
    use eArc\Data\Manager\Interfaces\Events\OnRemoveInterface;
    use eArc\DataTests\env\Counter;

    class MySearchIndexBridge implements OnPersistInterface, OnRemoveInterface, OnFindInterface
    {
        public int $onPersistCalledCorrectly = 0;
        public int $onRemoveCalledCorrectly = 0;
        public int $onFindCalledCorrectly = 0;

        public function onPersist(array $entities): void
        {
            $this->onPersistCalledCorrectly = (++Counter::$cnt);
        }

        public function onRemove(string $fQCN, array $primaryKeys): void
        {
            $this->onRemoveCalledCorrectly = (++Counter::$cnt);
        }

        public function onFind(string $fQCN, array $keyValuePairs): array|null
        {
            $this->onFindCalledCorrectly = (++Counter::$cnt);

            return null;
        }
    }
}
