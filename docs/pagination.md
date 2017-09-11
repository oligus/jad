# JAD

[<< Back](../README.md)

## Pagination

To enable pagination on a specific entity, simply set the annotation attribute `paginate` to `true`in your entity:

```
* @JAD\Header(type="tracks", paginate=true)
```

This will ad pagination links to the resource in question, however this might be expensive depending on your setup.
When a table is paginated it will always make two db queries, one for count and one for the selection.

Pagination will still work without the count query but paging links cannot be calculated.

### Parameters

Pagination uses the page strategy and consists of two parameters, size and number.

* size, number of records per page
* number, current page number

_Note: The size parameter has a hard ceiling (100) for safety. You can override this setting by using the global configuration:_

```
$config = Configure::getInstance();
$config->setConfig('max_page_size', 250);
```

A normal get with size and page parameters will display paginated content:

`http://localhost/api/jad/tracks?page[size]=25&page[number]=2`

If `size` is omitted, the default page size of `25` is used.

### Pager links

For all resources that have pagination activated, pager links will be provided in the result:

```json
"links": {
  "self": "http://localhost/api/jad/tracks?page[size]=25&page[number]=1",
  "first": "http://localhost/api/jad/tracks?page[size]=25&page[number]=1",
  "last": "http://localhost/api/jad/tracks?page[size]=25&page[number]=141",
  "next": "http://localhost/api/jad/tracks?page[size]=25&page[number]=2",
  "previous": "http://localhost/api/jad/tracks?page[size]=25&page[number]=2"
  }
```
