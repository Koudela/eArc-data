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

interface OnRemoveInterface
{
    /**
     * Will be called for removing the data related to the entities.
     *
     * All tagged Services are called. Thus there can be services removing the
     * data from database(s), services removing the data from search indices and
     * services removing the data from shared memory cache, redis server or
     * other means.
     *
     * @param string $fQCN
     * @param string[] $primaryKeys
     */
    public function onRemove(string $fQCN, array $primaryKeys): void;
}
