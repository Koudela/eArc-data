<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Exceptions;

use eArc\Data\Exceptions\Interfaces\DataExceptionInterface;
use RuntimeException;

/**
 * Generic data exception.
 */
class DataException extends RuntimeException implements DataExceptionInterface
{
}
