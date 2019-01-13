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

use eArc\Data\Interfaces\Persistence\PersistableDataInterface;

class PersistableData implements PersistableDataInterface
{
    /** @var string|null */
    protected $identifier;

    /** @var mixed */
    protected $data;

    /**
     * @inheritdoc
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @inheritdoc
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @inheritdoc
     */
    public function set($data): void
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->data;
    }
}
