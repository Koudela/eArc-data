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

use eArc\Data\Exceptions\DataException;
use eArc\Data\Manager\Interfaces\DataStoreInterface;
use eArc\Data\Manager\Interfaces\EntitySaveStackInterface;
use eArc\Data\Manager\Interfaces\Events\OnTransactionInterface;
use eArc\Data\Manager\Interfaces\TransactionStoreInterface;
use eArc\Data\ParameterInterface;
use Exception;

class TransactionStore implements TransactionStoreInterface
{
    protected EntitySaveStackInterface|null $entitySaveStack = null;
    protected DataStoreInterface|null $dataStore = null;
    protected array|null $transaction = null;

    /** @var array<string, OnTransactionInterface> */
    protected array $transactionKeys = [];

    public function transactionIsOpen(): bool
    {
        return is_array($this->transaction);
    }

    public function transactionAddPersistItem(array $entities): void
    {
        if (!$this->transactionIsOpen()) {
            throw new DataException('{e4dd5f18-093b-4d0d-9aa5-3869be7b2c0e} There is no open transaction.');
        }

        $this->transaction[] = [
            'type' => OnTransactionInterface::TYPE_PERSIST,
            'entities' => $entities,
        ];
    }

    public function transactionAddRemoveItem(string $fQCN, array $primaryKeys, bool $force): void
    {
        if (!$this->transactionIsOpen()) {
            throw new DataException('{578f0329-0b47-453a-bf37-ff8868ac4961} There is no open transaction.');
        }

        $this->transaction[] = [
            'type' => OnTransactionInterface::TYPE_REMOVE,
            'fQCN' => $fQCN,
            'primaryKeys' => $primaryKeys,
            'force' => $force
        ];

    }

    public function transactionStart(): bool
    {
        if (!$this->transactionIsOpen()) {
            $this->transactionKeys = [];
            $this->transaction = [];

            return true;
        }

        return false;
    }

    public function transactionCommit(): void
    {
        foreach (di_get_tagged(ParameterInterface::TAG_ON_TRANSACTION) as $service => $args) {
            $service = di_static($service);
            if (!is_subclass_of($service, OnTransactionInterface::class)) {
                throw new DataException(sprintf(
                    '{06e6f0ae-2eea-49fd-837c-2b8862f5198d} Service %s tagged by the interface %s has to implement it.',
                    $service,
                    OnTransactionInterface::class
                ));
            }
            $this->transactionKeys[$service->onTransactionCommit($this->transaction)] = $service;
        }

        $this->processStack($this->transaction);

        $this->transactionClear();
    }

    public function transactionRollback(): void
    {
        $rollback = [];

        foreach ($this->transactionKeys as $id => $service) {
            $rollback = array_merge($rollback, $service->onRollback($id));
        }

        $this->processStack($rollback);

        $this->transaction = null;
        $this->transactionKeys = [];
    }

    public function transactionClear(): void
    {
        foreach ($this->transactionKeys as $id => $service) {
            try {
                $service->onTransactionEnd($id);
            } catch (Exception $exception) {
                unset($exception);
            }
        }

        $this->transaction = null;
        $this->transactionKeys = [];
    }

    protected function processStack(array $stack): void
    {
        if (is_null($this->entitySaveStack)) {
            $this->entitySaveStack = di_get(EntitySaveStack::class);
            $this->dataStore = di_get(DataStore::class);
        }

        foreach ($stack as $item) {
            switch ($item['type']) {
                case OnTransactionInterface::TYPE_REMOVE:
                    $this->entitySaveStack->directPersist($item['entities']);
                    break;
                case OnTransactionInterface::TYPE_PERSIST:
                    $this->dataStore->remove($item['fQCN'], $item['primaryKeys'], $item['force']);
                    break;
            }
        }
    }
}
