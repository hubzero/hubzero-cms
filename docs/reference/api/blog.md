<!--
status: generated
source: core/components/com_blog/api/controllers/
-->

# Blog API

Endpoints under `/api/blog`, from the `com_blog` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/blog`](#post-blog) | Create an entry |
| `GET` | [`/blog/list`](#get-blog-list) | Display a list of entries |
| `DELETE` | [`/blog/{id}`](#delete-blog-id) | Delete an entry |
| `GET` | [`/blog/{id}`](#get-blog-id) | Retrieve an entry |
| `PUT` | [`/blog/{id}`](#put-blog-id) | Update an entry |

## POST /blog

Create an entry

API version 1.0, task `create` in [`entriesv1_0.php`](../../../core/components/com_blog/api/controllers/entriesv1_0.php#L135).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | yes | — | Scope type (group, member, etc.) |
| `scope_id` | integer | yes | — | Scope object ID |
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `content` | string | yes | — | Entry content |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (1 = public, 2 = registered users, 5 = private) |
| `allow_comments` | integer | no | 1 | Allow comments on the entry? |
| `publish_up` | string | no | now | Publish start timestamp (YYYY-MM-DD HH:mm:ss) |
| `publish_down` | string | no | — | Publish end timestamp (YYYY-MM-DD HH:mm:ss) |
| `tags` | string | no | — | Comma-separated list of tags |

## GET /blog/list

Display a list of entries

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_blog/api/controllers/entriesv1_0.php#L29).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | no | site | Scope type (group, member, etc.) |
| `scope_id` | integer | no | 0 | Scope object ID |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## DELETE /blog/{id}

Delete an entry

API version 1.0, task `delete` in [`entriesv1_0.php`](../../../core/components/com_blog/api/controllers/entriesv1_0.php#L555).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Blog entry identifier |

## GET /blog/{id}

Retrieve an entry

API version 1.0, task `read` in [`entriesv1_0.php`](../../../core/components/com_blog/api/controllers/entriesv1_0.php#L317).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Blog entry identifier |

## PUT /blog/{id}

Update an entry

API version 1.0, task `update` in [`entriesv1_0.php`](../../../core/components/com_blog/api/controllers/entriesv1_0.php#L352).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Blog entry identifier |
| `scope` | string | no | — | Scope type (group, member, etc.) |
| `scope_id` | integer | no | — | Scope object ID |
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `content` | string | yes | — | Entry content |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (1 = public, 2 = registered users, 5 = private) |
| `allow_comments` | integer | no | 1 | Allow comments on the entry? |
| `publish_up` | string | no | now | Publish start timestamp (YYYY-MM-DD HH:mm:ss) |
| `publish_down` | string | no | — | Publish end timestamp (YYYY-MM-DD HH:mm:ss) |
| `hits` | integer | no | 0 | Record hits |
| `tags` | string | no | — | Comma-separated list of tags |
