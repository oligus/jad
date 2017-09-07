# JAD

[<< Back](../README.md)

## Ordering

Order results with the sort parameter.

### Parameters

The ordering parameter consist of two parts, ordering field and direction.
Direction defaults to ASC, putting a hyphen `-` in front of the field indicates DESC ordering.

Examples:

```
GET /api/v1/jad/customers?sort=first-name
```
_Order records by first name ascending_

```
GET /api/v1/jad/customers?sort=-first-name
```
_Order records by first name descending_

```
GET /api/v1/jad/customers?sort=first-name,city
```
_Order records by first name ascending, city ascending_

```
GET /api/v1/jad/customers?sort=city,-country
```
_Order records by first city ascending, country descending_
