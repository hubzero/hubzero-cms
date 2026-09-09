<!--
status: generated
source: core/components/com_wiki/api/controllers/
-->

# Wiki API

Endpoints under `/api/wiki`, from the `com_wiki` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/wiki`](#post-wiki) | Create a page |
| `GET` | [`/wiki/list`](#get-wiki-list) | Display a list of pages |
| `DELETE` | [`/wiki/{id}`](#delete-wiki-id) | Delete a page |
| `GET` | [`/wiki/{id}`](#get-wiki-id) | Display info for a page |
| `PUT` | [`/wiki/{id}`](#put-wiki-id) | Update a page |

## POST /wiki

Create a page

API version 1.0, task `create` in [`pagesv1_0.php`](../../../core/components/com_wiki/api/controllers/pagesv1_0.php#L130).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `title` | string | yes | — | Entry title |
| `pagename` | string | no | — | Page name |
| `pagetext` | string | yes | — | Page content |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (0 = public, 1 = registered users, 4 = private) |
| `scope` | string | no | site | Page scope |
| `scope_id` | integer | no | 0 | Page scope ID |
| `params` | array | no | — | Page options |

## GET /wiki/list

Display a list of pages

API version 1.0, task `list` in [`pagesv1_0.php`](../../../core/components/com_wiki/api/controllers/pagesv1_0.php#L30).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | title | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |
| `scope` | string | no | site | Page scope |
| `scope_id` | integer | no | 0 | Page scope ID |

## DELETE /wiki/{id}

Delete a page

API version 1.0, task `delete` in [`pagesv1_0.php`](../../../core/components/com_wiki/api/controllers/pagesv1_0.php#L482).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Page identifier |

## GET /wiki/{id}

Display info for a page

API version 1.0, task `read` in [`pagesv1_0.php`](../../../core/components/com_wiki/api/controllers/pagesv1_0.php#L212).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Page identifier |
| `version_id` | integer | no | 0 | Optional revision ID. If none specified, page will default to most current approved revision. |

## PUT /wiki/{id}

Update a page

API version 1.0, task `update` in [`pagesv1_0.php`](../../../core/components/com_wiki/api/controllers/pagesv1_0.php#L271).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |
| `title` | string | yes | — | Entry title |
| `pagename` | string | no | — | Page name |
| `pagetext` | string | yes | — | Page content |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (0 = public, 1 = registered users, 4 = private) |
| `scope` | string | no | site | Page scope |
| `scope_id` | integer | no | 0 | Page scope ID |
| `params` | array | no | — | Page options |
| `summary` | string | no | — | Summary of changes made |
