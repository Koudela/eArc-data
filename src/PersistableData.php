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
     * Get the identifier of the object/data. A string that is unique to the
     * type of object/data. May return null if the persisted data the object
     * belongs to does not exist yet.
     *
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * Set the identifier of the object/data.
     *
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Set the data of the object.
     *
     * @param mixed $data
     */
    public function set($data): void
    {
        $this->data = $data;
    }

    /**
     * Get the data of the object.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->data;
    }
}
