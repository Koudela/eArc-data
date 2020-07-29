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

//TODO: Query Language
//'TestEntityA = %s'; -> all ids from index where value = %s
//'TestEntityA IN [%s]'; -> all ids from index where value = %s OR ...
//'TestEntityA < %s'; -> all ids from index where value < %s
//'TestEntityA > %s'; -> all ids from index where value > %s
//'AND'; -> [ids result] in both [ids result]
// 'OR'; -> [ids result] in some [ids result]
// 'NOT'; -> NOT IN; !=;
// '(%)'; -> execute before
//'TestEntityA.property = %s';
//'TestEntityA.property IN [%s]';
//'TestEntityA.property < %s';
//'TestEntityA.property > %s';
//'TestEntityA JOIN TestEntityB.testEntityA_KeyProperty'; A ids mapped to B ids... in both
//'TestEntityA.testEntityB_keyProperty JOIN TestEntityB'; B ids mapped to A ids... in both

//(new Query(EntityName::class))->equal('property', 'value'))->AND()
use eArc\DataStoreTests\env\TestEntityA;
use eArc\DataStoreTests\env\TestEntityB;
$a = ['pk1' => ['color' => 'purple'], 'pk2' => ['color' => 'blue']];
$b = ['pk1' => ['color' => 'purple'], 'pk3' => ['color' => 'orange']];
//$a OR $b
$c = $a + $b;
//$a AND $b
$c = array_intersect_key($a, $b);
/**
 * @method static Relation where(string $fQCN, string $property)
 * @method static Conjunction from(string $fQCN)
 */
class Query {}

/**
 * @method Conjunction IN(array $value)
 * @method Conjunction equals($value)
 * @method Conjunction notEqual($value)
 * @method Conjunction lt($value)
 * @method Conjunction leq($value)
 * @method Conjunction gt($value)
 * @method Conjunction geq($value)
 * @method Join JOIN()
 */
class Relation {}

/**
 * @method Conjunction AND(Conjunction $query)
 * @method Conjunction OR(Conjunction $query)
 * @method Join JOIN(string $property)
 * @method Relation andWhere(string $property)
 * @method Relation orWhere(string $property)
 * @method limit(int $max)
 * @method sortAsc(string ...$property)
 * @method sortDesc(string ...$property)
 */
class Conjunction {}

/**
 * @method Conjunction ON(string $fQCN, string $property)
 */
class Join {}

Query::where(TestEntityA::class, 'color')->equals('purple')
    ->andWhere('material')->notEqual('uranium')
    ->JOIN('id')->ON(TestEntityB::class, 'a_id');
