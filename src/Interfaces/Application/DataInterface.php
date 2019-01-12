<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Interfaces\Application;

use eArc\Data\Interfaces\Persistence\PersistableDataInterface;

/**
 * Data application interface.
 */
interface DataInterface
{
    /**
     * Get the identifier of the object/data. A string that is unique to the
     * type of object/data. May return null if the persisted data the object
     * belongs to does not exist yet.
     *
     * @return string|null
     */
    public function getIdentifier(): ?string;

    /**
     * Get the data of the object.
     *
     * @return mixed
     */
    public function get();

    /**
     * Set the data of the object.
     *
     * @param mixed $data
     */
    public function set($data): void;

    /**
     * Expose the persistable part of the object. (Warning: Do not use this
     * method in the application context.)
     *
     * @return PersistableDataInterface
     */
    public function expose(): PersistableDataInterface;
}
