# JAD

[<< Back](../README.md)

## Configure

Use `Configuration` singleton class for configuration of JAD.

```php
use Jad\Configure;

$em = Manager::getInstance()->getEm();
$mapper = new AnnotationsMapper($em);

$config = Configure::getInstance();
$config->setConfig('cors', true);

$jad = new Jad($mapper);
$jad->setPathPrefix('/api/jad');
```

Configurable options:
        
| Option        | Type    | Description                                             |
| ------------- |-------- | ------------------------------------------------------- |
| debug         | bool    | Pretty print output                                     | 
| cors          | bool    | Sets Access-Control-Allow-Origin to * (disables cors)   | 
| max_page_size | integer | Maximum number of records, defaults to 25, can be overrided with `page[size]=25`       | 
| strict        | bool    | Set to true will:<br />1. Throw not found error on missing resource (other wise returns an empty string)<br />2. Throw an error on no supported methods ie. `OPTIONS`     | 
| test_mode     | bool    | Used for testing        | 
