<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Repository;

use eArc\DataStore\Repository\Interfaces\RepositoryInterface;
use function eArc\DataStore\Manager\data_find;

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
