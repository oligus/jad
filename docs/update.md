# JAD

[<< Back](../README.md)

## Updating new resource

To update a resource

```
PATCH /api/v1/jad/genres
```

Request body:
```json
{
  "data": {
    "type": "genre",
    "id": 26,
    "attributes": {
      "name": "Updated Genre"
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
      "name":"Updated Genre"
    }
  },
  "links": {
    "self":"http://api/v1/jad/genres"
  }
}
```

## Adding a relationship
 
```
PATCH /api/v1/jad/playlist/4
```

Request body:
```json
{
  "data": {
    "type": "playlist", 
    "relationships": {
      "tracks": {
        "data": {
          "type": "track",
          "id": 422
        }
      }
    }
  }   
}
```

Response:
```json
{
   "data":{
      "id":4,
      "type":"playlist",
      "attributes":{
         "name":"New Playlist"
      },
      "relationships":{
         "tracks":{
            "links":{
               "self":"http://api/v1/jad/playlist/4/relationship/tracks",
               "related":"http://api/v1/jad/playlist/4/tracks"
            }
         }
      }
   },
   "links":{
      "self":"http://api/v1/jad/playlist/4"
   }
}
```

Verifying that the track actually have been added to the playlist:

`GET /api/v1/jad/playlist/4/relationship/tracks`
