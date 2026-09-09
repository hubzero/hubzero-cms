<!--
status: generated
source: core/components/com_collections/api/controllers/
-->

# Collections API

Endpoints under `/api/collections`, from the `com_collections` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/collections`](#post-collections) | Create a collection |
| `GET` | [`/collections/list`](#get-collections-list) | Display a list of collections |
| `GET` | [`/collections/posts`](#get-collections-posts) | Display posts |
| `POST` | [`/collections/posts`](#post-collections-posts) | Create a post |
| `DELETE` | [`/collections/posts/{id}`](#delete-collections-posts-id) | Delete a post |
| `GET` | [`/collections/posts/{id}`](#get-collections-posts-id) | Retrieve a post |
| `PUT` | [`/collections/posts/{id}`](#put-collections-posts-id) | Update a post |
| `DELETE` | [`/collections/{id}`](#delete-collections-id) | Delete a collection |
| `GET` | [`/collections/{id}`](#get-collections-id) | Retrieve a collection |
| `PUT` | [`/collections/{id}`](#put-collections-id) | Update a collection |

## POST /collections

Create a collection

API version 1.0, task `create` in [`collectionsv1_0.php`](../../../core/components/com_collections/api/controllers/collectionsv1_0.php#L113).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `object_type` | string | yes | — | Object type (group, member, etc.) |
| `object_id` | integer | yes | — | Object ID |
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `description` | string | no | — | Entry description |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (0 = public, 1 = registered users, 4 = private) |
| `layout` | string | no | grid | How to display posts |
| `sort` | string | no | created | How to sort posts (created, ordering) |

## GET /collections/list

Display a list of collections

API version 1.0, task `list` in [`collectionsv1_0.php`](../../../core/components/com_collections/api/controllers/collectionsv1_0.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /collections/posts

Display posts

API version 1.0, task `list` in [`postsv1_0.php`](../../../core/components/com_collections/api/controllers/postsv1_0.php#L41).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `collection_id` | integer | no | — | Collection identifier |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## POST /collections/posts

Create a post

API version 1.0, task `create` in [`postsv1_0.php`](../../../core/components/com_collections/api/controllers/postsv1_0.php#L170).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `collection_id` | integer | yes | — | Collection identifier |
| `title` | string | yes | — | Entry title |
| `description` | string | no | — | Entry description |
| `url` | string | no | — | Entry URL; Requires 'type'='link' |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (0 = public, 1 = registered users, 4 = private) |
| `type` | string | no | file | Item type |
| `object_id` | integer | no | 0 | Object ID |

## DELETE /collections/posts/{id}

Delete a post

API version 1.0, task `delete` in [`postsv1_0.php`](../../../core/components/com_collections/api/controllers/postsv1_0.php#L584).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |

## GET /collections/posts/{id}

Retrieve a post

API version 1.0, task `read` in [`postsv1_0.php`](../../../core/components/com_collections/api/controllers/postsv1_0.php#L341).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |

## PUT /collections/posts/{id}

Update a post

API version 1.0, task `update` in [`postsv1_0.php`](../../../core/components/com_collections/api/controllers/postsv1_0.php#L415).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |
| `collection_id` | integer | no | — | Collection identifier |
| `title` | string | no | — | Entry title |
| `description` | string | no | — | Entry description |
| `url` | string | no | — | Entry URL; Requires 'type'='link' |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (0 = public, 1 = registered users, 4 = private) |
| `type` | string | no | file | Item type |
| `object_id` | integer | no | 0 | Object ID |

## DELETE /collections/{id}

Delete a collection

API version 1.0, task `delete` in [`collectionsv1_0.php`](../../../core/components/com_collections/api/controllers/collectionsv1_0.php#L398).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |

## GET /collections/{id}

Retrieve a collection

API version 1.0, task `read` in [`collectionsv1_0.php`](../../../core/components/com_collections/api/controllers/collectionsv1_0.php#L232).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |

## PUT /collections/{id}

Update a collection

API version 1.0, task `update` in [`collectionsv1_0.php`](../../../core/components/com_collections/api/controllers/collectionsv1_0.php#L266).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |
| `object_type` | string | yes | — | Object type (group, member, etc.) |
| `object_id` | integer | yes | — | Object ID |
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `description` | string | no | — | Entry description |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 0 | Access level (0 = public, 1 = registered users, 4 = private) |
| `layout` | string | no | grid | How to display posts |
| `sort` | string | no | created | How to sort posts (created, ordering) |
