!BOOM
koudela@koudela-lap:~/Projekte/eArc-data$ rm -rf *
koudela@koudela-lap:~/Projekte/eArc-data$ git status
On branch develop
Your branch is up-to-date with 'origin/develop'.

Changes to be committed:
(use "git restore --staged <file>..." to unstage)
    new file:   src/IndexHandling/AbstractValue.php
    new file:   src/IndexHandling/IndexFactory.php
    new file:   src/IndexHandling/Interfaces/IndexInterface.php
    new file:   src/IndexHandling/QualifiedValue.php
    new file:   src/IndexHandling/QueryIndex.php
    new file:   src/IndexHandling/QueryRange.php
    new file:   src/IndexHandling/QueryValue.php
    new file:   src/IndexHandling/Resolver.php
    deleted:    src/Manager/Interfaces/EntityProxyInterface.php
    deleted:    src/Manager/UniqueEntityProxy.php
    new file:   src/Query/Collector.php
    deleted:    src/Query/Interfaces/QueryServiceInterface.php
    new file:   src/Query/PropertyRelation.php
    modified:   src/Query/Query.php
    new file:   src/Query/QueryConjunction.php
    new file:   src/Query/QueryDesign.php
    new file:   src/Query/QueryJoin.php
    new file:   src/Repository/Interfaces/ParameterInterface.php
    deleted:    src/Serialization/DataTypes/EntityProxyInterfaceDataType.php
    new file:   tests/saturn.php

    Changes not staged for commit:
    (use "git add/rm <file>..." to update what will be committed)
        (use "git restore <file>..." to discard changes in working directory)
            deleted:    LICENSE
            deleted:    NOTES.md
            deleted:    README.md
            deleted:    composer.json
            deleted:    src/Collection/AbstractBaseCollection.php
            deleted:    src/Collection/Collection.php
            deleted:    src/Collection/EmbeddedCollection.php
            deleted:    src/Collection/Interfaces/CollectionBaseInterface.php
            deleted:    src/Collection/Interfaces/CollectionInterface.php
            deleted:    src/Collection/Interfaces/EmbeddedCollectionInterface.php
            deleted:    src/Entity/AbstractEmbeddedEntity.php
            deleted:    src/Entity/AbstractEntity.php
            deleted:    src/Entity/Interfaces/Cascade/CascadeDeleteInterface.php
            deleted:    src/Entity/Interfaces/Cascade/CascadePersistInterface.php
            deleted:    src/Entity/Interfaces/EmbeddedEntityInterface.php
            deleted:    src/Entity/Interfaces/EntityBaseInterface.php
            deleted:    src/Entity/Interfaces/EntityInterface.php
            deleted:    src/Entity/Interfaces/Index/IsIndexedInterface.php
            deleted:    src/Entity/Interfaces/PrimaryKey/AutoincrementPrimaryKeyInterface.php
            deleted:    src/Entity/Interfaces/PrimaryKey/PrimaryKeyInterface.php
            deleted:    src/Exceptions/DataException.php
            deleted:    src/Exceptions/HomogeneityException.php
            deleted:    src/Exceptions/Interfaces/DataExceptionInterface.php
            deleted:    src/Exceptions/Interfaces/HomogeneityExceptionInterface.php
            deleted:    src/Exceptions/Interfaces/NoDataExceptionInterface.php
            deleted:    src/Exceptions/Interfaces/NoIndexExceptionInterface.php
            deleted:    src/Exceptions/Interfaces/QueryExceptionInterface.php
            deleted:    src/Exceptions/NoDataException.php
            deleted:    src/Exceptions/NoIndexException.php
            deleted:    src/Exceptions/QueryException.php
            deleted:    src/Filesystem/Interfaces/DirectoryServiceInterface.php
            deleted:    src/Filesystem/Interfaces/NamingServiceInterface.php
            deleted:    src/Filesystem/Interfaces/PersistenceInterface.php
            deleted:    src/Filesystem/StaticDirectoryService.php
            deleted:    src/Filesystem/StaticNamingService.php
            deleted:    src/Filesystem/StaticPersistenceService.php
            deleted:    src/IDESupport/.phpstorm.meta.php
            deleted:    src/IndexHandling/AbstractValue.php
            deleted:    src/IndexHandling/IndexEventHandler.php
            deleted:    src/IndexHandling/IndexFactory.php
            deleted:    src/IndexHandling/Interfaces/IndexInterface.php
            deleted:    src/IndexHandling/PrimaryKeyGenerator.php
            deleted:    src/IndexHandling/QualifiedValue.php
            deleted:    src/IndexHandling/QueryIndex.php
            deleted:    src/IndexHandling/QueryRange.php
            deleted:    src/IndexHandling/QueryValue.php
            deleted:    src/IndexHandling/Resolver.php
            deleted:    src/Manager/DataStore.php
            deleted:    src/Manager/Interfaces/DataStoreInterface.php
            deleted:    src/Manager/Interfaces/EntitySaveStackInterface.php
            deleted:    src/Manager/StaticEntitySaveStack.php
            deleted:    src/Query/Collector.php
            deleted:    src/Query/PropertyRelation.php
            deleted:    src/Query/Query.php
            deleted:    src/Query/QueryConjunction.php
            deleted:    src/Query/QueryDesign.php
            deleted:    src/Query/QueryJoin.php
            deleted:    src/Query/QueryService.php
            deleted:    src/Repository/GenericRepository.php
            deleted:    src/Repository/Interfaces/EmbeddedRepositoryInterface.php
            deleted:    src/Repository/Interfaces/ParameterInterface.php
            deleted:    src/Repository/Interfaces/RepositoryBaseInterface.php
            deleted:    src/Repository/Interfaces/RepositoryInterface.php
            deleted:    src/Serialization/DataTypes/CollectionInterfaceDataType.php
            deleted:    src/Serialization/DataTypes/EmbeddedCollectionInterfaceDataType.php
            deleted:    src/Serialization/DataTypes/EmbeddedEntityInterfaceDataType.php
            deleted:    src/Serialization/DataTypes/EntityInterfaceDataType.php
            deleted:    src/Serialization/DataTypes/PrimaryKeyDataType.php
            deleted:    src/events/components/earc_data/Functions.php
            deleted:    src/events/components/earc_data/Parameter.php
            deleted:    tests/data/.gitignore
            deleted:    tests/data/eArc/DataStoreTests/env/TestEntityA/8fa184d7-62da-496a-b162-60e478959d1b.txt
            deleted:    tests/data/eArc/DataStoreTests/env/TestEntityA/cc47625e-a3a1-475e-aa3a-cbdb7617b612.txt
            deleted:    tests/env/TestEntityA.php
            deleted:    tests/env/TestEntityB.php
            deleted:    tests/features/data.feature
            deleted:    tests/saturn.php
            deleted:    tests/test.php
