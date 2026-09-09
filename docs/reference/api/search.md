<!--
status: generated
source: core/components/com_search/api/controllers/
-->

# Search API

Endpoints under `/api/search`, from the `com_search` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/search/getHubTypes`](#get-search-gethubtypes) | Display a list of hub types for a term |
| `GET` | [`/search/list`](#get-search-list) | Display a list of entries |
| `GET` | [`/search/suggest`](#get-search-suggest) | Display a list of suggestions for a term |
| `GET` | [`/search/typeSuggestions`](#get-search-typesuggestions) | Get suggestions for submitted terms |

## GET /search/getHubTypes

Display a list of hub types for a term

API version 1.0, task `getHubTypes` in [`searchv1_0.php`](../../../core/components/com_search/api/controllers/searchv1_0.php#L244).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `terms` | string | yes | *:* | Terms to search for. |

## GET /search/list

Display a list of entries

API version 1.0, task `list` in [`searchv1_0.php`](../../../core/components/com_search/api/controllers/searchv1_0.php#L25).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `type` | string | no | — | Content type (groups, members, etc.) |
| `limit` | integer | no | 10 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `terms` | string | yes | *:* | Terms to search for. |
| `sortBy` | string | no | — | Field to sort results by. |
| `sortDir` | string | no | — | Direction to sort results by. |
| `filters` | array | no | [] | Filters to apply to results. |

## GET /search/suggest

Display a list of suggestions for a term

API version 1.0, task `suggest` in [`searchv1_0.php`](../../../core/components/com_search/api/controllers/searchv1_0.php#L167).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `terms` | string | yes | — | Terms to get suggestions for. |

## GET /search/typeSuggestions

Get suggestions for submitted terms

API version 1.0, task `typeSuggestions` in [`searchv1_0.php`](../../../core/components/com_search/api/controllers/searchv1_0.php#L200).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `terms` | string | yes | — | Terms to search for. |
