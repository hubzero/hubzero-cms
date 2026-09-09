<!--
status: generated
source: core/components/com_content/api/controllers/
-->

# Content API

Endpoints under `/api/content`, from the `com_content` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/content/list`](#get-content-list) | Get a list of resources |

## GET /content/list

Get a list of resources

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_content/api/controllers/entriesv1_0.php#L24).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
