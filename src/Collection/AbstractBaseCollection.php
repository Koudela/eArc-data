<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Collection;

use eArc\Data\Collection\Interfaces\CollectionBaseInterface;
use eArc\Data\Entity\Interfaces\EntityBaseInterface;
use eArc\Data\Repository\Interfaces\RepositoryBaseInterface;
use Generator;

abstract class AbstractBaseCollection implements CollectionBaseInterface, RepositoryBaseInterface
{
    /** @var EntityBaseInterface */
    protected $owner;
    /** @var string */
    protected $fQCN;
    /** @var array */
    protected $items;

    /**
     * @param EntityBaseInterface $owner
     * @param string $fQCN
     */
    public function __construct(EntityBaseInterface $owner, string $fQCN)
    {
        $this->owner = $owner;
        $this->fQCN = $fQCN;
    }

    public function getOwner(): EntityBaseInterface
    {
        return $this->owner;
    }

    public function asArray(): array
    {
        return $this->items;
    }

    public function getIterator(): Generator
    {
        foreach ($this->items as $key => $value) {
            yield $key => $value;
        }
    }

    public function getEntityName(): string
    {
        return $this->fQCN;
    }
}
