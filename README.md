# eArc-data

```php
class MyEntity extends \eArc\DataStore\Entity\AbstractEntity
{
    public $data;
}

$proxy = eArc\DataStore\Manager\UniqueEntityProxy::getInstance(new MyEntity(), null);
$data = 'some text';
$entity = $proxy->load();
\eArc\DataStore\Repository\data_save($entity);
\eArc\DataStore\Repository\data_delete($entity);
```

##TODO
UniqueEntityProxy must be unique!

```php
use eArc\DataStore\Entity\AbstractEntity;
use function eArc\DataStore\Repository\data_save;

class A_Entity extends AbstractEntity
{
    protected $collection;

    public function __construct()
    {
        $this->collection = new eArc\DataStore\Collection\Collection($this, A_Entity::class);
        $this->collection->addEntity($this);
    }
}

data_save(new A_Entity()); //<- infinity loop?
// There may be several layers of embedded entities between!
// Maybe ObjectHashService could help?
// The atomic nature of getIndex must be preserved!
```
