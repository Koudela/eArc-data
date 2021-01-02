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

interface OnDeleteInterface
{
    /**
     * Returns an iterable of callbacks. These will be called prior deletion.
     *
     * Hint: Use this interface to cascade deletion.
     *
     * @return callable[]
     */
    public function getOnDeleteCallables(): array;
}
