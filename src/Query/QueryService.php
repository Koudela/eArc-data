<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data-store
 * @link https://github.com/Koudela/eArc-data-store/
 * @copyright Copyright (c) 2019-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\DataStore\Query;

use eArc\DataStore\Entity\Interfaces\Index\IsIndexedInterface;
use eArc\DataStore\Exceptions\Interfaces\QueryExceptionInterface;
use eArc\DataStore\Exceptions\QueryException;
use eArc\DataStore\Filesystem\StaticDirectoryService;
use eArc\DataStore\Query\Interfaces\QueryServiceInterface;

class QueryService implements QueryServiceInterface
{
    /**
     * @param string $fQCN
     * @param string|null $query
     * @param array|null $allowedPrimaryKeys
     *
     * @return array
     *
     * @throws QueryExceptionInterface
     */
    public function find(string $fQCN, ?string $query = null, ?array $allowedPrimaryKeys = null): array
    {
        if (!$query) {
            $allKeys = $this->findAll($fQCN);

            if (null === $allowedPrimaryKeys) {
                return $allKeys;
            }

            return array_intersect_key($allKeys, $allowedPrimaryKeys);
        }

        if (!$fQCN instanceof IsIndexedInterface) {
            throw new QueryException(sprintf('%s does not implement the %s.', $fQCN, IsIndexedInterface::class));
        }

//        $indexedProperties = $fQCN::getIndexedProperties();
//
//        foreach ($keyValuePairs as $key => $value) {
//            if (!array_key_exists($key, $indexedProperties)) {
//                throw new NoIndexException(sprintf('%s is not a key of %s.', $key, $fQCN));
//            }
//        }

        return [];
    }

    public function findBy(string $fQCN, array $keyValuePairs): array
    {
        // TODO: Implement findBy() method.

        return [];
    }

    protected function findAll(string $fQCN): array
    {
        $all = [];

        foreach(scandir(di_static(StaticDirectoryService::class)::getPathFromClassName($fQCN)) as $file) {
            if (substr($file, -4) === '.txt') {
                $primaryKey = substr($file, 0, -4);
                $all[$primaryKey] = $primaryKey;
            }
        }

        return $all;
    }
}
