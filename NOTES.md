Es gibt den User-Bereich und den Core-Bereich
Zum User-Bereich zählen:
- Collection
- Entity
- Repository

Zum Core-Bereich:
- Exceptions
- Filesystem
- IndexHandling
- Query
- Serialization

Alle Bereiche dürfen nur über die Manager miteinander kommunizieren.
Für den User-Bereich sind zuständig:
- DataStore
- UniqueEntityProxy

Für den Core-Bereich ist zuständig
- DataStore::isLoaded
- StaticEntitySaveStack 

