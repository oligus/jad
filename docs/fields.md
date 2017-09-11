# JAD

[<< Back](../README.md)

## Sparse field sets

When the resources have to much data you can limit fields to be rendered in the result by just include the fields
you require.

### Parameters

The ordering parameter consist of two parts, resource type and list of fields for inclusion.

Examples:

```
GET /api/v1/jad/customers?fields[customers]=first-name
```
_List customers first names only_

```
GET /api/v1/jad/customers?fields[customers]=first-name,second-name
```
_List customers first and second name_

```
GET /api/v1/jad/customers?include=invoices&fields[customers]=first-name,second-name&fields[invoices]=total
```
_List customers first and second name and include total field for invoices_
