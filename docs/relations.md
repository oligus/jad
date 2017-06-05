# JAD

[<< Back](../README.md)

## Relationships

By default all relationships that a resource has will be listed when a resource or a list of resources get fetched.

Example, fetching a playlist with id 2 will also tell us there is a relationship named tracks.

Request:
```GET /api/v1/jad/playlists/2```

Response:
```json
{
  "data": {
    "id": 2,
    "type": "playlist",
    "attributes": {
      "name": "Some tracks"
    },
    "relationships": {
      "tracks": {
        "links": {
          "self": "http://api/v1/jad/playlists/2/relationship/tracks",
          "related": "http://api/v1/jad/playlists/2/tracks"
        }
      }
    }
  },
  "links": {
    "self": "http://api/v1/jad/playlists/2"
  }
}
```

### List the related resources

When you use the relationship link, related resources will be listed without attributes, only id and type will be listed.

Request:
```GET /api/v1/jad/playlists/2/relationship/tracks```

Response:
```json
{
  "data": [
    {
      "id": 15,
      "type": "track"
    },
    {
      "id": 645,
      "type": "track"
    }
  ],
  "links": {
    "self": "http://api/v1/jad/playlists/2/relationship/tracks"
  }
}
```

Or you can get the related resources in full

Request:
```GET /api/v1/jad/playlists/2/tracks```

Result:
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
      }
    },
    {
      "id": 645,
      "type": "track",
      "attributes": {
        "name": "Swedish Schnapps",
        "composer": "",
        "price": "0.99"
      }
    }
  ],
  "links": {
    "self": "http://api/v1/jad/playlists/2/tracks"
  }
}
```

### Including resources

To limit requests you can include related resources. You can also include relations of relations by using dot notation.

Example, you want playlist with id 2, you want all the tracks and you want the albums these tracks belong to:

Request:
```GET /api/v1/jad/playlists/2?include=tracks,tracks.album```

```json
{
  "data": {
    "id": 2,
    "type": "playlist",
    "attributes": {
      "name": "Some tracks"
    },
    "relationships": {
      "tracks": {
        "links": {
          "self": "http://api/v1/jad/playlists/2/relationship/tracks",
          "related": "http://api/v1/jad/playlists/2/tracks"
        }
      }
    },
    "included": [
      [
        {
          "id": 15,
          "type": "track",
          "attributes": {
            "name": "Go Down",
            "composer": "AC\/DC",
            "price": "0.99"
          }
        },
        {
          "id": 645,
          "type": "track",
          "attributes": {
            "name": "Swedish Schnapps",
            "composer": "",
            "price": "0.99"
          }
        },
      ],
      [
        {
          "id": 4,
          "type": "album",
          "attributes": {
            "title": "Let There Be Rock"
          }
        },
        {
          "id": 51,
          "type": "album",
          "attributes": {
            "title": "Up An' Atom"
          }
        }
      ]
    ]
  },
  "links": {
    "self": "http://api/v1/jad/playlists/2"
  }
}
```