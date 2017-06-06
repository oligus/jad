# JAD

[<< Back](../README.md)

## Deleting a resource

Deleting a resource
```
DELETE /api/v1/jad/genres/26
```

Verify it has been deleted:
```
GET /api/v1/jad/genres/26
```

Response:
```json
{
   "errors":[
      {
         "status":404,
         "title":"Jad error",
         "detail":"Resource of type [genre] with id [26] could not be found."
      }
   ]
}
```
