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
use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Data\Entity\Interfaces\Events\PostPersistInterface;
use eArc\Data\Entity\Interfaces\MutableEntityReferenceInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;

class ImmutableProxy extends AbstractEntity implements MutableEntityReferenceInterface, PostPersistInterface
{
    public string|null $referencePK;
    public string|null $lastPersistedReferencePK;

    public function __construct(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function setMutableReverenceTarget(MutableReverenceKeyInterface $entity): void
    {
        $this->referencePK = $entity->getPrimaryKey();
    }

    public function postPersist(EntityInterface $entity): void
    {
        $this->lastPersistedReferencePK = $this->referencePK;
    }

    public function getLastPersistedReferencePK(): string
    {
        return $this->lastPersistedReferencePK;
    }
}
