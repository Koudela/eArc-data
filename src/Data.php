<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data;

use eArc\Data\Interfaces\Application\DataInterface;
use eArc\Data\Interfaces\Persistence\PersistableDataInterface;

/**
 * Data class.
 */
class Data implements DataInterface
{
    /** @var PersistableDataInterface */
    protected $persistableData;

    /**
     * @param PersistableDataInterface $persistableData
     */
    public function __construct(PersistableDataInterface $persistableData)
    {
        $this->persistableData = $persistableData;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier(): ?string
    {
        $this->persistableData->getIdentifier();
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        $this->persistableData->get();
    }

    /**
     * @inheritdoc
     */
    public function set($data): void
    {
        $this->persistableData->set($data);
    }

    /**
     * @inheritdoc
     */
    public function expose(): PersistableDataInterface
    {
        return $this->persistableData;
    }
}
