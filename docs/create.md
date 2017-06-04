# JAD

[<< Back](../README.md)

## Create a new resource

To create a new resource item post to the resource type link with a request body containing type and attributes.

```
POST /api/v1/jad/genres
```

Request body:
```json
{
  "data": {
    "type": "genre",
    "attributes": {
      "name": "New Genre"
    }   
  }
}
```

Response:
```json
{
  "data": {
    "id":26,
    "type":"genre",
    "attributes": {
      "name":"Created Genre"
    }
  },
  "links": {
    "self":"http:\/\/:\/genres"
  }
}
```

