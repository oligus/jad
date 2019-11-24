# JAD

JSON Api :heart: Doctrine ORM

[![Build Status](https://travis-ci.org/oligus/jad.svg?branch=master)](https://travis-ci.org/oligus/jad)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Codecov.io](https://codecov.io/gh/oligus/jad/branch/master/graphs/badge.svg)](https://codecov.io/gh/oligus/jad)
[![Maintainability](https://api.codeclimate.com/v1/badges/db45a4d29b976060fe8a/maintainability)](https://codeclimate.com/github/oligus/jad/maintainability)

JAD is a library created for rapid development of [JSON API](http://jsonapi.org) backend REST implementation. You can
run JAD as a standalone server with php -S (see [demo](demo/README.md)), or you can use it as a middleware in your framework.

It turns doctrine entities ([doctrine/doctrine2](https://github.com/doctrine/doctrine2)) to a JSON API resource, or 
collection of resources automagically.

## Requirements

You need to have Doctrine installed and preferably setup before you can use Jad.

## Install

`composer require oligus/jad`

## Quick start

1. Annotate your entities that you want to expose to JSON-API:

```php
/**
 * @ORM\Entity
 * @ORM\Table(name="albums")
 * @Jad\Map\Annotations\Header(type="albums")
 */
class Albums
{
...
```

2. Setup JAD using current entity manager. 

```php
$jad = new Jad(new Jad\Map\AnnotationMapper($em));
$jad->setPathPrefix('/api/v1/jad');
$jad->jsonApiResult();
```

3. Fetch results

``` 
GET /api/v1/jad/albums
``` 

## Contents

[Configure](docs/configure.md)

[Mapping your entities](docs/mapping.md)

[Fetching the resources](docs/fetch.md)

[Fetching resources with relationships](docs/relations.md)

[Creating a new resource](docs/create.md)

[Updating a resource](docs/update.md)

[Deleting resources / relationships](docs/delete.md)

[Validation](docs/validation.md)

## Support

### Lumen

Support for lumen via middleware.

In your Lumen bootstrap file (../lumen/bootstrap/app.php)
```php
// Jad middleware
$app->middleware([
    'jad' => Jad\Support\Lumen\JadMiddleWare::class,
]);

...

// Register Service Providers
$app->register(Jad\Support\Lumen\JadServiceProvider::class);
```

You can go with the default configuration or copy `lumen/vendor/oligus/jad/src/Support/Lumen/jad.php` to `/lumen/config`
and change it there.

## Contributing

When contributing to this repository, please first discuss the change you wish to make via issue before making a pull request.

## Authors

* **Oli Gustafsson** - *Initial work* - [oligus](https://github.com/oligus)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE.md) file for details
