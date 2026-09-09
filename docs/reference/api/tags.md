<!--
status: generated
source: core/components/com_tags/api/controllers/
-->

# Tags API

Endpoints under `/api/tags`, from the `com_tags` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/tags`](#post-tags) | Create an entry |
| `GET` | [`/tags/list`](#get-tags-list) | Displays a list of tags |
| `GET` | [`/tags/tagactivitylogs/previouslogs`](#get-tags-tagactivitylogs-previouslogs) | Retrieve tag's logs that preceeded the given log |
| `DELETE` | [`/tags/{id}`](#delete-tags-id) | Delete an entry |
| `GET` | [`/tags/{id}`](#get-tags-id) | Retrieve an entry |
| `PUT` | [`/tags/{id}`](#put-tags-id) | Update an entry |
| `DELETE` | [`/tags/{id}/add`](#delete-tags-id-add) | Add a tag to an item |
| `DELETE` | [`/tags/{id}/remove`](#delete-tags-id-remove) | Remove tag from an item |

## POST /tags

Create an entry

API version 1.0, task `create` in [`entriesv1_0.php`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L155).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `raw_tag` | string | yes | — | Tag text |
| `tag` | string | no | — | Normalized text (alpha-numeric, no punctuation) |
| `description` | string | yes | — | Longer description of a tag |
| `admin` | integer | no | 0 | Admin state (0 = no, 1 = yes) |
| `substitutes` | string | no | — | Comma-separated list of aliases or alternatives |

## GET /tags/list

Displays a list of tags

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L40).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | raw_tag | Field to sort results by. |
| `sortDir` | string | no | desc | Direction to sort results by. |
| `scope` | string | no | — | Object scope (ex: group, resource, etc.) |
| `scope_id` | integer | no | 0 | Object scope ID. Typically a Resource ID, Group ID, etc. |
| `tagger` | integer | no | 0 | ID of user that tagged items. |

## GET /tags/tagactivitylogs/previouslogs

Retrieve tag's logs that preceeded the given log

API version 2.0, task `previousLogs` in [`tagactivitylogsv2_0.php`](../../../core/components/com_tags/api/controllers/tagactivitylogsv2_0.php#L26).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `tagId` | integer | yes | — | ID of associated tag |
| `logId` | integer | yes | — | ID of log to compare against |
| `limit` | integer | no | 100 | Limit of logs to retrieve |

## DELETE /tags/{id}

Delete an entry

API version 1.0, task `delete` in [`entriesv1_0.php`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L395).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Tag entry identifier |

## GET /tags/{id}

Retrieve an entry

API version 1.0, task `read` in [`entriesv1_0.php`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L260).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Tag entry identifier |

## PUT /tags/{id}

Update an entry

API version 1.0, task `update` in [`entriesv1_0.php`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L292).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Tag entry identifier |
| `raw_tag` | string | no | — | Tag text |
| `tag` | string | no | — | Normalized text (alpha-numeric, no punctuation) |
| `description` | string | no | — | Longer description of a tag |
| `admin` | integer | no | 0 | Admin state (0 = no, 1 = yes) |
| `substitutes` | string | no | — | Comma-separated list of aliases or alternatives |

## DELETE /tags/{id}/add

Add a tag to an item

API version 1.0, task `add` in [`entriesv1_0.php`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L500).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Tag entry identifier |
| `scope` | string | yes | — | Item type |
| `scope_id` | integer | yes | 0 | Item ID |
| `tagger` | integer | no | 0 | ID of user who tagged the item. |

## DELETE /tags/{id}/remove

Remove tag from an item

API version 1.0, task `remove` in [`entriesv1_0.php`](../../../core/components/com_tags/api/controllers/entriesv1_0.php#L434).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Tag entry identifier |
| `scope` | string | yes | — | Item type |
| `scope_id` | integer | yes | 0 | Item ID |
| `tagger` | integer | no | 0 | ID of user who tagged the item. Supplying this will only remove tags by this user. |
