# JAD

[<< Back](../README.md)

## Create a new resource

To create a new resource item, post to the resource type link with a request body containing type and attributes.

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
      "name":"New Genre"
    }
  },
  "links": {
    "self":"http://api/v1/jad/genres"
  }
}
```

## Create a new resource with relationships

Create a new playlist with tracks

```
POST /api/v1/jad/playlists
```

Request body:
```json
{
  "data": {
    "type": "playlist",
    "attributes": {
      "name": "New Playlist"
    },
   "relationships": {
    "tracks": {
      "data": [
        { "type": "track", "id": 15 },
        { "type": "track", "id": 43 },
        { "type": "track", "id": 77 },
        { "type": "track", "id": 117 },
        { "type": "track", "id": 351 }
      ]
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
      "self":"http://api/v1/jad/playlist"
   }
}
```

Verifying that the tracks actually have been added to the playlist:

`GET /api/v1/jad/playlist/4/relationship/tracks`

Response:
```json
{
   "data":[
      {
         "id":15,
         "type":"track"
      },
      {
         "id":43,
         "type":"track"
      },
      {
         "id":77,
         "type":"track"
      },
      {
         "id":117,
         "type":"track"
      },
      {
         "id":351,
         "type":"track"
      }
   ],
   "links":{
      "self":"/api/v1/jad/playlist/4/relationship/tracks"
   }
}
```
