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
use eArc\Data\Entity\Interfaces\ImmutableEntityInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\AutoPrimaryKeyInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;

class ReverencedImmutableEntity extends AbstractEntity implements ImmutableEntityInterface, AutoPrimaryKeyInterface, MutableReverenceKeyInterface
{
    protected null|string $immutableProxyPK;

    public function __construct(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function setPrimaryKey(?string $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }

    public function getMutableReverenceKey(): string
    {
        return $this->immutableProxyPK;
    }

    public function getMutableReverenceClass(): string
    {
        return ImmutableProxy::class;
    }

    public function setProxy(ImmutableProxy $entity): void
    {
        $this->immutableProxyPK = $entity->getPrimaryKey();
    }
}
