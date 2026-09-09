<!--
status: generated
source: core/components/com_forum/api/controllers/
-->

# Forum API

Endpoints under `/api/forum`, from the `com_forum` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/api/v2.0/forum/userscategories/create`](#post-api-v2-0-forum-userscategories-create) | Create user's categories |
| `DELETE` | [`/api/v2.0/forum/userscategories/destroy`](#delete-api-v2-0-forum-userscategories-destroy) | Destroys user's categories |
| `POST` | [`/forum`](#post-forum) | Create a thread or post in a thread |
| `GET` | [`/forum/categories`](#get-forum-categories) | Display categories for a section |
| `GET` | [`/forum/list`](#get-forum-list) | Display a list of threads |
| `GET` | [`/forum/sections`](#get-forum-sections) | Display a list of sections |
| `GET` | [`/forum/{thread}`](#get-forum-thread) | Retrieve a thread |

## POST /api/v2.0/forum/userscategories/create

Create user's categories

API version 2.0, task `create` in [`userscategoriesv2_0.php`](../../../core/components/com_forum/api/controllers/userscategoriesv2_0.php#L19).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `category_id` | integer | yes | — | Forum category's ID |
| `user_id` | integer | yes | — | User's ID |

## DELETE /api/v2.0/forum/userscategories/destroy

Destroys user's categories

API version 2.0, task `destroy` in [`userscategoriesv2_0.php`](../../../core/components/com_forum/api/controllers/userscategoriesv2_0.php#L108).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `category_id` | integer | yes | — | Forum category's ID |
| `user_id` | integer | yes | — | User's ID |

## POST /forum

Create a thread or post in a thread

API version 1.0, task `create` in [`threadsv1_0.php`](../../../core/components/com_forum/api/controllers/threadsv1_0.php#L551).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `category_id` | integer | yes | 0 | Category ID |
| `scope` | string | yes | site | Scope type (site, group, etc.) |
| `scope_id` | integer | yes | 0 | Scope object ID |
| `title` | string | no | — | Entry title |
| `comment` | string | yes | — | Entry content |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 1 | Published state (0 = unpublished, 1 = published) |
| `access` | integer | no | 1 | Access level (1 = public, 2 = registered users, 5 = private) |
| `anonymous` | integer | no | 0 | Commentor is anonymous? |
| `parent` | integer | no | 0 | ID of the parent post this post is in reply to. |
| `thread` | string | no | 0 | ID of the forum thread the post belongs to. 0 if new thread. |
| `sticky` | integer | no | 0 | If the thread is sticky or not. Only applies to thread starter posts. |
| `closed` | integer | no | 0 | If the thread is closed (no more new posts) or not. Only applies to thread starter posts. |
| `tags` | string | no | — | Comma-separated list of tags |

## GET /forum/categories

Display categories for a section

API version 1.0, task `categories` in [`threadsv1_0.php`](../../../core/components/com_forum/api/controllers/threadsv1_0.php#L117).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `section` | integer | yes | 0 | Section ID |
| `search` | string | no | — | A word or phrase to search for. |
| `scope` | string | no | site | Scope (site, groups, members, etc.) |
| `scope_id` | integer | no | 0 | Scope ID |
| `closed` | integer | no | — | If the category is marked as closed (1) or not (0). NULL to return all. |

## GET /forum/list

Display a list of threads

API version 1.0, task `list` in [`threadsv1_0.php`](../../../core/components/com_forum/api/controllers/threadsv1_0.php#L304).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `section` | integer | no | 0 | Section ID. Find all posts for all categories within a section. |
| `category` | integer | no | 0 | Category ID. Find all posts within a category. |
| `threads_only` | boolean | no | no | Return only thread starter posts (true) or any post (false). |
| `parent` | integer | no | — | Parent post ID. Find all immediate descendent (replies) posts. |
| `thread` | integer | no | 0 | Thread ID. Find all posts in a specified thread. |
| `scope` | string | no | site | Scope (site, groups, members, etc.) |
| `scope_id` | integer | no | 0 | Scope ID |

## GET /forum/sections

Display a list of sections

API version 1.0, task `sections` in [`threadsv1_0.php`](../../../core/components/com_forum/api/controllers/threadsv1_0.php#L33).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | no | site | Scope (site, groups, members, etc.) |
| `scope_id` | integer | no | 0 | Scope ID |

## GET /forum/{thread}

Retrieve a thread

API version 1.0, task `read` in [`threadsv1_0.php`](../../../core/components/com_forum/api/controllers/threadsv1_0.php#L798).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Thread identifier |
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `section` | string | no | — | Section alias to filter by |
| `category` | string | no | — | Category alias to filter by |
| `state` | integer | no | 1 | Published state (0 = unpublished, 1 = published) |
| `scope` | string | no | site | Scope (site, groups, members, etc.) |
| `scope_id` | integer | no | 0 | Scope ID |
| `scope_sub_id` | integer | no | 0 | Scope sub-ID |
| `object_id` | integer | no | 0 | Object ID |
| `start_id` | integer | no | 0 | ID of record to start with |
| `start_at` | string | no | — | Start timestamp (YYYY-MM-DD HH:mm:ss) |
| `sort` | string | no | newest | Field to sort results by. |
