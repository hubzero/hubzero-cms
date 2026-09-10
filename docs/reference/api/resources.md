<!--
status: generated
source: core/components/com_resources/api/controllers/
-->

# Resources API

Endpoints under `/api/resources`, from the `com_resources` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/resources/autocomplete`](#get-resources-autocomplete) | A simple search on title and id for use with autocomplete |
| `GET` | [`/resources/list` (v1.0)](#get-resources-list-v1-0) | Get a list of resources |
| `GET` | [`/resources/list` (v1.1)](#get-resources-list-v1-1) | Get a list of resources |
| `GET` | [`/resources/renderlatex` (v1.0)](#get-resources-renderlatex-v1-0) | Render LaTeX expression |
| `GET` | [`/resources/renderlatex` (v1.1)](#get-resources-renderlatex-v1-1) | Render LaTeX expression |
| `GET` | [`/resources/whatsnew` (v1.0)](#get-resources-whatsnew-v1-0) | Get a list of new content for a given time period |
| `GET` | [`/resources/whatsnew` (v1.1)](#get-resources-whatsnew-v1-1) | Get a list of new content for a given time period |

## GET /resources/autocomplete

A simple search on title and id for use with autocomplete

API version 1.1, task `autocomplete` in [`entriesv1_1.php`](../../../core/components/com_resources/api/controllers/entriesv1_1.php#L392).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `search` | string | no | — | Term to search resource id or title |
| `existingCids` | array | no | — | List of Resource IDs to exclude from the search |

## GET /resources/list (v1.0)

Get a list of resources

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_resources/api/controllers/entriesv1_0.php#L28).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `type` | string | no | — | Type of resource to filter results. |
| `sortby` | string | no | date | Value to sort results by. |
| `search` | string | no | — | A word or phrase to search for. |

## GET /resources/list (v1.1)

Get a list of resources

API version 1.1, task `list` in [`entriesv1_1.php`](../../../core/components/com_resources/api/controllers/entriesv1_1.php#L30).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `type` | string | no | — | Type of resource to filter results. |
| `sortby` | string | no | date | Value to sort results by. |
| `search` | string | no | — | A word or phrase to search for. |

## GET /resources/renderlatex (v1.0)

Render LaTeX expression

API version 1.0, task `renderlatex` in [`entriesv1_0.php`](../../../core/components/com_resources/api/controllers/entriesv1_0.php#L275).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `expression` | string | yes | — | LaTeX expression |

## GET /resources/renderlatex (v1.1)

Render LaTeX expression

API version 1.1, task `renderlatex` in [`entriesv1_1.php`](../../../core/components/com_resources/api/controllers/entriesv1_1.php#L455).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `expression` | string | yes | — | LaTeX expression |

## GET /resources/whatsnew (v1.0)

Get a list of new content for a given time period

API version 1.0, task `whatsnew` in [`entriesv1_0.php`](../../../core/components/com_resources/api/controllers/entriesv1_0.php#L223).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `period` | string | no | month | Time period. |
| `category` | string | no | resources | Type of resource to filter results. |

## GET /resources/whatsnew (v1.1)

Get a list of new content for a given time period

API version 1.1, task `whatsnew` in [`entriesv1_1.php`](../../../core/components/com_resources/api/controllers/entriesv1_1.php#L340).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `period` | string | no | month | Time period. |
| `category` | string | no | resources | Type of resource to filter results. |
