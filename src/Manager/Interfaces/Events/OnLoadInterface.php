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

use eArc\Data\Entity\Interfaces\EntityInterface;

interface OnLoadInterface
{
    /**
     * Will be called for creating the entities from the data.
     *
     * As soon as the one tagged Service returns an entity the other registered
     * services and callables are skipped. Thus for example if the entity is found
     * in shared memory it does not need to be looked up in the database.
     *
     * @param string $fQCN
     * @param string[] $primaryKeys
     *
     * @return EntityInterface[]
     */
    public function onLoad(string $fQCN, array $primaryKeys): array;
}
