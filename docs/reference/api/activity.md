<!--
status: generated
source: core/components/com_activity/api/controllers/
-->

# Activity API

Endpoints under `/api/activity`, from the `com_activity` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/activity` (v1.0)](#post-activity-v1-0) | Create an entry |
| `POST` | [`/activity` (v1.1)](#post-activity-v1-1) | Create an entry |
| `GET` | [`/activity/list` (v1.0)](#get-activity-list-v1-0) | Display a list of entries |
| `GET` | [`/activity/list` (v1.1)](#get-activity-list-v1-1) | Display a list of entries |
| `DELETE` | [`/activity/{id}` (v1.0)](#delete-activity-id-v1-0) | Delete an entry |
| `DELETE` | [`/activity/{id}` (v1.1)](#delete-activity-id-v1-1) | Delete an entry |
| `GET` | [`/activity/{id}` (v1.0)](#get-activity-id-v1-0) | Retrieve an entry |
| `GET` | [`/activity/{id}` (v1.1)](#get-activity-id-v1-1) | Retrieve an entry |
| `PUT` | [`/activity/{id}` (v1.0)](#put-activity-id-v1-0) | Update an entry |
| `PUT` | [`/activity/{id}` (v1.1)](#put-activity-id-v1-1) | Update an entry |

## POST /activity (v1.0)

Create an entry

API version 1.0, task `create` in [`entriesv1_0.php`](../../../core/components/com_activity/api/controllers/entriesv1_0.php#L141).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | yes | — | Scope type (group, member, etc.) |
| `scope_id` | integer | yes | — | Scope object ID |
| `action` | string | yes | — | Action taken |
| `alias` | string | no | — | Entry alias |
| `description` | string | yes | — | Description of the activity |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `crated_by` | integer | no | 0 | User ID of entry creator |
| `anonymous` | integer | no | 0 | Anonymous (0 = false, 1 = true) |
| `parent` | integer | no | 0 | ID of parent activity |
| `recipients` | string | no | 1 | Comma-separated list of scope:scope_id pairs (ex: user:1001,group:1000) |

## POST /activity (v1.1)

Create an entry

API version 1.1, task `create` in [`entriesv1_1.php`](../../../core/components/com_activity/api/controllers/entriesv1_1.php#L354).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | yes | — | Scope type (group, member, etc.) |
| `scope_id` | integer | yes | — | Scope object ID |
| `action` | string | yes | — | Action taken |
| `alias` | string | no | — | Entry alias |
| `description` | string | yes | — | Description of the activity |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `crated_by` | integer | no | 0 | User ID of entry creator |
| `anonymous` | integer | no | 0 | Anonymous (0 = false, 1 = true) |
| `parent` | integer | no | 0 | ID of parent activity |
| `recipients` | string | no | 1 | Comma-separated list of scope:scope_id pairs (ex: user:1001,group:1000) |

## GET /activity/list (v1.0)

Display a list of entries

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_activity/api/controllers/entriesv1_0.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | no | site | Scope type (group, member, etc.) |
| `scope_id` | integer | no | 0 | Scope object ID |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /activity/list (v1.1)

Display a list of entries

API version 1.1, task `list` in [`entriesv1_1.php`](../../../core/components/com_activity/api/controllers/entriesv1_1.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | no | site | Scope type (group, member, etc.) |
| `scope_id` | integer | no | 0 | Scope object ID |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
| `start_date` | string | no | — | Start timestamp (YYYY-MM-DD or YYYY-MM-DD HH:mm:ss) |
| `end_date` | string | no | — | Start timestamp (YYYY-MM-DD or YYYY-MM-DD HH:mm:ss) |
| `recipients` | string | no | — | Filter by a list of recipients (type:id) the activity was sent to. Example: recipients=user:1000,project:2413 |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |

## DELETE /activity/{id} (v1.0)

Delete an entry

API version 1.0, task `delete` in [`entriesv1_0.php`](../../../core/components/com_activity/api/controllers/entriesv1_0.php#L595).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Activity entry identifier |

## DELETE /activity/{id} (v1.1)

Delete an entry

API version 1.1, task `delete` in [`entriesv1_1.php`](../../../core/components/com_activity/api/controllers/entriesv1_1.php#L837).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Activity entry identifier |

## GET /activity/{id} (v1.0)

Retrieve an entry

API version 1.0, task `read` in [`entriesv1_0.php`](../../../core/components/com_activity/api/controllers/entriesv1_0.php#L350).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Activity entry identifier |

## GET /activity/{id} (v1.1)

Retrieve an entry

API version 1.1, task `read` in [`entriesv1_1.php`](../../../core/components/com_activity/api/controllers/entriesv1_1.php#L572).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Activity entry identifier |

## PUT /activity/{id} (v1.0)

Update an entry

API version 1.0, task `update` in [`entriesv1_0.php`](../../../core/components/com_activity/api/controllers/entriesv1_0.php#L384).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Activity entry identifier |
| `scope` | string | no | — | Scope type (group, member, etc.) |
| `scope_id` | integer | no | — | Scope object ID |
| `action` | string | no | — | Action taken |
| `alias` | string | no | — | Entry alias |
| `description` | string | no | — | Description of the activity |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `crated_by` | integer | no | 0 | User ID of entry creator |
| `anonymous` | integer | no | 0 | Anonymous (0 = false, 1 = true) |
| `parent` | integer | no | 0 | ID of parent activity |
| `recipients` | integer | no | 1 | List of recpient channels |

## PUT /activity/{id} (v1.1)

Update an entry

API version 1.1, task `update` in [`entriesv1_1.php`](../../../core/components/com_activity/api/controllers/entriesv1_1.php#L615).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Activity entry identifier |
| `scope` | string | no | — | Scope type (group, member, etc.) |
| `scope_id` | integer | no | — | Scope object ID |
| `action` | string | no | — | Action taken |
| `alias` | string | no | — | Entry alias |
| `description` | string | no | — | Description of the activity |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `crated_by` | integer | no | 0 | User ID of entry creator |
| `anonymous` | integer | no | 0 | Anonymous (0 = false, 1 = true) |
| `parent` | integer | no | 0 | ID of parent activity |
| `recipients` | integer | no | 1 | List of recpient channels |
