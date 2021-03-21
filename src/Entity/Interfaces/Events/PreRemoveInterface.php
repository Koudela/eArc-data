<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces\Events;

interface PreRemoveInterface
{
    /**
     * Returns an iterable of callbacks. These will be called before the removal
     * of the entities data.
     *
     * Hint 1: This method is static as entities can be removed without loading
     * them.
     *
     * Hint 2: Use this interface in conjunction with the data_load function
     * to cascade deletion.
     *
     * @return callable[]
     */
    public static function getPreRemoveCallables(): iterable;
}
