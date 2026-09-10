<!--
status: generated
source: core/components/com_citations/api/controllers/
-->

# Citations API

Endpoints under `/api/citations`, from the `com_citations` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/citations/list` (v1.0)](#get-citations-list-v1-0) | Display a list of citations |
| `GET` | [`/citations/list` (v1.1)](#get-citations-list-v1-1) | Display a list of citations |

## GET /citations/list (v1.0)

Display a list of citations

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_citations/api/controllers/entriesv1_0.php#L23).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /citations/list (v1.1)

Display a list of citations

API version 1.1, task `list` in [`entriesv1_1.php`](../../../core/components/com_citations/api/controllers/entriesv1_1.php#L24).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
