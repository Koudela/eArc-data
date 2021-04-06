<?php /** @noinspection PhpIllegalPsrClassPathInspection */ declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataTests\env;

use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Entity\GenericMutableEntityReference;
use eArc\Data\Entity\Interfaces\ImmutableEntityInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;

class ReverencedByGenericImmutableEntity extends AbstractEntity implements MutableReverenceKeyInterface, ImmutableEntityInterface
{
    protected string|null $mutableReferencePrimaryKey;

    public function __construct(string $primaryKey, GenericMutableEntityReference|null $mutableReference = null)
    {
        $this->primaryKey = $primaryKey;

        if (is_null($mutableReference)) {
            $mutableReference = new GenericMutableEntityReference();
        }

        if (is_null($mutableReference->getPrimaryKey())) {
            data_persist($mutableReference);
        }

        $this->mutableReferencePrimaryKey = $mutableReference->getPrimaryKey();
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
