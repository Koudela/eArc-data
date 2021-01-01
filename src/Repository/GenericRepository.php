<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Repository;

use eArc\Data\Repository\Interfaces\RepositoryInterface;
use function eArc\Data\Manager\data_find;

class GenericRepository implements RepositoryInterface
{
    /** @var string */
    protected $fQCN;

    public function __construct(string $fQCN)
    {
        $this->fQCN = $fQCN;
    }

    public function getEntityName(): string
    {
        return $this->fQCN;
    }

    public function find(?string $query = null): array
    {
        return data_find($this->fQCN, $query);
    }
}
