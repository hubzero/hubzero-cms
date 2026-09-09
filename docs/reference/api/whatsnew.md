<!--
status: generated
source: core/components/com_whatsnew/api/controllers/
-->

# Whatsnew API

Endpoints under `/api/whatsnew`, from the `com_whatsnew` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/whatsnew/list`](#get-whatsnew-list) | Displays a list of new content |

## GET /whatsnew/list

Displays a list of new content

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_whatsnew/api/controllers/entriesv1_0.php#L24).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `period` | string | no | year | Time period to return results for. |
| `category` | string | no | — | Category to filter by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
