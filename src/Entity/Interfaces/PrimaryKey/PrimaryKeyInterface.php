<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces\PrimaryKey;

interface PrimaryKeyInterface
{
    /**
     * Get the primary key of the entity. A string that is unique to the type of
     * object. May return null if the persisted data the object belongs to does
     * not exist yet.
     *
     * @return string|null
     */
    public function getPrimaryKey(): string|null;
}
