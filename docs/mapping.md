# JAD

[<< Back](../README.md)

## Mapping

Jad needs to know what resource belongs to what entity so a mapping is required before you can use Jad. There are three
different types of mapping options available:

* AutoMapper
* AnnotationMapper
* ArrayMapper

### AutoMapper

Auto mapper tries to map everything for you automagically, it will simply take all entity classes it can find and create
json api type names from the class names. Optionally, you can add an array with excluded types in the constructor, these
types will then not be exposed to json api.

```
$mapper = new Jad\Map\AutoMapper($em, ['excluded']);
```

### AnnotationMapper

The the annotation mapper requires you to use annotated entities in Doctrine. In your entity file you simply add a 
annotation `@Jad\Map\Annotations(type="albums")`, this annotation will map json api resource type `albums` to the entity
and expose it to the json api.

```
use Jad\Map\Annotations;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\AlbumRepository")
 * @ORM\Table(name="albums")
 * @Jad\Map\Annotations(type="albums")
 */
class Albums
{
...
```

After your entities have been annotated, simply create annotations mapper to inject to Jad:

```
$mapper = new Jad\Map\AnnotationsMapper($em);
```

### ArrayMapper

With the array mapper, you simply add every type using `mapper->add` method with type name and the corresponding entity
class. All added entities will be exposed to json api.

```
$mapper = new Jad\Map\ArrayMapper($em);
$mappper->add('articles', 'MyProject/Entities/Articles');
```