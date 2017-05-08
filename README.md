JAD
---
JSON Api To Doctrine ORM

[![Build Status](https://travis-ci.org/oligus/jad.svg?branch=master)](https://travis-ci.org/oligus/jad)

### Requirements

```
$map = new EntityMap();
$map->add('Articles', 'MyProject/Entities/Articles');
$jad = new Jad($em);
$jad->setPathPrefix('/api/jad');
$jad->setEntityMap($map);
$jad->jsonApiResult();
```

        // /articles                            => get all
        // /articles/1                          => get id
        // /articles/1/author                   => get author relationships
        // /articles/1/relationships/author     => same as above
        // /articles/1/relationships/comments   => get comments relationships
