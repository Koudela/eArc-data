<?php declare(strict_types=1);

use eArc\Data\IndexHandling\Interfaces\IndexInterface;
use eArc\Data\IndexHandling\UseRedis\IndexRedis;
use eArc\Data\Manager\DataStore;
use eArc\DataTests\env\TestEntityB;
use eArc\DI\DI;
use function eArc\Data\Manager\data_delete;
use function eArc\Data\Manager\data_find;
use function eArc\Data\Manager\data_load;
use function eArc\Data\Manager\data_save;

include __DIR__.'/../vendor/autoload.php';

DI::init();
di_decorate(IndexInterface::class, IndexRedis::class);
di_set_param('earc.data.path', __DIR__.'/data/');
di_get(DataStore::class)->init();
$TEB = new TestEntityB();
$TEB->setSomeValue(rand(0,50));
data_save($TEB);
foreach(data_find(TestEntityB::class) as $primaryKey) {
    //var_dump(data_load(TestEntityA::class, $primaryKey));
    var_dump($primaryKey);
    $entity = data_load(TestEntityB::class, $primaryKey);
    $entity->setSomeValue(rand(0,50));
    data_save($entity);
//    /** @var TestEntityA $entity */
//    $entity = $uniqueFactory->load();
//    $entity->setPublicVar(42);
//    $entity->setProtectedVar('overwrite');
//    $entity->setPrivateVar(false);
//    $uniqueFactory->persist();
}
