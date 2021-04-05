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

class EmptyEntity extends AbstractEntity
{
    public function __construct(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }
}
