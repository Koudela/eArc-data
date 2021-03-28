<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Entity;

use eArc\Data\Entity\Interfaces\ImmutableEntityInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;

abstract class AbstractImmutableMutableReferencedEntity extends AbstractEntity implements ImmutableEntityInterface, MutableReverenceKeyInterface
{
    protected string $mutableReferencePrimaryKey;

    public function __construct(string $mutableReferencePrimaryKey)
    {
        $this->mutableReferencePrimaryKey = $mutableReferencePrimaryKey;
    }

    public function getMutableReverenceKey(): string
    {
        return $this->mutableReferencePrimaryKey;
    }

    public function getMutableReverenceClass(): string
    {
        return GenericMutableEntityReference::class;
    }
}
