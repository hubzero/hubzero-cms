<!--
status: generated
source: core/components/com_billboards/api/controllers/
-->

# Billboards API

Endpoints under `/api/billboards`, from the `com_billboards` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/billboards/list`](#get-billboards-list) | Lists an index of existing billboard collections |
| `GET` | [`/billboards/{id}`](#get-billboards-id) | Lists all billboards for a given collection |

## GET /billboards/list

Lists an index of existing billboard collections

API version 1.0, task `list` in [`collectionsv1_0.php`](../../../core/components/com_billboards/api/controllers/collectionsv1_0.php#L24).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |

## GET /billboards/{id}

Lists all billboards for a given collection

API version 1.0, task `read` in [`collectionsv1_0.php`](../../../core/components/com_billboards/api/controllers/collectionsv1_0.php#L57).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Collection identifier |
