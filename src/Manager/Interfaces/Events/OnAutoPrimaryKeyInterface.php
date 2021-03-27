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

use eArc\Data\Entity\Interfaces\PrimaryKey\AutoPrimaryKeyInterface;

interface OnAutoPrimaryKeyInterface
{
    /**
     * Will be called in order to generate a primary key, if a entity without
     * primary key and with `AutoPrimaryKeyInterface` is persisted.
     *
     * As soon as the one tagged Service returns an string result the other
     * registered services are skipped. Thus there is only one generated key
     * even if more services are applicable.
     *
     * @param AutoPrimaryKeyInterface $entity
     *
     * @return string|null
     */
    public function onAutoPrimaryKey(AutoPrimaryKeyInterface $entity): string|null;
}
