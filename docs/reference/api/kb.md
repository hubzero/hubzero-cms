<!--
status: generated
source: core/components/com_kb/api/controllers/
-->

# Kb API

Endpoints under `/api/kb`, from the `com_kb` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/kb`](#post-kb) | Create a knowledge base article |
| `GET,` | [`/kb/categories`](#get-kb-categories) | The knowledge base categories |
| `GET` | [`/kb/list`](#get-kb-list) | List knowledge base articles |
| `DELETE` | [`/kb/{id}`](#delete-kb-id) | Delete a knowledge base article |
| `GET` | [`/kb/{id}`](#get-kb-id) | Get a single knowledge base article |
| `PUT` | [`/kb/{id}`](#put-kb-id) | Update a knowledge base article |

## POST /kb

Create a knowledge base article

API version 1.0, task `create` in [`entriesv1_0.php`](../../../core/components/com_kb/api/controllers/entriesv1_0.php#L289).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `title` | string | yes | — | Article title. |
| `fulltxt` | string | yes | — | Article body, as wiki markup. |
| `category` | integer | yes | — | ID of the knowledge base category the article belongs to. |
| `alias` | string | no | — | URL alias, unique within the category. Derived from the title when absent. |
| `state` | integer | no | 1 | Published state. |
| `access` | integer | no | 1 | Viewing access level. |
| `created` | string | no | — | Creation timestamp. Defaults to now. |
| `created_by` | integer | no | — | ID of the user the article is credited to. Defaults to the caller. |
| `tags` | string | no | — | Comma-separated list of tags to apply. |

## GET, /kb/categories

The knowledge base categories

API version 1.0, task `categories` in [`entriesv1_0.php`](../../../core/components/com_kb/api/controllers/entriesv1_0.php#L512).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | no | — | Category ID. Required to read, update or delete one; omitted to list them or create one. |
| `title` | string | no | — | Category title. Required on create. |
| `alias` | string | no | — | URL alias, unique among the category's siblings. Derived from the title when absent. |
| `parent_id` | integer | no | 1 | ID of the parent category; the root (1) makes it top-level. Passing it on an update moves the category, and its descendants with it. |
| `description` | string | no | — | Category description. |
| `note` | string | no | — | Administrative note. |
| `published` | string | no | 1 | Published state. On a listing, the state to filter by -- Super Users only, 'all' for every state. |
| `access` | integer | no | 1 | Viewing access level. |
| `parent` | integer | no | — | On a listing, only the immediate children of this category (ID). |

## GET /kb/list

List knowledge base articles

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_kb/api/controllers/entriesv1_0.php#L94).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of results to return. |
| `limitstart` | integer | no | 0 | Offset to start returning results from (snapped to a multiple of limit). |
| `category` | string | no | — | Only articles in this category, by ID, alias or path. |
| `search` | string | no | — | Only articles whose title or text contains this. |
| `state` | string | no | 1 | Only articles in this state, from published and unpublished categories alike. Super Users only; anyone else sees published articles whatever they pass. 'all' for every state. |
| `sort` | string | no | title | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |

## DELETE /kb/{id}

Delete a knowledge base article

API version 1.0, task `delete` in [`entriesv1_0.php`](../../../core/components/com_kb/api/controllers/entriesv1_0.php#L477).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Article ID |

## GET /kb/{id}

Get a single knowledge base article

API version 1.0, task `read` in [`entriesv1_0.php`](../../../core/components/com_kb/api/controllers/entriesv1_0.php#L243).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Article ID |

## PUT /kb/{id}

Update a knowledge base article

API version 1.0, task `update` in [`entriesv1_0.php`](../../../core/components/com_kb/api/controllers/entriesv1_0.php#L382).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Article ID |
| `title` | string | no | — | Article title. |
| `fulltxt` | string | no | — | Article body, as wiki markup. |
| `category` | integer | no | — | ID of the knowledge base category the article belongs to. |
| `alias` | string | no | — | URL alias, unique within the category. |
| `state` | integer | no | — | Published state. |
| `access` | integer | no | — | Viewing access level. |
| `tags` | string | no | — | Comma-separated list of tags, replacing the article's current ones. |
