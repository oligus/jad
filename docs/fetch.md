# JAD

[<< Back](../README.md)

## Fetching resources

#### Fetch a collection of items

A simple get without any parameters will fetch a collection
 
```
GET /api/v1/jad/genres
```

Result might be:
```json
{
  "data": [
    {
      "id": 1,
      "type": "genre",
      "attributes": {
        "name": "Rock"
      }
    },
    {
      "id": 2,
      "type": "genre",
      "attributes": {
        "name": "Punk"
      }
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

#### Filtering

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


##### Examples:

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


