<!--
status: generated
source: core/components/com_publications/api/controllers/
-->

# Publications API

Endpoints under `/api/publications`, from the `com_publications` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/publications/list` (v1.0)](#get-publications-list-v1-0) | Display publications user authors |
| `GET` | [`/publications/list` (v1.1)](#get-publications-list-v1-1) | Display publications user is listed as author |

## GET /publications/list (v1.0)

Display publications user authors

API version 1.0, task `list` in [`publicationsv1_0.php`](../../../core/components/com_publications/api/controllers/publicationsv1_0.php#L28).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 0 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | title | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /publications/list (v1.1)

Display publications user is listed as author

API version 1.1, task `list` in [`publicationsv1_1.php`](../../../core/components/com_publications/api/controllers/publicationsv1_1.php#L28).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 0 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | title | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
