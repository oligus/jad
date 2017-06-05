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
$jad = new Jad(new Jad\Map\AnnotationMapper($em));
$jad->setPathPrefix('/api/v1/jad');
$jad->jsonApiResult();
```

## Contents

[Mapping your entities](docs/mapping.md)

[Fetching the resources](docs/fetch.md)

[Fetching resources with relationships](docs/relations.md)

[Creating a new resource](docs/create.md)

## Contributing

When contributing to this repository, please first discuss the change you wish to make via issue before making a pull request.

## Authors

* **Oli Gustafsson** - *Initial work* - [oligus](https://github.com/oligus)

## License

This project is licensed under the GPL v2 License - see the [LICENSE](LICENSE) file for details
