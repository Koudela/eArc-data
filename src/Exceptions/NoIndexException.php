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

use eArc\Data\Exceptions\Interfaces\NoIndexExceptionInterface;

/**
 * Is no index exception.
 */
class NoIndexException extends DataException implements NoIndexExceptionInterface
{
}
