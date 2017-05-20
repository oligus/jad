JAD
---
JSON Api To Doctrine ORM

[![Build Status](https://travis-ci.org/oligus/jad.svg?branch=master)](https://travis-ci.org/oligus/jad)

JAD is a library that marries two other libraries, [tobscure/json-api](https://github.com/tobscure/json-api) and
[doctrine/doctrine2](https://github.com/doctrine/doctrine2), to transform a doctrine entity to a json api resource.

You preferably use Jad in a middleware or plugin where Jad will capture the request by itself and return a json response
if it can correctly find the mapping, otherwise response is empty.

### Requirements

You need to have Doctrine installed and preferably setup before you can use Jad.

### Install

`composer require oligus/jad`

### Quick start
```
$jad = new Jad(new Jad\Map\AutoMapper($em));
$jad->setPathPrefix('/api/v1/jad');
$jad->jsonApiResult();
```

### Mapping entities

Jad needs to know what resource belongs to what entity so a mapping is required before you can use Jad. There are three
different types of mapping options available:

* AutoMapper
* AnnotationMapper
* ArrayMapper

#### AutoMapper

Auto mapper tries to map everything for you automagically, it will simply take all entity classes it can find and create
json api type names from the class names. Optionally, you can add an array with excluded types in the constructor, these
types will then not be exposed to json api.

```
$mapper = new Jad\Map\AutoMapper($em, ['excluded']);
```

#### AnnotationMapper

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

#### ArrayMapper

With the array mapper, you simply add every type using `mapper->add` method with type name and the corresponding entity
class. All added entities will be exposed to json api.

```
$mapper = new Jad\Map\ArrayMapper($em);
$mappper->add('articles', 'MyProject/Entities/Articles');
```

### Usage

Setup Jad in your middleware/plugin:

```
$jad = new Jad($mapper);
$jad->setPathPrefix('/api/v1/jad');
$jad->jsonApiResult();
```

#### Fetch single item

```
GET /api/v1/jad/articles/45
```

#### Fetch a collection

```
GET /api/v1/jad/articles
```

#### Fields
Get the fields requested for inclusion, keyed by resource type.
```
// GET /api/v1/jad/articles?fields[articles]=title,body
```

#### Ordering
Order collection
```
// GET  /api/v1/jad/articles?sort=-created,title
```

#### Limit and offset
Order collection
```
// GET /api/v1/jad/articles?page[offset]=5
// GET /api/v1/jad/articles?page[limit]=25
// GET /api/v1/jad/articles?page[number]=5&page[size]=20
// GET /api/v1/jad/articles?page[offset]=20&page[limit]=200
```
### Relationships
Include relationship

```
// GET /api/v1/jad/articles??include=author,comments
```

Will generate relationship. The relationship Entity must be specified in the mapper.

### Doctrine setup

Doctrine entity
```
/**
 * @ORM\Entity(repositoryClass="MyProject\Repositories\ArticlesRepository")
 * @ORM\Table(name="articles")
 */
class Articles
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

```