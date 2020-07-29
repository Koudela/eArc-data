<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Manager\Interfaces;

use eArc\DataStore\Entity\Interfaces\EntityInterface;
use eArc\DataStore\Entity\Interfaces\PrimaryKey\PrimaryKeyInterface;
use eArc\DataStore\Repository\Interfaces\RepositoryBaseInterface;

interface EntityProxyInterface extends PrimaryKeyInterface, RepositoryBaseInterface
{
    public function load(?string $typeHint = null): EntityInterface;
}
