# JAD

[<< Back](../README.md)

# Fetching resources

## Fetch a collection of items

A simple get without any parameters will fetch a collection
 
```
GET /api/v1/jad/genres
```

Result might be:
```json
{"data": [
    {
      "id": 1,
      "type": "genre",
      "attributes": {
        "name": "Rock"
      }
    },{
      "id": 2,
      "type": "genre",
      "attributes": {
        "name": "Punk"
      }
    }
]}
```

## Fetch single item

Fetch a single item by id

```
GET /api/v1/jad/genres/12
```

```json
{"data":{"id":12,"type":"genre","attributes":{"name":"Easy Listening"}}}
```

## Sparse fieldset

Return only specific fields in the response on a per-type basis by including a fields[TYPE] parameter.

The value of the fields parameter is a comma-separated list that refers to the name(s) of the fields to be returned.

### Examples:

Only fetch fields city, country and email
```
GET /api/jad/customers?fields[customers]=city,country,email
```

As above but include invoices and only fetch invoice-data and total from related invoices
```
GET /api/jad/customers?include=invoices?fields[customers]=city,country,email&fields[invoices]=invoice-date,total
```

## Sorting

The sorting parameter consist of two parts, sorting field and direction.
Direction defaults to ASC, putting a hyphen `-` in front of the field indicates DESC ordering.

#### Examples:

Order records by first name ascending:
```
GET /api/v1/jad/customers?sort=first-name
```

Order records by first name descending:
```
GET /api/v1/jad/customers?sort=-first-name
```

Order records by first name ascending, city ascending:
```
GET /api/v1/jad/customers?sort=first-name,city
```

Order records by first city ascending, country descending
```
GET /api/v1/jad/customers?sort=city,-country
```

## Pagination

To enable pagination on a specific entity, simply set the annotation attribute `paginate` to `true`in your entity:

```
* @JAD\Header(type="tracks", paginate=true)
```

This will ad pagination links to the resource in question, however this might be expensive depending on your setup.
When a table is paginated it will always make two db queries, one for count and one for the selection.

Pagination will still work without the count query but paging links cannot be calculated.

#### Parameters

Pagination uses the page strategy and consists of two parameters, size and number.

* size, number of records per page
* number, current page number

_Note: The size parameter has a hard ceiling (100) for safety. You can override this setting by using the global configuration:_

```
$config = Configure::getInstance();
$config->setConfig('max_page_size', 250);
```

A normal get with size and page parameters will display paginated content:

```
GET /api/jad/tracks?page[size]=25&page[number]=2

```

If `size` is omitted, the default page size of `25` is used.

#### Pager links

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

## Filtering

Two types of filter are available, simple `single` filter that filters on one property.
Or a column wide `conditional` filter that spans over multiple columns.

Filter conditionals available:

| Conditional |                          |
| ----------- |------------------------- |
| eq          | equal                    | 
| lt          | less than                | 
| lte         | less than or equal to    | 
| gt          | greater than             | 
| gte         | greater than or equal to | 
| like        | LIKE %value%             | 
| notLike     | NOT LIKE %value%         | 

#### Examples:

Fetch all tracks where price is less than 1:
```
GET /api/v1/jad/tracks?filter[price][lte]=1
```


Fetch all tracks where `(price > 0 AND price < 2) OR genre = 5`
```
GET /api/jad/tracks?filter[tracks][and][price][gt]=0&filter[tracks][and][price][lt]=2&filter[tracks][or][genre][eq]=5
```

Filters:
```
filter[tracks][and][price][gt]=0
filter[tracks][and][price][lt]=2
filter[tracks][or][genre][eq]=5
```


