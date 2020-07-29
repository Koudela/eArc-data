<?php declare(strict_types=1);

use eArc\DataStore\Manager\DataStore;
use eArc\DataStoreTests\env\TestEntityB;
use eArc\DI\DI;
use function eArc\DataStore\Manager\data_delete;
use function eArc\DataStore\Manager\data_load;
use function eArc\DataStore\Manager\data_save;

include __DIR__.'/../vendor/autoload.php';

DI::init();
di_set_param('earc.data.path', __DIR__.'/data/');
di_get(DataStore::class)->init();
$TEB = new TestEntityB();
$TEB->setSomeValue(rand(0,5));
data_save($TEB);
foreach(di_get(DataStore::class)->getRepository(TestEntityB::class)->find() as $primaryKey) {
    //var_dump(data_load(TestEntityA::class, $primaryKey));
    var_dump($primaryKey);
    $entity = data_load(TestEntityB::class, $primaryKey);
    $entity->setSomeValue(rand(0,5));
    data_save($entity);
//    /** @var TestEntityA $entity */
//    $entity = $uniqueFactory->load();
//    $entity->setPublicVar(42);
//    $entity->setProtectedVar('overwrite');
//    $entity->setPrivateVar(false);
//    $uniqueFactory->persist();
}
