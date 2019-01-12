<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/earc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Interfaces\Exceptions;

use eArc\Data\Exceptions\DataException;

/**
 * Data exists exception.
 */
class DataExistsException extends DataException implements DataExistsExceptionInterface
{
}
