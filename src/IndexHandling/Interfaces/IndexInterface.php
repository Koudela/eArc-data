<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\IndexHandling\Interfaces;

use eArc\Data\Entity\Interfaces\EntityInterface;
use eArc\Serializer\Exceptions\SerializeException;

interface IndexInterface
{
    /**
     * @param string $type
     * @param EntityInterface $entity
     * @param string $propertyName
     * @param int|float|string|null $value
     *
     * @throws SerializeException
     */
    public function updateIndex(string $type, EntityInterface $entity, string $propertyName, $value): void;
    public function queryIndex(string $dataCategory, string $dataProperty, string $value): string;
    public function querySortedIndex(string $dataCategory, string $dataProperty, string $value, ?float $min = null, ?float $max = null): string;
}
