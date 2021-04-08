# earc/data

Strongly decoupled data component of the eArc libraries. 

Use this library if you want maximal control of the persistence process. 
Update/request your databases, caches, search indices, etc. during entity 
persistence/load processing in an optimal order determined by your project. You 
can even exchange them, stack them up or remove parts with minimal effort and 
without touching your business logic. 

It works best with key-value based persistence like you would use with a 
[redis server](https://redis.io/) backed up by a search index server like 
[elasticsearch](https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html), 
and a memory based entity caching. Checkout the eArc components [earc/data-redis](https://github.com/Koudela/eArc-data-redis) 
and earc/data-elasticsearch [earc/data-elasticsearch](https://github.com/Koudela/eArc-data-elasticsearch)
for easy integration.

The usage of a traditional sql databases is possible too, but comes with a bit
more effort on your side. Check the [writing your own bridge](#writing-your-own-bridge)
section for a deeper insight.

## table of contents

- [pro/cons](#procons)
- [installation](#installation)
- [basic usage](#basic-usage)
    - [classes](#classes)
        - [entities](#entities)
        - [collections](#collections)
        - [embedded entities](#embedded-entities)
        - [embedded collections](#embedded-collections)
    - [functions](#functions)
        - [data persist](#data-persist)
        - [data load](#data-load)
        - [data delete](#data-delete)
        - [data remove](#data-remove)
        - [data find](#data-find) 
        - [data schedule](#data-schedule)
        - [data detach](#data-detach)
    - [livecycle-events](#livecycle-events)
        - [via entity](#via-entity)
        - [via service](#via-service)
    - [autogenerate keys](#autogenerate-keys)
    - [bridges](#bridges)
- [advanced usage](#advanced-usage)
    - [immutable entities](#immutable-entities)
    - [mutable references](#mutable-references)
    - [writing your own bridge](#writing-your-own-bridge)
    - [on data persist](#on-data-persist)
    - [on data load](#on-data-load)
    - [on data remove](#on-data-remove)
    - [on data find](#on-data-find)
    - [on primary key generation](#on-primary-key-generation)
- [releases](#releases)
    - [release v0.1 (beta)](#release-01-pre-release)
    - [release v0.0](#release-00)

## pro/cons

### pro

- **strongly decoupled** - write your business logic first and decide then about
the persistence layer (databases, caches, search indices with respect to entities)
- **no configuration overhead** - there are no attributes that need to be kept
in sync with the database relations. Just use the abstract classes (or the interfaces
if you need more control).
- **use of global functions** - once initialized there is no need to inject a
repository or a manager in any class.
- **use it everywhere** - saving and loading data works even in vanilla functions 
and closures.
- **architectural optimized code** - processing works in linear time to the size 
and count of your entities. No headache about an entity manager slowing down after
persisting a couple of entities.
- **support for all standard dependency enrichment techniques** - like pre/post
  load/persist/remove events, wakeup/sleep events, cascading removals/updates.
- **support for immutables**
- **write transactions** for the complete persistence infrastructure
- **extendable** - integrates with nearly all kinds of persistence handling.

### cons

- **dependency** on a data library - although it's a much softer coupling than 
for example doctrine provides.
- **small overhead** - entity, collection and event processing need some programming 
logic

## installation

Install the earc data library via composer.

```
$ composer require earc/data
```

## basic usage

The `data_*` functions need to be imported. Use this in your`index.php`, bootstrap 
or configuration script.

```php
use eArc\Data\Initializer;

Initializer::init();
```

Then register your persistence infrastructure to the `onLoad`, `onPersit`, `onRemove`,
`onFind` and `onAutoPrimaryKey` events.

```php
use eArc\Data\ParameterInterface;

di_tag(ParameterInterface::TAG_ON_LOAD, MyCacheBridge::class);
di_tag(ParameterInterface::TAG_ON_LOAD, MyDataBaseBridge::class);

di_tag(ParameterInterface::TAG_ON_PERSIST, MyCacheBridge::class);
di_tag(ParameterInterface::TAG_ON_PERSIST, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_PERSIST, MySearchIndexBridge::class);

di_tag(ParameterInterface::TAG_ON_REMOVE, MyCacheBridge::class);
di_tag(ParameterInterface::TAG_ON_REMOVE, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_REMOVE, MySearchIndexBridge::class);

di_tag(ParameterInterface::TAG_ON_FIND, MySearchIndexBridge::class);
di_tag(ParameterInterface::TAG_ON_FIND, MyDataBaseBridge::class);

di_tag(ParameterInterface::TAG_ON_AUTO_PRIMARY_KEY, MyDataBaseBridge::class);
```

After that entities can be loaded and saved via the `data_*` functions.

```php
$entity = new MyEntity('some arguments');

data_persist($entity); // saves the entity
$pk = $entity->getPrimaryKey(); // yields the primary key - may be set prior persistence
data_load(MyEntity::class, $pk); // returns the entity of MyEntity::class with the primary key $pk
data_delete($entity); // removes the entity
data_remove(MyEntity::class, $pk); // removes the entity without the need of being loaded before
data_find(MyEntity::class, ['name' => ['Anton', 'Max', 'Simon'], 'age' => 42]); // returns the primary keys for all MyEntity instances where the name property is equal to 'Anton', 'Max' or 'Simon' and the age property is 42

// expert functions - use only if you really know what you are doing
data_schedule($entity); // schedules the saving process until `data_persist` is called with any argument
data_detach(get_class($entity), [$pk]); // removes the entity from the earc/data entity cache
```

You can call the `data_*` functions from everywhere (i.e. constructors, methods, 
vanilla functions or plain code).

### classes

There are five types of entity related objects *pure* **entities**, **collections** 
*of pure entities*, **embedded entities**, **embedded collections** and **immutable entities**.
The related interfaces are used in the persisting/loading process.

#### entities

Entities are objects that are associated to a primary key, to identify them (in
combination with their class name) for loading. They implement the `EntityInterface`
and the `PrimaryKeyInterface`. To create an entity class just extend the `AbstractEntity`
class and wire the `primaryKey` property.

For reverencing entities within other entities you have to use the primary key 
as property (like you would do it in a sql database).

```php
use eArc\Data\Entity\AbstractEntity;

class MyReverencedEntity extends AbstractEntity {/*...*/}

class Entity extends AbstractEntity
{
    protected string $myReverencedEntityPK;
    
    public function setMyReverencedEntity(MyReverencedEntity $myReverencedEntity)
    {
        $this->myReverencedEntityPK = $myReverencedEntity->getPrimaryKey();
    }
    
    public function getMyReverencedEntity(): MyReverencedEntity
    {
        return data_load(MyReverencedEntity::class, $this->myReverencedEntityPK);
    }
}
```

This gives the advantage, that reverences between entities does not need to be 
managed by earc/data, and your reverenced entity is lazy loaded. Both saves a 
lot of overhead and gives you more control without doing much more programming.

#### collections

Reverencing many entities follows the same principles. To make it easy use the 
`Collection` class. The presence of the `CollectionInterface` is mandatory for
the persistence/loading process.

```php
use eArc\Data\Collection\Collection;
use eArc\Data\Entity\AbstractEntity;

class MyReverencedEntity extends AbstractEntity {/*...*/}

class EntityWithCollection extends AbstractEntity
{
    protected Collection $myReverencedEntityCollection;
    
    public function __construct()
    {
        $this->myReverencedEntityCollection = new Collection($this, MyReverencedEntity::class);
    }
    
    public function getMyReverencedEntityCollection(): Collection
    {
        return $this->myReverencedEntityCollection;
    }
}

$entityWithCollection = new EntityWithCollection();
$myReverencedEntity = new MyReverencedEntity();
$collection = $entityWithCollection->getMyReverencedEntityCollection();
$collection->add($myReverencedEntity->getPrimaryKey());
$collection->remove($myReverencedEntity->getPrimaryKey());
foreach ($collection as $primaryKey) {
    echo $primaryKey;
}
foreach ($collection->asArray() as $entity) {
    echo $entity->getPrimaryKey();
}
```

Collections consist always of one type of entities. 

Hint: If you extend the type of the entities used by your collection, you can add 
instances of this to the collection, but earc/data neither guarantees that the 
additional data is saved nor that it is not.

#### embedded entities

If two entities are strongly coupled (one entity is needed nearly every time 
while working with the other and vice versa) for example within xml like document 
structures or products and their attributes, you should consider using embedded 
entities. They may have a better performance on loading and saving as the underlying
datastructure is hit only once. 

Embedded entities are saved and loaded with the root entity. The root entity is
always a pure entity and has a primary key. The embedded entities do not have a
primary key. They always hold a reverence to their parent (the embedding) entity.
Embedded entities may be embedded within other embedded entities. They can form 
an entity tree.

They implement the `EmbeddedEntityInterface`. To create an embedded entity class 
just extend the `AbstractEmbeddedEntity` class and wire the `ownerEntity` property. 

```php
use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Entity\AbstractEmbeddedEntity;

class MyEmbeddedEntity extends AbstractEmbeddedEntity
{
    //...
    public function setParent(MyRootEntity $parent)
    {
        $this->ownerEntity = $parent;    
    }
}

class MyRootEntity extends AbstractEntity
{
    protected MyEmbeddedEntity $myEmbeddedEntity;
    
    public function setMyReverencedEntity(MyEmbeddedEntity $myEmbeddedEntity)
    {
        $this->myEmbeddedEntity = $myEmbeddedEntity;
        $myEmbeddedEntity->setParent($this);
    }
    
    public function getMyEmbeddedEntity(): MyEmbeddedEntity
    {
        return $this->myEmbeddedEntity;
    }
}
```

#### embedded collections

Embedded collections are collections of embedded entities embedded within other
entities. Use the `EmbeddedCollection` class for them. The presence of the 
`EmbeddedCollectionInterface` is mandatory for the persistence/loading process.

```php
use eArc\Data\Collection\EmbeddedCollection;
use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Entity\AbstractEmbeddedEntity;

class MyEmbeddedEntity extends AbstractEmbeddedEntity {/*...*/}

class MyRootEntity extends AbstractEntity
{
    protected EmbeddedCollection $myEmbeddedEntityCollection;
    
    public function __construct()
    {
        $this->myEmbeddedEntityCollection = new EmbeddedCollection($this, MyEmbeddedEntity::class);
    }
    
    public function getMyEmbeddedEntityCollection(): EmbeddedCollection
    {
        return $this->myEmbeddedEntityCollection;
    }
}

$rootEntity = new MyRootEntity();
$myEmbeddedEntity = new MyEmbeddedEntity();
$collection = $rootEntity->getMyEmbeddedEntityCollection();
$collection->add($myEmbeddedEntity);
$collection->remove($myEmbeddedEntity);
foreach ($collection as $entity) {
    echo $entity::class;
}
foreach ($collection->asArray() as $entity) {
    echo $entity::class;
}
```

Embedded collections consist always of one type of entities.

Hint: If you extend the type of the entities used by your collection, you can add
instances of this to the collection and this time earc/data guarantees that the 
additional data is saved.

Embedded collections expose a `findBy()` method to search the collection in a 
key value based fashion.

```php
$rootEntity = new MyRootEntity();
$collection = $rootEntity->getMyEmbeddedEntityCollection();
$entities = $collection->findBy(['name' => 'Claudia', 'age' => [31,32,33]]);
foreach ($entities as $entity) {
    echo $entity::class === MyEmbeddedEntity::class
        && $entity->getName() === 'Claudia'
        && in_array($entity->getAge(), [31, 32, 33]) ? 'true' : 'something went wrong';
}
```

Hint: If you reverence an object by a property of an entity that is no embedded
entity and not an embedded collection earc/data neither guarantees that the data
of the property is saved nor that it is not. It should be avoided, but it is not
permitted. If you use your own bridge and serializer it may make sense.

### functions

earc/data uses functions instead of services to give maximal freedom to your code.

#### data persist

`data_persist` takes one entity as argument. The data of the entity will be saved.
If the `getPrimaryKey()` method returns null an exception is thrown. There is only 
one exception to the rule: if the entity implements the `OnAutoPrimaryKeyInterface` 
and at least one callable registered to the `OnAutoPrimaryKeyInterface` event 
returns a string result. Then this result is used as primary key.

On calling `data_persist` all entities scheduled via `data_schedule` will be saved 
first. `data_persist` can be called without argument, to trigger the saving of the
scheduled entities without the need to explicit save any entity. 

To save multiple entities there is a `data_persist_batch` function.

#### data load

`data_load` takes the class name and primary key as arguments. It returns the
entity. 

`data_load` returns the same instance on successive calls when the same arguments 
are provided.

You can change this behaviour by calling `data_detach` in between. Please note the
resulting behaviour might be unexpected for inattentive developers. Use with great
care.

`data_load` takes a third facultative flag. If it is set to `DataStoreInterface::LOAD_FLAG_USE_FIRST_LEVEL_CACHE_ONLY`,
only entities already loaded are returned. If it is set to `DataStoreInterface::LOAD_FLAG_SKIP_FIRST_LEVEL_CACHE`,
earc/data does not look up the already loaded entities and does not add the loaded 
entities to the already loaded ones - the retrieved entities are in a detached state
and will be another instance than the same entities loaded before or after that.

There is a `data_load_batch` function to load multiple entities at once.

#### data delete

`data_delete` takes an entity as argument. It removes the entity data.

Entities implementing the `ImmutableEntityInterface` can only be deleted if the
force flag has been set.

This function has a multiple counterpart `data_delete_batch`.

#### data remove

`data_remove` takes the entity class name and primary key as arguments. It works
as `data_delete` but without the need of the entity being loaded prior removal.

To delete multiple entities of the same class use the `data_remove_batch` function.

#### data find

`data_find` takes the entity class nam as argument. If it is called without further
arguments, it returns all primary keys of the corresponding persisted entities.

The second optional argument takes key value pairs with keys equal to the property
names of the entity. The value of each key has to be a data value or array of data
values. Key value pairs are joint via logic `AND`. Value arrays are interpreted 
as `IN`. Not all key value pairs or value arrays may be supported. It depends on 
the used infrastructure, the setting (for example the usable sql indices) and 
the implementation of the bridge. If one or more key value pairs are not supported 
a `QueryException` is thrown.

Instead of calling `data_load_batch($fQCN, data_load($fQCN, $keyValuePairs)` you
can use the shorthand `data_find_entities($fQCN, $keyValuePairs)`.

Hint: Beside this function there may be more ways to search for entities. These 
are not part of the earc/data abstraction.

#### data schedule

Server responses should be as fast as possible. To support this goal earc/data
has the `data_schedule` function. The persisting of the supplied entity will be scheduled
until `data_persist` is called. If you call `data_persist` in a shutdown function
you can build and send your server response without being delayed by writing
data to a database or index.

#### data detach

earc/data holds a reverence to each loaded entity to ensure every entity exists
only once. To circumvent this behaviour `data_detach` can be called. It deletes
the reverence.

`data_detach` without arguments will delete all reverences. `data_detach` with 
a class name as argument will delete all reverences to entities of this class.
`data_detach` called with class name and primary key deletes the reverence to only 
one entity. 

There are three reasons for calling `data_detach`:

1. Changing the primary key without deleting the entity first.
2. Generating different instances from the same data.
3. Garbage collection.

Hint: If you change the primary key of a persisted entity without calling `data_detach`
first you may encounter unexpected behaviour.

### livecycle events

There are six livecycle events. They are represented by six interfaces (`PreLoadInterface`,
`PostLoadInterface`, `PrePersistInterface`, `PostPersistInterface`, `PreRemoveInterface`,
`PostRemoveInterface`). Each event exists as entity event and as service event.
The service event is triggered prior the entity event. The interface method of 
the registered services are called every time the event is triggered, the 
interface methods of the entity only if the entity is affected by the event.

Any interface method will be called with the entity (if the method is not static) 
or class name and primary key (if the method is static). Returns are not evaluated, 
thus all interface methods have void return types.

#### via entity

To use the livecycle events via an entity, the entity has to implement the corresponding
interface. 

Use cases are wakeup/sleep procedures or cascading removals/persistence.

A cascade persist can be implemented as follows:

```php
use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Entity\Interfaces\EntityInterface;
use \eArc\Data\Entity\Interfaces\Events\PrePersistInterface;

class MyReverencedEntity extends AbstractEntity {/*...*/}

class SomeEntity extends AbstractEntity implements PrePersistInterface
{
    protected string $myReverencedEntityPK;
    
    public function setMyReverencedEntity(MyReverencedEntity $myReverencedEntity)
    {
        $this->myReverencedEntityPK = $myReverencedEntity->getPrimaryKey();
    }
    
    public function getMyReverencedEntity(): MyReverencedEntity
    {
        return data_load(MyReverencedEntity::class, $this->myReverencedEntityPK);
    }
    
    public function prePersist(EntityInterface $entity): void
    {
        if ($entity = data_load(MyReverencedEntity::class, $this->myReverencedEntityPK, true)) {
            data_persist($entity);        
        }
    }
}
```

An implementation via a referenced service method and attributes might be more 
structured but also a bit slower. earc/data gives you the freedom to shape it for
your needs or use third party plugins if available. 

#### via service

Warning: Use service events only if you really need to process events globally, 
or your application is slowed down unnecessarily.

```php
use eArc\Data\Entity\Interfaces\Events\PreRemoveInterface;

di_tag(PreRemoveInterface::class, MyPreRemoveService::class); // <- this comes in your bootstrap section


class MyPreRemoveService implements PreRemoveInterface 
{
    public static function preRemove(string $fQCN, string $primaryKey): void
    {
        // pre remove logic goes here...
    }
}
```

### autogenerate keys

On pure and immutable entities implementing the `AutoPrimaryKeyInterface` the 
primary keys will be set automatically, if `persist_entity` is called without the 
entities having proper primary keys.

To handle key generation there exists the `OnAutoPrimaryKeyInterface`. Every 
class tagged with `di_tag` by this interface name will be called upon via `di_get` 
(see [earc/di](https://github.com/Koudela/eArc-di) for further reference). It has to
implement the `OnAutoPrimaryKeyInterface` as well. The callables received this way
will be called (with the entity as argument) until a string is returned. This
string is set as primary key.

You are completely in charge and responsible for key generation. You can use 
for example attribute driven libraries or write your own generators. Some bridges 
may handle this too.

### bridges

earc/data is an abstraction layer. It is responsible for two major apis: 1. It
is an api you call to save/load and in a restricted way find your entities, without
bothering about the persisting media (filesystem, databases, memory, search indexes,
etc.). 2. It is an api where the persisting infrastructure plugs in.

It was all about the first api until now. The bridges chapter is about the second
api. It tells you how to plug in the persisting infrastructure.

There are five livecycle events that call upon the persisting media, each have a
tag and an interface:
- data persist -> `OnPersistInterface` -> `ParameterInterface::TAG_ON_PERSIST`
- data load -> `OnLoadInterface` -> `ParameterInterface::TAG_ON_LOAD`
- data remove -> `OnPersistInterface` -> `ParameterInterface::TAG_ON_PERSIST`
- data find -> `OnFindInterface` -> `ParameterInterface::TAG_ON_FIND`
- primary key generation -> `OnAutoPrimaryKeyInterface` -> `ParameterInterface::TAG_ON_AUTO_PRIMARY_KEY`

Any library implementing this five interfaces is a valid bridge for earc/data. 

Some library will only implement a subset. Think of a search index. Only the 
data persist/remove events are relevant for it.

There exist some prebuild bridges:
- [redis bridge](https://github.com/Koudela/eArc-data-redis) key-value based database server used for caching
- [elasticsearch bridge](https://github.com/Koudela/eArc-data-elasticsearch) search index
- [filesystem bridge](https://github.com/Koudela/eArc-data-filesystem) filesystem as database or on the fly backup engine
- [key generator bridge](https://github.com/Koudela/eArc-data-primary-key-generator) generating uuids and autoincrement ids

- [default setup bridge](https://github.com/Koudela/eArc-data-default-setup) use 
  filesystem (storage), redis (cache), elasticsearch (search) and key generator 
  bridge in a ready to use setup.

#### plug in a bridge

To activate a bridge you have to tag the interfaces.

```php
use eArc\Data\ParameterInterface;

di_tag(ParameterInterface::TAG_ON_PERSIST, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_LOAD, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_REMOVE, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_FIND, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_AUTO_PRIMARY_KEY, MyDataBaseBridge::class);
```

You can register as many services as you like. The order of tagging is the order
they will be called.

Tagging has to be done after initializing the dependency injection component 
[earc/di](https://github.com/Koudela/eArc-di) and before calling any `data_*` 
function.

```php
use eArc\DI\DI;

DI::init();
```

You can skip initializing earc/di, if you initialize earc/data first.

```php
use eArc\Data\Initializer;

Initializer::init();
```

This will init earc/di as well.

## advanced usage

### immutable entities

Immutable entities are a special case of pure entities. They implement the
`ImmutableEntityInterface`. Immutable entities can not be updated and only
deleted with the force flag.

If immutable entities implement the `AutoPrimaryKeyInterface` a new primary key
will be generated on persist, if there is a data set with this primary key already.

### mutable references

A common use case for immutable entities is to track the changes of a common
entity by a chain of immutables. You cannot reference the entity represented by
the chain via the primary key as every immutable entity has its own primary key.
You cannot use another key either, because you can not remove it from the older
immutables. You may attach a counter and search for the maximal value, but that
may slow down entity loading significantly as the chain grows.

The solution is a second mutable entity which keeps track of the chain of immutables
and updates its reverence. It can be a bit laborious and error-prone to manage
this in your code. By implementing the `MutableReverenceKeyInterface` in the
immutable entity the update of the mutable entity (which has to implement the
`MutableEntityReferenceInterface`) is done automatically by earc/data.

There are two restriction on this concept:
1. The mutable entity has to have a primary key, when the mutable reverence from
   the immutable to the mutable is established.
2. There can only be one mutable reference entity. If you need more references from
   other entities to the immutable, they have to use the mutable reference entity as
   proxy.

```php
use eArc\Data\Entity\Interfaces\MutableEntityReferenceInterface;
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;
use eArc\Data\Entity\Interfaces\ImmutableEntityInterface;
use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Exceptions\DataException;

class MyClassReverencingAImmutable extends AbstractEntity implements MutableEntityReferenceInterface
{
    protected string|null $myImmutablePK;

    //...

    public function setMutableReverenceTarget(MutableReverenceKeyInterface $entity): void
    {
        $this->myImmutablePK = $entity->getPrimaryKey();
    }
    
    public function getMyImmutable(): MyImmutable|null
    {
         return is_null($this->myImmutablePK) ? null : data_load(MyImmutable::class, $this->myImmutablePK);
    }
    
    public function setMyImmutable(MyImmutable $immutable)
    {
        // If the current class does not have a primary key yet, we have to persist it first.
        // A post persist event does not reduce the effort and may lead into a infinity loop. 
        // You may use the solution for reverences between two immutables to circumvent this.
        if (is_null($this->primaryKey)) {
            throw new DataException('Primary key is missing. Try to persist this entity first.');
        }
        
        // The reverse key is set on persist.
        $immutable->setMutableReverenceKey($this->primaryKey);
    }
    
    //...
}

class MyImmutable extends AbstractEntity implements MutableReverenceKeyInterface, ImmutableEntityInterface
{
    protected string|null $mutableReferencePrimaryKey;

    //...
    
    public function getMutableReverenceKey(): string
    {
        return $this->mutableReferencePrimaryKey;
    }

    public function setMutableReverenceKey(string $mutableReferencePrimaryKey): void
    {
        $this->mutableReferencePrimaryKey = $mutableReferencePrimaryKey;
    }

    public function getMutableReverenceClass(): string
    {
        return MyClassReverencingAImmutable::class;
    }
    
    //...
}

class ReferenceUsingProxy extends AbstractEntity
{
    protected string $myImmutableProxyFQCN;
    protected string $myImmutableProxyPK;
    
    //...
    
    public function setMyImmutable(MyImmutable $immutable) 
    {
        $this->myImmutableProxyFQCN = $immutable->getMutableReverenceClass();
        $this->myImmutableProxyPK = $immutable->getMutableReverenceKey();
    }
    
    public function getMyImmutable(): MyImmutable
    {
        $proxy = data_load($this->myImmutableProxyFQCN, $this->myImmutableProxyPK)->getMyImmutable();
        
        return $proxy->getMyImmutable();
    }
    //...
}
```

To implement a mutable reference between two immutable entity chains you can use the
`GenericMutableEntityReference` as link between them.

```php
use eArc\Data\Entity\Interfaces\PrimaryKey\MutableReverenceKeyInterface;
use eArc\Data\Entity\Interfaces\ImmutableEntityInterface;
use eArc\Data\Entity\AbstractEntity;
use eArc\Data\Entity\GenericMutableEntityReference;

class MyImmutableClassReverencingAImmutable extends AbstractEntity
{
    protected string|null $myImmutableLinkPK;

    //...
    
    public function getMyImmutable(): MyImmutable|null
    {
         return is_null($this->myImmutableLinkPK) ? null : 
            data_load(GenericMutableEntityReference::class, $this->myImmutableLinkPK)->getMutableReverenceTarget();
    }
    
    public function setMyImmutable(MyImmutable $immutable)
    {
        $this->myImmutableLinkPK = $immutable->getMutableReverenceKey();
    }

        
    //...
}

class MyImmutable extends AbstractEntity implements MutableReverenceKeyInterface, ImmutableEntityInterface
{
    protected string|null $mutableReferencePrimaryKey;

    public function __construct(GenericMutableEntityReference|null $mutableReference)
    {
        if (is_null($mutableReference)) {
            $mutableReference = new GenericMutableEntityReference();            
        }
        
        if (is_null($mutableReference->getPrimaryKey())) {
            data_persist($mutableReference);        
        }
        
        $this->mutableReferencePrimaryKey = $mutableReference->getPrimaryKey();
    }
    
    //...
    
    public function getMutableReverenceKey(): string
    {
        return $this->mutableReferencePrimaryKey;
    }

    public function getMutableReverenceClass(): string
    {
        return GenericMutableEntityReference::class;
    }

    //...
}
```

### writing your own bridge

If there is no bridge available for your persistence infrastructure, or you need
maximal control of the persisting process, you can write your own bridge.

For writing a bridge all you have to do is to implement the interfaces
for the data persist/load/remove/find and primary key generation events.

#### on data persist

The `onPersist()` method of the `OnPersistInterface` will be called with the 
entities to persist as argument. Persistence must not fail silently.

#### on data load

The `onLoad()` method of the `OnLoadInterface` will be called with fully 
qualified class name and the primary keys of the entities as arguments. It has to 
return the entity objects on success. If the entity could not be found the callable
must not throw an error, but simply not return it. Other services may return the entity
object thereafter. 

The interface passes as third argument an array of callables. If a service fails
to load an entity, it can add a callback to the array. The callables will be called
when all entities are loaded, with the entities as argument. Thus, a cache can save
missing entities, without the need to listen at every post load event.

#### on data remove

The `onRemove()` method of the `OnReturnInterface` will be called with fully 
qualified class name and the primary keys of the entities as arguments. Removing an 
entity must not fail silently unless it has not existed yet.

#### on data find

The `onFind()` method of the `OnFindInterface` will be called with fully 
qualified class name and key-value-pairs of the entity as arguments. It has to 
return an array of primary keys. It may be empty if no entity fits to the 
key-value-pairs. They have to return null, if they cannot process the 
key-value-pairs because of the infrastructure or missing data, but they have to 
throw an earc/data `QueryExceptionInterface` if just a configuration or index is 
missing.

The empty key-value-pairs array witch has to return all existing primary keys of
the entity class has to be supported always. The other key-value-pairs may not be 
supported or depend on configuration or indices. 

Key value pairs have to be joint via logic `AND`. Value arrays have to be 
interpreted as `IN`.

For example `callable(User::class, ['firstname' => 'Max', 'age' => [18, 19, 20, 21, 22, 23])`
has to return all users with first name Max and age between 18 and 23.

Bridges may extend this syntax to support a wider range of search requests.

#### on primary key generation

The `onAutoPrimaryKey()` method of the `OnAutoPrimaryKeyInterface` will be called 
with the entity in need for a new primary key as argument. It has to return a
string, or null if it is not willing to process the request.

The key generation is called upon before saving the entity. This may be a contradiction
to the infrastructure, for example if the entity is persisted to a sql database
which generates the key. In these cases the method has to return an empty 
string. Of course this must be recognised by the on data persist services.

## releases

### release 0.1 (pre-release)

* transactions (beta - documentation in code only, no tests, interfaces may change)

### release 0.0

* the first official release
* PHP ^8.0 support
* IDE support for PHPStorm:
    - return type support for `data_load`, `data_load_batch` and `data_find_entities`
