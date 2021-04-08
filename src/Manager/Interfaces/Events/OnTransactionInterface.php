<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Manager\Interfaces\Events;

use eArc\Data\Exceptions\Interfaces\NoDataExceptionInterface;

interface OnTransactionInterface
{
    const TYPE_PERSIST = 'persist';
    const TYPE_REMOVE = 'remove';

    /**
     * Will be called at the beginning of when `data_transaction_commit` is called
     * after `data_transaction_start`.
     *
     * @param array<int, array> $items The inner array has the keys `type` and `entities`
     * if `type` is `TYPE_PERSIST and `fQCN`, `primaryKeys`, `force` if `type` is remove.
     *
     * @return string transaction identifier
     */
    public function onTransactionCommit(array $items): string;

    /**
     * Will be called when `data_transaction_commit` has finished. Is not allowed
     * to throw an error.
     *
     * @param string $transactionIdentifier
     */
    public function onTransactionEnd(string $transactionIdentifier): void;

    /**
     * Will be called at the beginning of a rollback.
     *
     * @param string $transactionIdentifier
     *
     * @return array items related to the transaction identifier
     *
     * @throws NoDataExceptionInterface if the transaction identifier does not exist
     * or is not active anymore.
     */
    public function onRollback(string $transactionIdentifier): array;
}
