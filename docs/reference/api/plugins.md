<!--
status: generated
source: core/components/com_plugins/api/controllers/
-->

# Plugins API

Endpoints under `/api/plugins`, from the `com_plugins` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/plugins`](#post-plugins) | Create an entry |
| `GET` | [`/plugins/list`](#get-plugins-list) | Display a list of entries |
| `GET` | [`/plugins/trigger`](#get-plugins-trigger) | Trigger a specific event |
| `PUT` | [`/plugins/{extension_id}`](#put-plugins-extension-id) | Update an entry |
| `DELETE` | [`/plugins/{id}`](#delete-plugins-id) | Delete an entry |
| `GET` | [`/plugins/{id}`](#get-plugins-id) | Retrieve an entry |

## POST /plugins

Create an entry

API version 1.0, task `create` in [`entriesv1_0.php`](../../../core/components/com_plugins/api/controllers/entriesv1_0.php#L139).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `name` | string | yes | — | Name |
| `element` | string | yes | — | Element |
| `folder` | string | yes | — | Folder (plugin group) |
| `enabled` | integer | no | 0 | Enabled |
| `access` | integer | no | 1 | Access |
| `params` | string | no | — | JSON Encoded list of params |

## GET /plugins/list

Display a list of entries

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_plugins/api/controllers/entriesv1_0.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `folder` | string | no | — | Folder (plugin group) |
| `enabled` | integer | no | 1 | Enabled |
| `access` | integer | no | 1 | Access |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /plugins/trigger

Trigger a specific event

API version 1.0, task `trigger` in [`entriesv1_0.php`](../../../core/components/com_plugins/api/controllers/entriesv1_0.php#L415).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `event` | string | yes | — | Event name with optional plugin group in dot notation (group.event) |
| `folder` | string | no | — | Folder (plugin group) |

## PUT /plugins/{extension_id}

Update an entry

API version 1.0, task `update` in [`entriesv1_0.php`](../../../core/components/com_plugins/api/controllers/entriesv1_0.php#L261).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `extension_id` | integer | yes | — | Extension identifier |
| `name` | string | no | — | Name |
| `element` | string | no | — | Element |
| `folder` | string | no | — | Folder (plugin group) |
| `enabled` | integer | no | 0 | Enabled |
| `access` | integer | no | 1 | Access |
| `params` | string | no | — | JSON Encoded list of params |

## DELETE /plugins/{id}

Delete an entry

API version 1.0, task `delete` in [`entriesv1_0.php`](../../../core/components/com_plugins/api/controllers/entriesv1_0.php#L360).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `extension_id` | integer | yes | 0 | Extension identifier |

## GET /plugins/{id}

Retrieve an entry

API version 1.0, task `read` in [`entriesv1_0.php`](../../../core/components/com_plugins/api/controllers/entriesv1_0.php#L225).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Extension identifier |
