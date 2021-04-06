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

class ImmutableAutoPrimaryKeyEntity extends AbstractEntity implements ImmutableEntityInterface, AutoPrimaryKeyInterface
{
    public function __construct(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function setPrimaryKey(?string $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }
}
