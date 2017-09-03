# JAD

[<< Back](../README.md)

## Mapping

Jad needs to know what resource belongs to what entity so a mapping is required before you can use Jad. There are three
different types of mapping options available:

* AnnotationMapper
* ArrayMapper
* AutoMapper

### AnnotationMapper

The the annotation mapper requires you to use annotated entities in Doctrine. In your entity file you simply add a 
annotation `@Jad\Map\Annotations\Headers(type="albums")`, this annotation will map json api resource type `albums` to the entity
and expose it to the json api.

```
use Jad\Map\Annotations as JAD;

/**
 * @ORM\Entity(repositoryClass="Jad\Database\Repositories\AlbumRepository")
 * @ORM\Table(name="albums")
 * @JAD\Header(type="albums")
 */
class Albums
{
...
```

#### Header

* Resource type name `@JAD\Header(type="albums")`

#### Attributes

* Attribute visibility `@JAD\Attribute(visible=false)`

After your entities have been annotated, simply create annotations mapper to inject to Jad:

```
$mapper = new Jad\Map\AnnotationsMapper($em);
```

##### Note

If Doctrine can't seem to find the Jad annotation classes and you get an error similar to this one:

```json
{
  "errors": {
    "code": 500,
    "title": "[Semantical Error] The annotation \"@Jad\\Map\\Annotations\\Header\" in class MyProject\\MyEntities\\Entity does not exist, or could not be auto-loaded."
  }
}
```

Then you probably need to specifically register the classes with either `registerLoader` or `registerAutoloadNamespace`:

`AnnotationRegistry::registerLoader('class_exists');`

##### OR

`AnnotationRegistry::registerAutoloadNamespace("Jad\Map\Annotations", "/../vendor/oligus/src/Map/Annotations");`

### ArrayMapper

With the array mapper, you simply add every type using `mapper->add` method with type name and the corresponding entity
class. All added entities will be exposed to json api.

```
$mapper = new Jad\Map\ArrayMapper($em);
$mappper->add('articles', 'MyProject/Entities/Articles');
```

### AutoMapper

Auto mapper tries to map everything for you automagically, it will simply take all entity classes it can find and create
json api type names from the class names. Optionally, you can add an array with excluded types in the constructor, these
types will then not be exposed to json api.

```
$mapper = new Jad\Map\AutoMapper($em, ['excluded']);
```
