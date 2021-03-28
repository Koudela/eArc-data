<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity\Interfaces;

use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;

/**
 * Identifies an entity being a mutable reference. Must not implement the
 * `ImmutableEntityReferenceInterface`.
 *
 * Hint: Counterpart of the `MutableReverenceKeyInterface`.
 */
interface MutableEntityReferenceInterface extends EntityInterface
{
    public function setMutableReverenceTarget(MutableReverenceKeyInterface $entity): void;
}
