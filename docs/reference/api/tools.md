<!--
status: generated
source: core/components/com_tools/api/controllers/
-->

# Tools API

Endpoints under `/api/tools`, from the `com_tools` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/tools/list`](#get-tools-list) | Method to get list of tools |
| `GET` | [`/tools/listAll`](#get-tools-listall) | Method to get list of tools |
| `GET` | [`/tools/output`](#get-tools-output) | Grabs the output from a tool session |
| `POST` | [`/tools/run`](#post-tools-run) | Runs a rappture job. |
| `GET` | [`/tools/screenshots/{user_id}`](#get-tools-screenshots-user-id) | Method to take session screenshots for user |
| `GET` | [`/tools/status`](#get-tools-status) | Gets the status of the session identified |
| `DELETE` | [`/tools/storage/delete`](#delete-tools-storage-delete) | Delete one file or folder from the member's storage. |
| `GET` | [`/tools/storage/files`](#get-tools-storage-files) | List the folders and files in one directory of the member's storage. |
| `POST` | [`/tools/storage/mkdir`](#post-tools-storage-mkdir) | Create a new folder in the member's storage. |
| `POST` | [`/tools/storage/rename`](#post-tools-storage-rename) | Rename a file or folder in the member's storage. |
| `POST` | [`/tools/storage/upload`](#post-tools-storage-upload) | Upload one or more files into a directory of the member's storage. |
| `GET` | [`/tools/storage/usage`](#get-tools-storage-usage) | Current disk-usage / quota for the authenticated member (read-only). |
| `GET` | [`/tools/{sessionid}/fileshare`](#get-tools-sessionid-fileshare) | Method to setup Windows filesharing connection |
| `GET` | [`/tools/{sessionid}/screenshot`](#get-tools-sessionid-screenshot) | Method to return session screenshot |
| `DELETE` | [`/tools/{session}`](#delete-tools-session) | Method to stop tool session |
| `GET` | [`/tools/{session}`](#get-tools-session) | Method to view tool session |
| `GET` | [`/tools/{session}/state`](#get-tools-session-state) | Report the launch/connection state of a tool session using ONLY the middleware database -- it never calls out to the middleware (maxwell). |
| `GET` | [`/tools/{session}/timeout`](#get-tools-session-timeout) | Report the timeout window for a tool session (DB-only; no middleware call). |
| `GET` | [`/tools/{session}/unshare`](#get-tools-session-unshare) | Method to disconnect from shared tool session |
| `GET` | [`/tools/{tool}`](#get-tools-tool) | Method to get tool information |
| `GET` | [`/tools/{tool}/invoke`](#get-tools-tool-invoke) | Method to invoke new tools session |
| `GET` | [`/tools/{tool}/rappturexml`](#get-tools-tool-rappturexml) | Method to get the Rappture definition file for a tool |
| `DELETE` | [`/tools/{user_id}`](#delete-tools-user-id) | Method to purge users storage |
| `GET` | [`/tools/{user_id}`](#get-tools-user-id) | Method to return users storage results |

## GET /tools/list

Method to get list of tools

API version 1.0, task `list` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L83).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `user_id` | integer | yes | 0 | Member identifier |

## GET /tools/listAll

Method to get list of tools

API version 1.0, task `listAll` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L32).

## GET /tools/output

Grabs the output from a tool session

API version 1.0, task `output` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1073).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `session_num` | string | yes | 0 | a valid hub tool session number |
| `run_file` | string | yes | — | the name of the run file that contains the desired output |

## POST /tools/run

Runs a rappture job.

API version 1.0, task `run` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L762).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `app` | string | yes | — | Name of app installed as a tool in the hub |
| `revision` | string | no | default | The specific requested revision of the app |
| `xml` | string | yes | — | Content of the driver file that rappture will use to invoke the given app |

## GET /tools/screenshots/{user_id}

Method to take session screenshots for user

API version 1.0, task `screenshots` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L280).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `user_id` | integer | yes | 0 | Member identifier |

## GET /tools/status

Gets the status of the session identified

API version 1.0, task `status` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L983).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `session_num` | string | yes | 0 | a valid hub tool session number |

## DELETE /tools/storage/delete

Delete one file or folder from the member's storage.

API version 1.0, task `delete` in [`storagev1r0.php`](../../../core/components/com_tools/api/controllers/storagev1r0.php#L158).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `dir` | string | no | — | Directory the item lives in, relative to the storage root |
| `name` | string | yes | — | File or folder name to delete |
| `type` | string | no | file | file or folder |

## GET /tools/storage/files

List the folders and files in one directory of the member's storage.

API version 1.0, task `files` in [`storagev1r0.php`](../../../core/components/com_tools/api/controllers/storagev1r0.php#L75).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `dir` | string | no | — | Directory relative to the member's storage root |

## POST /tools/storage/mkdir

Create a new folder in the member's storage.

API version 1.0, task `mkdir` in [`storagev1r0.php`](../../../core/components/com_tools/api/controllers/storagev1r0.php#L238).

## POST /tools/storage/rename

Rename a file or folder in the member's storage.

API version 1.0, task `rename` in [`storagev1r0.php`](../../../core/components/com_tools/api/controllers/storagev1r0.php#L277).

## POST /tools/storage/upload

Upload one or more files into a directory of the member's storage.

API version 1.0, task `upload` in [`storagev1r0.php`](../../../core/components/com_tools/api/controllers/storagev1r0.php#L328).

## GET /tools/storage/usage

Current disk-usage / quota for the authenticated member (read-only).

API version 1.0, task `usage` in [`storagev1r0.php`](../../../core/components/com_tools/api/controllers/storagev1r0.php#L25).

## GET /tools/{sessionid}/fileshare

Method to setup Windows filesharing connection

API version 1.0, task `fileshare` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1751).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `sessionid` | integer | yes | 0 | Tool session identifier |
| `username` | string | no | — | Username |
| `private_ip` | string | no | — | Private IP Address |
| `public_ip` | string | no | — | Public IP Address |

## GET /tools/{sessionid}/screenshot

Method to return session screenshot

API version 1.0, task `screenshot` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L326).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `sessionid` | integer | yes | 0 | Tool session identifier |
| `type` | string | no | png | Image format |
| `notfound` | integer | no | 0 | Not found |

## DELETE /tools/{session}

Method to stop tool session

API version 1.0, task `delete` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1507).

## GET /tools/{session}

Method to view tool session

API version 1.0, task `read` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1147).

## GET /tools/{session}/state

Report the launch/connection state of a tool session using ONLY the middleware database -- it never calls out to the middleware (maxwell).

API version 1.0, task `state` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1259).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `session` | integer | yes | 0 | A valid hub tool session number |
| `mode` | string | no | vnc | Connection style to evaluate readiness for: 'vnc' or 'redirect' |

## GET /tools/{session}/timeout

Report the timeout window for a tool session (DB-only; no middleware call).

API version 1.0, task `timeout` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1418).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `session` | integer | yes | 0 | A valid hub tool session number |

## GET /tools/{session}/unshare

Method to disconnect from shared tool session

API version 1.0, task `unshare` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1583).

## GET /tools/{tool}

Method to get tool information

API version 1.0, task `info` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L142).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `tool` | string | yes | — | Tool identifier |
| `tool_version` | string | yes | current | Tool version |

## GET /tools/{tool}/invoke

Method to invoke new tools session

API version 1.0, task `invoke` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L571).

## GET /tools/{tool}/rappturexml

Method to get the Rappture definition file for a tool

API version 1.0, task `rapptureXML` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L459).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `xpath` | string | no | — | Traversal selecting desired area(s) of the document |

## DELETE /tools/{user_id}

Method to purge users storage

API version 1.0, task `purge` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1678).

## GET /tools/{user_id}

Method to return users storage results

API version 1.0, task `storage` in [`sessionsv1r0.php`](../../../core/components/com_tools/api/controllers/sessionsv1r0.php#L1633).
