JAD
---
JSON Api To Doctrine ORM

[![Build Status](https://travis-ci.org/oligus/jad.svg?branch=master)](https://travis-ci.org/oligus/jad)

JAD is a library that marries two other libraries, [tobscure/json-api](https://github.com/tobscure/json-api) and
[doctrine/doctrine2](https://github.com/doctrine/doctrine2), to make a doctrine entity becomes json api resource.

You preferably use Jad in a middleware or plugin where Jad will capture the request by itself and return a json response
if it can correctly find the mapping, otherwise response is empty.

### Requirements

You need to have Doctrine installed and preferably setup before you can use Jad.

### Mapping entities

Jad needs to know what resource belongs to what entity so a mapping is required before you can use Jad.

Example, if you have a path `/api/v1/jad/articles` and `articles` is the resource you want to map to your doctrine
MyProject/Entities/Articles entity:

```
$map = new Jad\Map\EntityMap();
$map->add('Articles', 'MyProject/Entities/Articles');
```

### Usage

Setup Jad in your middleware/plugin:

```
$jad = new Jad($em);
$jad->setPathPrefix('/api/v1/jad');
$jad->setEntityMap($map);
$jad->jsonApiResult();
```

### Examples

Doctrine entity
```
/**
 * @ORM\Entity(repositoryClass="Project\Repositories\ArticlesRepository")
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