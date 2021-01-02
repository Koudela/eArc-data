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
use eArc\QueryLanguage\AbstractResolver;

class Resolver extends AbstractResolver
{
    protected function queryRelation(string $dataCategory, string $dataProperty, string $cmp, $value, ?iterable $allowedDataIdentifiers = null): iterable
    {
        switch ($cmp) {
            case 'IN':
            case 'NOT IN':
            case '=':
            case '!=':
            case '<':
            case '<=':
            case '>':
            case '>=':
        }

        // TODO: ...
    }

    public function findAll(string $dataCategory): iterable
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
