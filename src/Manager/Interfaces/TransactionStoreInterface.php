<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager\Interfaces;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Exceptions\Interfaces\NoDataExceptionInterface;

interface TransactionStoreInterface
{
    /**
     * Returns true if a transaction is started and false otherwise.
     *
     * @return bool
     */
    public function transactionIsOpen(): bool;

    /**
     * Adds entities to a transaction for persistence.
     *
     * @param EntityInterface[] $entities
     */
    public function transactionAddPersistItem(array $entities): void;

    /**
     * Adds entities to a transaction for removal.
     *
     * @param string $fQCN
     * @param array $primaryKeys
     * @param bool $force
     */
    public function transactionAddRemoveItem(string $fQCN, array $primaryKeys, bool $force): void;

    /**
     * Starts a transaction. Returns false if a transaction is already open and
     * true otherwise.
     *
     * @return bool
     */
    public function transactionStart(): bool;

    /**
     * Commits a transaction.
     */
    public function transactionCommit(): void;

    /**
     * Triggers the rollback of a transaction. Any open transaction that is not
     * committed yet, will be cleared. Does nothing if there is no open transaction.
     */
    public function transactionRollback(): void;

    /**
     * Closes an open transaction and clears its content. Does nothing if there
     * is no open transaction.
     */
    public function transactionClear(): void;
}
