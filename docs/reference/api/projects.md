<!--
status: generated
source: core/components/com_projects/api/controllers/
-->

# Projects API

Endpoints under `/api/projects`, from the `com_projects` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/projects`](#post-projects) | Create a project |
| `GET` | [`/projects/list`](#get-projects-list) | Display projects user belongs to |
| `GET` | [`/projects/list`](#get-projects-list) | Display projects user belongs to |
| `GET` | [`/projects/list`](#get-projects-list) | List projects |
| `DELETE` | [`/projects/{id}`](#delete-projects-id) | Delete a project |
| `GET` | [`/projects/{id}`](#get-projects-id) | Get project info (if user is in project) |
| `GET` | [`/projects/{id}`](#get-projects-id) | Get project info (if user is in project) |
| `GET` | [`/projects/{id}`](#get-projects-id) | Get project info (if user is in project) |
| `PUT` | [`/projects/{id}`](#put-projects-id) | Update a project |
| `GET` | [`/projects/{id}/files`](#get-projects-id-files) | Get a list of project files |
| `GET` | [`/projects/{id}/files`](#get-projects-id-files) | Get a list of project files |
| `GET` | [`/projects/{id}/files`](#get-projects-id-files) | Get a list of project files |
| `GET` | [`/projects/{id}/files/connections`](#get-projects-id-files-connections) | Get a list of project files connections |
| `GET,POST` | [`/projects/{id}/files/connections/{cid}/chunkedUpload`](#get-post-projects-id-files-connections-cid-chunkedupload) | Uploads file chunk(s) and combines them before adding the final file to repository |
| `GET` | [`/projects/{id}/files/connections/{cid}/download`](#get-projects-id-files-connections-cid-download) | Download file or folder from project (non-default connection providers only) |
| `GET` | [`/projects/{id}/files/connections/{cid}/getmetadata`](#get-projects-id-files-connections-cid-getmetadata) | Get file annotation |
| `GET` | [`/projects/{id}/files/connections/{cid}/setmetadata`](#get-projects-id-files-connections-cid-setmetadata) | Set file annotation |
| `POST` | [`/projects/{id}/files/connections/{cid}/upload`](#post-projects-id-files-connections-cid-upload) | upload/replace a project file (only for non-default connection providers) |
| `GET` | [`/projects/{id}/files/delete`](#get-projects-id-files-delete) | Delete file or folder from project |
| `GET` | [`/projects/{id}/files/delete`](#get-projects-id-files-delete) | Delete file or folder from project |
| `GET` | [`/projects/{id}/files/download`](#get-projects-id-files-download) | Download file or folder from project (non-default connection providers only) |
| `GET` | [`/projects/{id}/files/get`](#get-projects-id-files-get) | Get file(s) metadata |
| `GET` | [`/projects/{id}/files/get`](#get-projects-id-files-get) | Get file(s) metadata |
| `GET` | [`/projects/{id}/files/getmetadata`](#get-projects-id-files-getmetadata) | Get file annotation |
| `GET` | [`/projects/{id}/files/insert`](#get-projects-id-files-insert) | Insert/update a project file |
| `GET` | [`/projects/{id}/files/insert`](#get-projects-id-files-insert) | Insert/update a project file |
| `GET` | [`/projects/{id}/files/makedirectory`](#get-projects-id-files-makedirectory) | Create a folder in project local repo |
| `GET` | [`/projects/{id}/files/makedirectory`](#get-projects-id-files-makedirectory) | Create a folder in project local repo |
| `GET` | [`/projects/{id}/files/move`](#get-projects-id-files-move) | Move file or folder in project |
| `GET` | [`/projects/{id}/files/move`](#get-projects-id-files-move) | Move file or folder in project |
| `GET` | [`/projects/{id}/files/rename`](#get-projects-id-files-rename) | Move file or folder in project |
| `GET` | [`/projects/{id}/files/rename`](#get-projects-id-files-rename) | Move file or folder in project |
| `GET` | [`/projects/{id}/files/setmetadata`](#get-projects-id-files-setmetadata) | Set file annotation |
| `POST` | [`/projects/{id}/files/upload`](#post-projects-id-files-upload) | upload/replace a project file (only for non-default connection providers) |
| `GET` | [`/projects/{id}/team`](#get-projects-id-team) | Get a list of project team members |

## POST /projects

Create a project

API version 2.0, task `create` in [`projectsv2_0.php`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L235).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `title` | string | yes | — | Project title |
| `alias` | string | yes | — | Project alias |
| `about` | string | no | — | Blurb about the project. |
| `owned_by_user` | integer | no | 0 | User ID of entry owner. Defaults to entry creator if not specified. |
| `owned_by_group` | integer | no | 0 | Group ID of entry owner.Specifies if a project is owned by a group. |
| `state` | integer | no | 1 | Published state (0 = unpublished, 1 = published) |
| `private` | integer | no | 1 | Private (1) project or publicly disoverable (0)? |
| `sync_group` | integer | no | 0 | Sync group membership to projects? (only applies if owned_by_group is set. |
| `agree` | integer | yes | 0 | Agree to terms & conditions |
| `restricted` | string | no | — | Project contains restricted data? |
| `hipaa` | string | no | no | Project contains HIPAA data? |
| `ferpa` | string | no | no | Project contains FERPA data? |
| `agree_ferpa` | integer | no | 0 | Agree to terms & conditions for FERPA data. Required if 'ferpa'='yes'. |
| `irb` | string | no | no | Project contains IRB data? |
| `agree_irb` | integer | no | 0 | Agree to terms & conditions for IRB data. Required if 'irb'='yes'. |
| `export` | string | no | no | Project data can be exported? |
| `grant_title` | string | no | — | Grant title |
| `grant_agency` | string | no | — | Grant agency |
| `grant_PI` | string | no | — | Grant PI |
| `grant_budget` | string | no | — | Grant budget |

## GET /projects/list

Display projects user belongs to

API version 1.0, task `list` in [`projectsv1_0.php`](../../../core/components/com_projects/api/controllers/projectsv1_0.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 0 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | title | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |
| `verbose` | integer | no | 0 | Receive verbose output for project status, team member role and privacy. |

## GET /projects/list

Display projects user belongs to

API version 1.1, task `list` in [`projectsv1_1.php`](../../../core/components/com_projects/api/controllers/projectsv1_1.php#L28).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 0 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | title | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |
| `verbose` | integer | no | 0 | Receive verbose output for project status, team member role and privacy. |

## GET /projects/list

List projects

API version 2.0, task `list` in [`projectsv2_0.php`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L42).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 0 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | title | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |
| `verbose` | integer | no | 0 | Receive verbose output for project status, team member role and privacy. |

## DELETE /projects/{id}

Delete a project

API version 2.0, task `delete` in [`projectsv2_0.php`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L1178).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer\|string | yes | — | Project identifier (numeric ID or alias) |

## GET /projects/{id}

Get project info (if user is in project)

API version 1.0, task `get` in [`projectsv1_0.php`](../../../core/components/com_projects/api/controllers/projectsv1_0.php#L171).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |

## GET /projects/{id}

Get project info (if user is in project)

API version 1.1, task `get` in [`projectsv1_1.php`](../../../core/components/com_projects/api/controllers/projectsv1_1.php#L210).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |

## GET /projects/{id}

Get project info (if user is in project)

API version 2.0, task `read` in [`projectsv2_0.php`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L677).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer\|string | yes | — | Project identifier (numeric ID or alias) |

## PUT /projects/{id}

Update a project

API version 2.0, task `update` in [`projectsv2_0.php`](../../../core/components/com_projects/api/controllers/projectsv2_0.php#L741).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer\|string | yes | — | Project identifier (numeric ID or alias) |
| `title` | string | yes | — | Project title |
| `alias` | string | yes | — | Project alias |
| `about` | string | no | — | Blurb about the project. |
| `owned_by_user` | integer | no | 0 | User ID of entry owner. Defaults to entry creator if nto specified. |
| `owned_by_group` | integer | no | 0 | Group ID of entry owner.Specifies if a project is owned by a group. |
| `state` | integer | no | 1 | Published state (0 = unpublished, 1 = published) |
| `private` | integer | no | 1 | Private (1) project or publicly disoverable (0)? |
| `sync_group` | integer | no | 0 | Sync group membership to projects? (only applies if owned_by_group is set. |
| `restricted` | string | no | — | Project contains restricted data? |
| `hipaa` | string | no | no | Project contains HIPAA data? |
| `ferpa` | string | no | no | Project contains FERPA data? |
| `agree_ferpa` | integer | no | 0 | Agree to terms & conditions for FERPA data. Required if 'ferpa'='yes'. |
| `irb` | string | no | no | Project contains IRB data? |
| `agree_irb` | integer | no | 0 | Agree to terms & conditions for IRB data. Required if 'irb'='yes'. |
| `export` | string | no | no | Project data can be exported? |
| `grant_title` | string | no | — | Grant title |
| `grant_agency` | string | no | — | Grant agency |
| `grant_PI` | string | no | — | Grant PI |
| `grant_budget` | string | no | — | Grant budget |

## GET /projects/{id}/files

Get a list of project files

API version 1.0, task `list` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L83).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `filter` | string | no | — | A word or phrase to search for. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files

Get a list of project files

API version 1.0, task `list` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L101).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `filter` | string | no | — | A word or phrase to search for. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files

Get a list of project files

API version 1.0, task `list` in [`publicationsv1_0.php`](../../../core/components/com_projects/api/controllers/publicationsv1_0.php#L62).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `sortby` | string | no | title | Field to sort results by. |
| `sortdir` | string | no | asc | Direction to sort results by. |
| `published` | string | no | 0 | Get only published datasets (1) or all including drafts (0) |

## GET /projects/{id}/files/connections

Get a list of project files connections

API version 1.0, task `connections` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1497).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |

## GET,POST /projects/{id}/files/connections/{cid}/chunkedUpload

Uploads file chunk(s) and combines them before adding the final file to repository

API version 1.0, task `chunkedUpload` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L717).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `cid` | string | yes | — | Connection identifier (numeric ID) |
| `subdir` | string | yes | — | Directory path to upload to |
| `flowFilename` | string | yes | — | Name of file being uploaded |
| `flowIdentifier` | string | yes | — | Temporary file basename for chunk parts |
| `flowTotalChunks` | integer | yes | — | Total number of file chunks |
| `flowTotalSize` | integer | yes | — | Total file size |
| `flowChunkNumber` | integer | yes | — | Index of the chunk in the request |
| `flowChunkSize` | integer | yes | — | Size of ths chunk in the request |
| `flowChunkHash` | string | yes | — | MD5 hash of the chunk in the request |

## GET /projects/{id}/files/connections/{cid}/download

Download file or folder from project (non-default connection providers only)

API version 1.0, task `download` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L938).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `cid` | string | yes | — | Connection identifier (numeric ID) |
| `asset` | array | yes | — | Array of file paths. |
| `folder` | array | no | — | Array of folder paths. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/connections/{cid}/getmetadata

Get file annotation

API version 1.0, task `getmetadata` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1109).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `cid` | string | yes | — | Connection identifier (numeric ID or alias) |
| `asset` | array | yes | — | Array of files (do not include local path - use subdir param). |
| `fields` | array | no | — | Fields to get metadata for (if empty, return all fields). |
| `subdir` | string | no | — | Directory path within project repo. |

## GET /projects/{id}/files/connections/{cid}/setmetadata

Set file annotation

API version 1.0, task `setmetadata` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1204).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `cid` | string | yes | — | Connection identifier (numeric ID or alias) |
| `asset` | string | yes | — | Array of files (do not include local path - use subdir param). |
| `metadata` | array | no | — | Associative array of metadata to update. |
| `subdir` | string | no | — | Directory path within project repo. |

## POST /projects/{id}/files/connections/{cid}/upload

upload/replace a project file (only for non-default connection providers)

API version 1.0, task `upload` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L634).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `cid` | string | yes | — | Connection identifier (numeric ID) |
| `subdir` | string | no | — | Directory path within project filespace |
| `file` | binary | yes | — | File contents to upload |

## GET /projects/{id}/files/delete

Delete file or folder from project

API version 1.0, task `delete` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L263).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `asset` | array | yes | — | Array of file paths. |
| `folder` | array | no | — | Array of folder paths. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/delete

Delete file or folder from project

API version 1.0, task `delete` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L333).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `asset` | array | yes | — | Array of file paths. |
| `folder` | array | no | — | Array of folder paths. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/download

Download file or folder from project (non-default connection providers only)

API version 1.0, task `download` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L571).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `asset` | array | yes | — | Array of file paths. |
| `folder` | array | no | — | Array of folder paths. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/get

Get file(s) metadata

API version 1.0, task `get` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L152).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `asset` | array | yes | — | Array of file/folder paths to get metadata for. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/get

Get file(s) metadata

API version 1.0, task `get` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L189).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `asset` | array | yes | — | Array of file/folder paths to get metadata for. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/getmetadata

Get file annotation

API version 1.0, task `getmetadata` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L882).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `asset` | array | yes | — | Array of files (do not include local path - use subdir param). |
| `fields` | array | no | — | Fields to get metadata for (if empty, return all fields). |
| `subdir` | string | no | — | Directory path within project repo. |

## GET /projects/{id}/files/insert

Insert/update a project file

API version 1.0, task `save` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L791).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `data_path` | string | yes | 25 | Path to local or remote file. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/insert

Insert/update a project file

API version 1.0, task `save` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L1018).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `data_path` | string | yes | 25 | Path to local or remote file. |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/makedirectory

Create a folder in project local repo

API version 1.0, task `makedirectory` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L205).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `directory` | string | yes | — | Directory path |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/makedirectory

Create a folder in project local repo

API version 1.0, task `makedirectory` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L260).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `directory` | string | yes | — | Directory path |
| `subdir` | string | no | — | Directory path within project repo, if not already included in the asset file path. |

## GET /projects/{id}/files/move

Move file or folder in project

API version 1.0, task `move` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L348).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `target` | string | yes | — | Target directory path within project repo |
| `asset` | array | yes | — | Array of file paths to move. |
| `folder` | array | no | — | Array of folder paths to move. |

## GET /projects/{id}/files/move

Move file or folder in project

API version 1.0, task `move` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L437).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `target` | string | yes | — | Target directory path within project repo |
| `asset` | array | yes | — | Array of file paths to move. |
| `folder` | array | no | — | Array of folder paths to move. |

## GET /projects/{id}/files/rename

Move file or folder in project

API version 1.0, task `rename` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L437).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `type` | string | yes | file | File or folder. |
| `from` | string | yes | — | Name of file/folder to rename (do not include local path - use subdir param). |
| `to` | string | yes | — | New name for file/folder (do not include local path - use subdir param). |
| `subdir` | string | no | — | Directory path within project repo. |

## GET /projects/{id}/files/rename

Move file or folder in project

API version 1.0, task `rename` in [`filesv1_0.php`](../../../core/components/com_projects/api/controllers/filesv1_0.php#L544).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `type` | string | yes | file | File or folder. |
| `from` | string | yes | — | Name of file/folder to rename (do not include local path - use subdir param). |
| `to` | string | yes | — | New name for file/folder (do not include local path - use subdir param). |
| `subdir` | string | no | — | Directory path within project repo. |

## GET /projects/{id}/files/setmetadata

Set file annotation

API version 1.0, task `setmetadata` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L970).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `asset` | string | yes | — | Array of files (do not include local path - use subdir param). |
| `metadata` | array | no | — | Associative array of metadata to update. |
| `subdir` | string | no | — | Directory path within project repo. |

## POST /projects/{id}/files/upload

upload/replace a project file (only for non-default connection providers)

API version 1.0, task `upload` in [`filefsv1_0.php`](../../../core/components/com_projects/api/controllers/filefsv1_0.php#L509).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `subdir` | string | no | — | Directory path within project filespace |
| `file` | binary | yes | — | File contents to upload |

## GET /projects/{id}/team

Get a list of project team members

API version 1.0, task `list` in [`teamv1_0.php`](../../../core/components/com_projects/api/controllers/teamv1_0.php#L60).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | string | yes | — | Project identifier (numeric ID or alias) |
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `sortby` | string | no | title | Field to sort results by. |
| `sortdir` | string | no | desc | Direction to sort results by. |
