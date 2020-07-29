<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Collection;

use eArc\DataStore\Collection\Interfaces\CollectionBaseInterface;
use eArc\DataStore\Entity\Interfaces\EntityBaseInterface;
use eArc\DataStore\Repository\Interfaces\RepositoryBaseInterface;

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
    public function __construct($owner, string $fQCN)
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

    public function getIterator()
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
