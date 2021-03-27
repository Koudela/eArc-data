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
     * @param string $fQCN
     * @param array $keyValuePairs
     *
     * @return string[]|null
     *
     * @throws QueryExceptionInterface
     */
    public function onFind(string $fQCN, array $keyValuePairs): array|null;
}
