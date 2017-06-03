# JAD

JSON Api :heart: Doctrine ORM

[![Build Status](https://travis-ci.org/oligus/jad.svg?branch=master)](https://travis-ci.org/oligus/jad)

Jad is a library that turns doctrine entities ([doctrine/doctrine2](https://github.com/doctrine/doctrine2)) to a
json api resource, or collection of resources automagically.

You preferably use Jad in a middleware or plugin where Jad will capture the request by itself and return a json response
if it can correctly find the mapping.

## Requirements

You need to have Doctrine installed and preferably setup before you can use Jad.

## Install

`composer require oligus/jad`

## Quick start
```
$jad = new Jad(new Jad\Map\AutoMapper($em));
$jad->setPathPrefix('/api/v1/jad');
$jad->jsonApiResult();
```

## Contents

[Mapping your entities](docs/mapping.md)

[Fetching the resources](docs/fetch.md)

## Usage

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


### Relationships
Include relationship

```
// GET /api/jad/tracks/1?include=playlists
```
Will generate relationship. The relationship Entity must be specified in the mapper.

#### Create

```json
{
  "data": {
    "type": "artists",
    "attributes": {
      "name": "Test artist"
    }
  }
}
```
#### Update
When updating an entity with relationship, you add

```json
{
  "data": {
    "type": "playlists",
    "id": "17",
    "relationship": {
      "tracks": { "data": { "type": "tracks", "id": 44 } }
    }
  }
}
``` 


#### Delete

```
DELETE /api/v1/jad/articles/45
```

### Doctrine setup
