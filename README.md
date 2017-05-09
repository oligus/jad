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

### Mapping entities

Jad needs to know what resource belongs to what entity so a mapping is required before you can use Jad.

Example, if you have a path `/api/v1/jad/articles` and `articles` is the resource you want to map to your doctrine
MyProject/Entities/Articles entity:

```
$map = new Jad\Map\EntityMap();
$map->add('articles', 'MyProject/Entities/Articles');
```

If your entity class id field differs from `id` you can change it in the map configuration.

```
$map->add('articles', [
    'entityClass'   => 'MyProject/Entities/Articles',
    'idField'       => 'myId',
]);
```

### Usage

Setup Jad in your middleware/plugin:

```
$jad = new Jad($em);
$jad->setPathPrefix('/api/v1/jad');
$jad->setEntityMap($map);
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

##### Fields
Get the fields requested for inclusion, keyed by resource type.
```
// GET /api/v1/jad/articles?fields[articles]=title,body
```

##### Ordering
Order collection
```
// GET  /api/v1/jad/articles?sort=-created,title
```

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