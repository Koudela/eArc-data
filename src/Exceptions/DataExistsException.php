<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Exceptions;

use eArc\Data\Interfaces\Exceptions\DataExistsExceptionInterface;

/**
 * Data exists exception.
 */
class DataExistsException extends DataException implements DataExistsExceptionInterface
{
}
