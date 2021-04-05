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

use eArc\Data\Exceptions\Interfaces\QueryExceptionInterface;

interface OnFindInterface
{
    /**
     * Will be called in order to retrieve the result of the data_find function.
     *
     * As soon as the one tagged Service returns an array the other registered
     * services and callables are skipped. Thus for example if the result is found
     * in the search index it need not to be looked up in the cache or database.
     *
     * Returns the primary keys for the key value pairs based on the
     * properties of the entities from the class. If the key value
     * pairs are empty all primary keys are returned. Key value pairs
     * are joint via logic `AND`. Value arrays are interpreted as `IN`.
     * Not all key value pairs or value arrays may be supported. It
     * depends on the used infrastructure, the setting (for example
     * the usable sql indices) and the implementation of the bridge.
     * If one or more key value pairs are not supported a query
     * exception is thrown.
     *
     * The bridge may extend this search syntax.
     *
     * Beside this function there may be more ways to search for entities. These
     * are not part of the earc/data abstraction.
     *
     * @param string $fQCN
     * @param array $keyValuePairs
     *
     * @return string[]|null
     *
     * @throws QueryExceptionInterface
     */
    public function onFind(string $fQCN, array $keyValuePairs): array|null;
}
