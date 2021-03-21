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
and a memory based entity caching. Checkout the eArc components [earc/data-redis](#TODO) 
and earc/data-elasticsearch [earc/data-elasticsearch](#TODO)
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
        - [immutable entities](#immutable-entities)
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
- [advanced usage](#advanced-usage)
    - [writing your own bridge](#writing-your-own-bridge)
- [releases](#releases)
    - [release v0.0](#release-v00)

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
\BootstrapEArcData::init();
```

Then register your persistence infrastructure to the `onLoad`, `onSave` and `onRemove` 
events.

```php
use eArc\Data\ParameterInterface;

di_tag(ParameterInterface::TAG_ON_LOAD, MyMemCacheBridge::class);
di_tag(ParameterInterface::TAG_ON_LOAD, MyDataBaseBridge::class);

di_tag(ParameterInterface::TAG_ON_PERSIST, MyMemCacheBridge::class);
di_tag(ParameterInterface::TAG_ON_PERSIST, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_PERSIST, MySearchIndexBridge::class);

di_tag(ParameterInterface::TAG_ON_REMOVE, MyMemCacheBridge::class);
di_tag(ParameterInterface::TAG_ON_REMOVE, MyDataBaseBridge::class);
di_tag(ParameterInterface::TAG_ON_REMOVE, MySearchIndexBridge::class);

di_tag(ParameterInterface::TAG_ON_FIND, MySearchIndexBridge::class);
di_tag(ParameterInterface::TAG_ON_FIND, MyDataBaseBridge::class);
```

After that entities can be loaded and saved via the `data_*` functions.

```php
use eArc\Data\Entity\AbstractEntity;

class MyEntity extends AbstractEntity
{
    public function __construct(...$args)
    {
        //... some construction logic
    }
    //... all the other entity logic
}

$entity = new MyEntity('some arguments');

\data_persist($entity); // saves the entity
$pk = $entity->getPrimaryKey(); // yields the primary key - may be set prior persistence
\data_load(MyEntity::class, $pk); // returns the entity of MyEntity::class with the primary key $pk
\data_delete($entity); // removes the entity
\data_remove(MyEntity::class, $pk); // removes the entity without the need of being loaded before
\data_find(MyEntity::class, ['name' => ['Anton', 'Max', 'Simon'], 'age' => 42]); // returns the primary keys for all MyEntity instances where the name property is equal to 'Anton', 'Max' or 'Simon' and the age property is 42

// expert functions - use only if you really know what you are doing
\data_schedule($entity); // schedules the saving process until `data_persist` is called with any argument
\data_detach(get_class($entity), $pk); // removes the entity from the earc/data entity cache
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
managed by earc/data, and your reverenced entity are lazy loaded. Both saves a 
lot of overhead and gives you more control without doing much more programming.

#### collections

Reverencing many entities follows the same principles. To make it easy use the 
`Collection` class. The presence of the `CollectionInterface` is mandatory for
the persistence/loading process.

```php
use eArc\Data\Collection\Collection;
use eArc\Data\Entity\AbstractEntity;

class MyReverencedEntity extends AbstractEntity {/*...*/}

class Entity extends AbstractEntity
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
    
    public function getMyReverencedEntityCollection(): EmbeddedCollection
    {
        return $this->myEmbeddedEntityCollection;
    }
}
```

Embedded collections consist always of one type of entities.

Hint: If you extend the type of the entities used by your collection, you can add
instances of this to the collection and this time earc/data guarantees that the 
additional data is saved.

Hint: If you reverence an object by a property of an entity that is no embedded
entity and not an embedded collection earc/data neither guarantees that the data
of the property is saved nor that it is not. It should be avoided, but it is not
permitted. If you use your own bridge and serializer it may make sense.

#### immutable entities

Immutable entities are a special case of pure entities. They implement the 
`ImmutableEntityInterface`. Immutable entities can not be updated and only 
deleted with the force flag.

If immutable entities implement the `AutoPrimaryKeyInterface` a new primary key
will be generated on persist, if there is a data set with this primary key already.

### functions

earc/data uses functions instead of services to give maximal freedom to your code.

#### data persist

`data_persist` takes one entity as argument. The data of the entity will be saved.
If the `getPrimaryKey()` method returns null an exception is thrown. There is only 
one exception to the rule: if the entity implements the `OnAutoPrimaryKeyInterface` 
and at least one callable registered to the `OnAutoPrimaryKeyInterface` event 
returns a string result. Then this result is used as primary key.

`data_persist` returns the primary key as result.

On calling `data_persist` all entities scheduled via `data_schedule` will be saved 
first. `data_persist` can be called without argument, to trigger the saving of the
scheduled entities without the need to explicit save any entity. 

#### data load

`data_load` takes the class name and primary key as arguments. It returns the
entity. 

`data_load` returns the same instance on successive calls when the same arguments 
are provided.

You can change this behaviour by calling `data_detach` in between. Please note the
resulting behaviour might be unexpected for inattentive developers. Use with great
care.

#### data delete

`data_delete` takes an entity as argument. It removes the entity data.

Entities implementing the `ImmutableEntityInterface` can only be deleted if the
force flag has been set.

#### data remove

`data_remove` takes the entity class name and primary key as arguments. It works
as `data_delete` but without the need of the entity being loaded prior removal.

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
`PostRemoveInterface`). Each event exists as entity event and as service event. The
service event is triggered prior the entity event. The callables of the registered 
services are called every time the event is triggered, the callables of the entity 
only if the entity is affected by the event.

Any callable that is returned by the interface method will be called with the 
entity (if the method is non static) or class name and primary key 
(if the method is static). Returns are not evaluated, thus void callables
are recommended.

#### via entity

To use the livecycle events via an entity, the entity has to implement the corresponding
interface. 

Use cases are wakeup/sleep procedures or cascading removals/persistence.

A cascade persist can be implemented as follows:

```php
use eArc\Data\Entity\AbstractEntity;
use \eArc\Data\Entity\Interfaces\Events\PrePersistInterface;

class MyReverencedEntity extends AbstractEntity {/*...*/}

class MyEntity extends AbstractEntity implements PrePersistInterface
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
    
    public function cascadePersist(MyEntity $myEntity): void
    {
        if ($entity = \data_load(MyReverencedEntity::class, $this->myReverencedEntityPK, true)) {
            \data_persist($entity);        
        }
    }
    
    public function getPrePersistCallables() : iterable
    {
        yield [$this, 'cascadePersist'];
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

di_tag(PreRemoveInterface::class, MyPreRemoveService::class);


class MyPreRemoveService implements PreRemoveInterface 
{
    public static function preRemove(string $fQCN, string $primaryKey): void
    {
        // pre remove logic goes here...
    }
    
    public static function getPreRemoveCallables(): iterable
    {
        yield [MyPreRemoveService::class, 'preRemove']; 
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
for example attribute driven libraries or write your own generators.

## advanced usage
### writing your own bridge

## releases

### release v0.0

* the first official release
* PHP ^8.0 support
* IDE support for PHPStorm:
    - return type support for `data_load`
