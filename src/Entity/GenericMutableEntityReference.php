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

use eArc\Data\Entity\Interfaces\MutableEntityReferenceInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\AutoPrimaryKeyInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;
use function data_load;

class GenericMutableEntityReference implements MutableEntityReferenceInterface, AutoPrimaryKeyInterface
{
    protected string|null $primaryKey = null;
    protected string|null $mutableReverenceKeyEntityPK;
    protected string|null $mutableReverenceKeyEntityFQCN;

    public function getPrimaryKey(): string|null
    {
        return $this->primaryKey;
    }

    public function setPrimaryKey(string|null $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }

    public function setMutableReverenceTarget(MutableReverenceKeyInterface $entity): void
    {
        $this->mutableReverenceKeyEntityFQCN = $entity::class;
        $this->mutableReverenceKeyEntityPK = $entity->getPrimaryKey();
   }

    public function getMutableReverenceTarget(): MutableReverenceKeyInterface|null
    {
        return is_null($this->mutableReverenceKeyEntityPK)
            ? null
            : data_load($this->mutableReverenceKeyEntityFQCN, $this->mutableReverenceKeyEntityPK);
    }
}
