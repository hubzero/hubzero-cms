<!--
status: generated
source: core/components/com_groups/api/controllers/
-->

# Groups API

Endpoints under `/api/groups`, from the `com_groups` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/groups` (v1.0)](#post-groups-v1-0) | Create a group |
| `POST` | [`/groups` (v1.1)](#post-groups-v1-1) | Create a group |
| `GET` | [`/groups/list` (v1.0)](#get-groups-list-v1-0) | Display a list of groups |
| `GET` | [`/groups/list` (v1.1)](#get-groups-list-v1-1) | Display a list of groups |
| `POST` | [`/groups/{group}/{plugin}`](#post-groups-group-plugin) | Create a record for a plugin type |
| `GET` | [`/groups/{group}/{plugin}/list`](#get-groups-group-plugin-list) | Display a list of records for a plugin type |
| `DELETE` | [`/groups/{group}/{plugin}/{id}`](#delete-groups-group-plugin-id) | Delete a record for a plugin type. |
| `PUT` | [`/groups/{group}/{plugin}/{id}`](#put-groups-group-plugin-id) | Update a record for a plugin type. |
| `GET` | [`/groups/{group}/{plugin}/{record_id}`](#get-groups-group-plugin-record-id) | Retrieve a record for a plugin type. |
| `DELETE` | [`/groups/{id}` (v1.0)](#delete-groups-id-v1-0) | Delete a group |
| `DELETE` | [`/groups/{id}` (v1.1)](#delete-groups-id-v1-1) | Delete a group |
| `GET` | [`/groups/{id}` (v1.0)](#get-groups-id-v1-0) | Retrieve a group record |
| `GET` | [`/groups/{id}` (v1.1)](#get-groups-id-v1-1) | Retrieve a group record |
| `PUT` | [`/groups/{id}` (v1.0)](#put-groups-id-v1-0) | Update a group |
| `PUT` | [`/groups/{id}` (v1.1)](#put-groups-id-v1-1) | Update a group |
| `GET` | [`/groups/{id}/members/list`](#get-groups-id-members-list) | Display members of a group |

## POST /groups (v1.0)

Create a group

API version 1.0, task `create` in [`groupsv1_0.php`](../../../core/components/com_groups/api/controllers/groupsv1_0.php#L109).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `cn` | string | yes | — | Group alias that appears in the url for group. Only lowercase alphanumeric chars allowed. |
| `title` | string | yes | — | Group title |
| `tags` | string (comma separated) | no | — | Group tags |
| `public_description` | string | no | — | Group public description |
| `private_description` | string | no | — | Group private description |
| `join_policy` | string | yes | open | Membership join policy |
| `discoverability` | string | yes | visible | Is the group shown in hub searches/listings. |

## POST /groups (v1.1)

Create a group

API version 1.1, task `create` in [`groupsv1_1.php`](../../../core/components/com_groups/api/controllers/groupsv1_1.php#L109).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `cn` | string | yes | — | Group alias that appears in the url for group. Only lowercase alphanumeric chars allowed. |
| `title` | string | yes | — | Group title |
| `tags` | string (comma separated) | no | — | Group tags |
| `public_description` | string | no | — | Group public description |
| `private_description` | string | no | — | Group private description |
| `join_policy` | string | yes | open | Membership join policy |
| `discoverability` | string | yes | visible | Is the group shown in hub searches/listings. |

## GET /groups/list (v1.0)

Display a list of groups

API version 1.0, task `list` in [`groupsv1_0.php`](../../../core/components/com_groups/api/controllers/groupsv1_0.php#L25).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
| `fields` | string | no | gidNumber,cn,description,created,created_by | Comma-separated list of fields to return |

## GET /groups/list (v1.1)

Display a list of groups

API version 1.1, task `list` in [`groupsv1_1.php`](../../../core/components/com_groups/api/controllers/groupsv1_1.php#L25).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
| `fields` | string | no | gidNumber,cn,description,created,created_by | Comma-separated list of fields to return |

## POST /groups/{group}/{plugin}

Create a record for a plugin type

API version 1.0, task `create` in [`pluginsv1_0.php`](../../../core/components/com_groups/api/controllers/pluginsv1_0.php#L122).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Group ID or alias that appears in the url for group. |
| `active` | string | yes | — | Data type. This is the 'active' plugin such as blog, forum, etc. |

## GET /groups/{group}/{plugin}/list

Display a list of records for a plugin type

API version 1.0, task `list` in [`pluginsv1_0.php`](../../../core/components/com_groups/api/controllers/pluginsv1_0.php#L24).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Group ID or alias that appears in the url for group. |
| `active` | string | yes | — | Data type. This is the 'active' plugin such as blog, forum, etc. |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |

## DELETE /groups/{group}/{plugin}/{id}

Delete a record for a plugin type.

API version 1.0, task `delete` in [`pluginsv1_0.php`](../../../core/components/com_groups/api/controllers/pluginsv1_0.php#L379).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Group ID or alias that appears in the url for group. |
| `active` | string | yes | — | Data type. This is the 'active' plugin such as blog, forum, etc. |
| `record_id` | integer | yes | — | Unique identifier |

## PUT /groups/{group}/{plugin}/{id}

Update a record for a plugin type.

API version 1.0, task `update` in [`pluginsv1_0.php`](../../../core/components/com_groups/api/controllers/pluginsv1_0.php#L290).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Group ID or alias that appears in the url for group. |
| `active` | string | yes | — | Data type. This is the 'active' plugin such as blog, forum, etc. |
| `record_id` | integer | yes | — | Unique identifier |

## GET /groups/{group}/{plugin}/{record_id}

Retrieve a record for a plugin type.

API version 1.0, task `read` in [`pluginsv1_0.php`](../../../core/components/com_groups/api/controllers/pluginsv1_0.php#L201).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Group ID or alias that appears in the url for group. |
| `active` | string | yes | — | Data type. This is the 'active' plugin such as blog, forum, etc. |
| `record_id` | integer | yes | — | Unique identifier |

## DELETE /groups/{id} (v1.0)

Delete a group

API version 1.0, task `delete` in [`groupsv1_0.php`](../../../core/components/com_groups/api/controllers/groupsv1_0.php#L562).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Group identifier |

## DELETE /groups/{id} (v1.1)

Delete a group

API version 1.1, task `delete` in [`groupsv1_1.php`](../../../core/components/com_groups/api/controllers/groupsv1_1.php#L588).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Group identifier |

## GET /groups/{id} (v1.0)

Retrieve a group record

API version 1.0, task `read` in [`groupsv1_0.php`](../../../core/components/com_groups/api/controllers/groupsv1_0.php#L286).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Group unique identifier |
| `fields` | string | no | gidNumber,cn,description,created,created_by | Comma-separated list of fields to return |

## GET /groups/{id} (v1.1)

Retrieve a group record

API version 1.1, task `read` in [`groupsv1_1.php`](../../../core/components/com_groups/api/controllers/groupsv1_1.php#L286).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Group unique identifier |
| `fields` | string | no | gidNumber,cn,description,created,created_by | Comma-separated list of fields to return |

## PUT /groups/{id} (v1.0)

Update a group

API version 1.0, task `update` in [`groupsv1_0.php`](../../../core/components/com_groups/api/controllers/groupsv1_0.php#L400).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Group identifier |
| `title` | string | yes | — | Group title |
| `tags` | string (comma separated) | no | — | Group tags |
| `public_description` | string | no | — | Group public description |
| `private_description` | string | no | — | Group private description |
| `join_policy` | string | yes | open | Membership join policy |
| `discoverability` | string | yes | visible | Is the group shown in hub searches/listings. |

## PUT /groups/{id} (v1.1)

Update a group

API version 1.1, task `update` in [`groupsv1_1.php`](../../../core/components/com_groups/api/controllers/groupsv1_1.php#L426).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Group identifier |
| `title` | string | yes | — | Group title |
| `tags` | string (comma separated) | no | — | Group tags |
| `public_description` | string | no | — | Group public description |
| `private_description` | string | no | — | Group private description |
| `join_policy` | string | yes | open | Membership join policy |
| `discoverability` | string | yes | visible | Is the group shown in hub searches/listings. |

## GET /groups/{id}/members/list

Display members of a group

API version 1.0, task `list` in [`membersv1_0.php`](../../../core/components/com_groups/api/controllers/membersv1_0.php#L21).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Group identifier |
| `list` | string | no | — | Comma-separated list of member status |
| `start` | integer | no | 0 | Number of where to start returning results. |
