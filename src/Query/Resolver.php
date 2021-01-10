<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/data
 * @link https://github.com/Koudela/eArc-data/
 * @copyright Copyright (c) 2019-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Data\Query;

use eArc\Data\Filesystem\StaticDirectoryService;
use eArc\Data\IndexHandling\Interfaces\IndexInterface;
use eArc\Data\IndexHandling\UseRedis\Helper\QueryRange;
use eArc\QueryLanguage\AbstractResolver;

class Resolver extends AbstractResolver
{
    protected function queryRelation(string $dataCategory, string $dataProperty, string $cmp, $value, ?iterable $allowedDataIdentifiers = null): iterable
    {
        switch ($cmp) {
            case 'IN':
            case 'NOT IN':
            case '=':
                return di_get(IndexInterface::class)->queryIndex($dataCategory, $dataProperty, $value);
            case '!=':
                return array_diff_key($this->findAll($dataCategory), di_get(IndexInterface::class)->queryIndex($dataCategory, $dataProperty, $value));
            case '<':
                new QueryRange($dataCategory, $dataProperty, null, $value, false, false);
            case '<=':
                new QueryRange($dataCategory, $dataProperty, null, $value, false, true);
            case '>':
                new QueryRange($dataCategory, $dataProperty, $value, null, false);
            case '>=':
                new QueryRange($dataCategory, $dataProperty, $value, null, true);
        }

        // TODO: ...
    }

    public function findAll(string $dataCategory): array
    {
        $all = [];

        foreach(scandir(di_static(StaticDirectoryService::class)::getPathFromClassName($dataCategory)) as $file) {
            if (substr($file, -4) === '.txt') {
                $primaryKey = substr($file, 0, -4);
                $all[$primaryKey] = $primaryKey;
            }
        }

        return $all;
    }

    public function sort(string $dataCategory, string $sort, iterable $dataPropertyNames, ?iterable $dataItems, ?iterable $allowedDataIdentifiers = null, int $limit = 0, int $offset = 0): iterable
    {
        // TODO: Implement sort() method.
    }
}
