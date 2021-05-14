# JAD

[<< Back](../README.md)

## Mapping

JAD needs to know what resource belongs to what entity so a mapping is required before you can use JAD. 

### AnnotationMapper

The the annotation mapper requires you to use annotated entities in Doctrine. In your entity file you simply add a 
annotation `@Jad\Map\Annotations\Headers(type="albums")`, this annotation will map json api resource type `albums` to the entity
and expose it to the json api.

```
use Jad\Map\Annotations as JAD;

/**
 * @ORM\Entity
 * @ORM\Table(name="albums")
 * @JAD\Header(type="albums")
 */
class Albums
{
...
```

#### Header

* Resource type name `@JAD\Header(type="albums")`
* Readonly entities `@JAD\Header(type="albums", readOnly=true)`
* Aliases  `@JAD\Header(type="albums", aliases="records,recordings")`

#### Attributes

* Visibility `@JAD\Attribute(visible=true)`
* Readonly `@JAD\Attribute(readOnly=true)`

After your entities have been annotated, simply create annotations mapper to inject to JAD:

```
$mapper = new Jad\Map\AnnotationsMapper($em);
```

##### Note

If Doctrine can't seem to find the JAD annotation classes and you get an error similar to this one:

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
