# JAD

[<< Back](../README.md)

## Fetching resources

#### Fetch a collection of items

A simple get without any parameters will fetch a collection
 
```
GET /api/v1/jad/genres
```

Result might be:
```
{
  "data": [
    {
      "id": 1,
      "type": "genre",
      "attributes": {
        "name": "Rock"
      }
    },{
      ...
    }
  ]
}
```

#### Fetch single item

Fetch a single item by id

```
GET /api/v1/jad/genres/12
```

```json
{"data":{"id":12,"type":"genre","attributes":{"name":"Easy Listening"}}}
```

#### Limit and offset

Limit default value is 25 and 25 per page.
```
GET /api/v1/jad/genres?page[offset]=5
GET /api/v1/jad/genres?page[limit]=25
GET /api/v1/jad/genres?page[number]=5&page[size]=20
GET /api/v1/jad/genres?page[offset]=20&page[limit]=200
```

#### Ordering

Order results with the sort parameter, defaults to ASC, putting a
hyphen `-` in front of the value indicates DESC ordering.

```
GET /api/v1/jad/genres?sort=-name
```

#### Fields

Limit the number of fields in the results, get requested fields for inclusion, keyed by resource type.

```
// GET /api/v1/jad/tracks?fields[track]=name
```

#### Filter

At the moment only one type of filter is available, the equal `[eq]` filter

```
// GET /api/v1/jad/tracks?filter[name][eq]=Go+Down
```

```json
{
  "data": [
    {
      "id": 15,
      "type": "track",
      "attributes": {
        "name": "Go Down",
        "composer": "AC\/DC",
        "price": "0.99"
    },
    ...
  ]
 }
```