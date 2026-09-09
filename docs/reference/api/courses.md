<!--
status: generated
source: core/components/com_courses/api/controllers/
-->

# Courses API

Endpoints under `/api/courses`, from the `com_courses` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/courses/asset/delete`](#post-courses-asset-delete) | Deletes an asset |
| `POST` | [`/courses/asset/deletefile`](#post-courses-asset-deletefile) | Deletes an asset file |
| `POST` | [`/courses/asset/edit`](#post-courses-asset-edit) | Retrieves the asset edit page |
| `GET` | [`/courses/asset/getformanddepid`](#get-courses-asset-getformanddepid) | Looks up the form id and deployment id based on the asset id |
| `GET` | [`/courses/asset/getformid`](#get-courses-asset-getformid) | Looks up the form id based on the asset id |
| `POST` | [`/courses/asset/handlers`](#post-courses-asset-handlers) | Gets the asset handlers for a given extension |
| `POST` | [`/courses/asset/new`](#post-courses-asset-new) | Creates a new asset |
| `POST` | [`/courses/asset/preview`](#post-courses-asset-preview) | Previews an asset |
| `POST` | [`/courses/asset/reorder`](#post-courses-asset-reorder) | Reorders assets |
| `POST` | [`/courses/asset/save`](#post-courses-asset-save) | Saves an asset |
| `POST` | [`/courses/asset/togglepublished`](#post-courses-asset-togglepublished) | Toggles the published state of an asset |
| `POST` | [`/courses/assetgroup/reorder`](#post-courses-assetgroup-reorder) | Reorders asset groups |
| `POST` | [`/courses/assetgroup/save`](#post-courses-assetgroup-save) | Saves an asset group |
| `GET` | [`/courses/form/image`](#get-courses-form-image) | Gets form images |
| `POST` | [`/courses/list`](#post-courses-list) | Lists course catalog |
| `POST` | [`/courses/passport/badge`](#post-courses-passport-badge) | Passport badges. Placeholder for now. |
| `POST` | [`/courses/prerequisite/delete`](#post-courses-prerequisite-delete) | Deletes a prerequisite |
| `POST` | [`/courses/prerequisite/new`](#post-courses-prerequisite-new) | Adds a new prerequisite |
| `POST` | [`/courses/unit/save`](#post-courses-unit-save) | Saves a course unit |
| `POST` | [`/courses/unity/save`](#post-courses-unity-save) | Processes grade save from unity app |

## POST /courses/asset/delete

Deletes an asset

API version 1.0, task `delete` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L559).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `asset_id` | integer | yes | — | ID of asset to delete |
| `scope` | string | yes | — | Asset scope |
| `scope_id` | integer | yes | — | Asset scope ID |

## POST /courses/asset/deletefile

Deletes an asset file

API version 1.0, task `deletefile` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L667).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | ID of asset owning file |
| `filename` | string | yes | — | Name of file to delete |

## POST /courses/asset/edit

Retrieves the asset edit page

API version 1.0, task `edit` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L173).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | ID of asset to edit |

## GET /courses/asset/getformanddepid

Looks up the form id and deployment id based on the asset id

API version 1.0, task `getformanddepid` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L893).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | ID of asset to look up |

## GET /courses/asset/getformid

Looks up the form id based on the asset id

API version 1.0, task `getformid` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L841).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | ID of asset to look up |

## POST /courses/asset/handlers

Gets the asset handlers for a given extension

API version 1.0, task `handlers` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L32).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `name` | string | yes | — | Name of file to be uploaded |

## POST /courses/asset/new

Creates a new asset

API version 1.0, task `new` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L90).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `files` | array | no | — | Files to upload |
| `type` | string | no | — | Content type being created |
| `handler` | string | no | — | The file handler to use |

## POST /courses/asset/preview

Previews an asset

API version 1.0, task `preview` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L215).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | ID of asset to preview |

## POST /courses/asset/reorder

Reorders assets

API version 1.0, task `reorder` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L727).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `asset` | array | yes | — | Array of IDs of assets to reorder |
| `scope` | string | yes | — | Asset scope |
| `scope_id` | integer | yes | — | Asset scope ID |

## POST /courses/asset/save

Saves an asset

API version 1.0, task `save` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L257).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | no | — | ID of asset to save |
| `title` | string | no | New asset | Asset title |
| `published` | string | no | — | Asset state |
| `state` | integer | no | — | Asset state |

## POST /courses/asset/togglepublished

Toggles the published state of an asset

API version 1.0, task `togglepublished` in [`assetv1_0.php`](../../../core/components/com_courses/api/controllers/assetv1_0.php#L788).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | ID of asset to toggle state |

## POST /courses/assetgroup/reorder

Reorders asset groups

API version 1.0, task `reorder` in [`assetgroupv1_0.php`](../../../core/components/com_courses/api/controllers/assetgroupv1_0.php#L194).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `assetgroupitem` | array | yes | — | Asset group items, in desired order |

## POST /courses/assetgroup/save

Saves an asset group

API version 1.0, task `save` in [`assetgroupv1_0.php`](../../../core/components/com_courses/api/controllers/assetgroupv1_0.php#L33).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | no | — | Asset group ID to edit |
| `title` | string | no | New asset group | Asset group title |
| `state` | integer | no | — | State of asset group |
| `description` | string | no | — | Short description |
| `unit_id` | integer | no | — | ID of parent unit |
| `parent` | integer | no | — | ID of parent asset group |
| `params` | array | no | — | Parameters related to the asset group |

## GET /courses/form/image

Gets form images

API version 1.0, task `image` in [`formv1_0.php`](../../../core/components/com_courses/api/controllers/formv1_0.php#L22).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Form ID |
| `form_version` | integer | no | — | Form version number |
| `file` | string | yes | — | Image filename |
| `token` | string | yes | — | Session authentication token |

## POST /courses/list

Lists course catalog

API version 1.0, task `list` in [`coursesv1_0.php`](../../../core/components/com_courses/api/controllers/coursesv1_0.php#L31).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of records to return |
| `limitstart` | integer | no | 0 | Offset of Records to return |

## POST /courses/passport/badge

Passport badges. Placeholder for now.

API version 1.0, task `badge` in [`passportv1_0.php`](../../../core/components/com_courses/api/controllers/passportv1_0.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `action` | string | yes | — | Badge action |
| `badge_id` | integer | yes | — | Passport badge ID |
| `user_email` | string | yes | — | Email address to which the badge was asserted |

## POST /courses/prerequisite/delete

Deletes a prerequisite

API version 1.0, task `delete` in [`prerequisitev1_0.php`](../../../core/components/com_courses/api/controllers/prerequisitev1_0.php#L84).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Prerequisite ID |

## POST /courses/prerequisite/new

Adds a new prerequisite

API version 1.0, task `new` in [`prerequisitev1_0.php`](../../../core/components/com_courses/api/controllers/prerequisitev1_0.php#L22).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `item_scope` | string | no | asset | Items having prerequisites |
| `item_id` | integer | yes | — | Item ID |
| `requisite_scope` | string | no | asset | Items that are prerequisites |
| `requisite_id` | integer | yes | — | Requisite ID |
| `section_id` | integer | yes | — | Section ID |

## POST /courses/unit/save

Saves a course unit

API version 1.0, task `save` in [`unitv1_0.php`](../../../core/components/com_courses/api/controllers/unitv1_0.php#L28).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | no | — | Unit ID to edit |
| `section_id` | integer | no | — | Section ID of unit |
| `offering_id` | integer | no | — | Offering ID of unit |
| `title` | string | no | New Unit | Unit title |
| `publish_up` | string | no | — | Start publishing date |
| `publish_down` | string | no | — | Stop publishing date |

## POST /courses/unity/save

Processes grade save from unity app

API version 1.0, task `save` in [`unityv1_0.php`](../../../core/components/com_courses/api/controllers/unityv1_0.php#L26).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `referrer` | string | no | $_SERVER['HTTP_REFERER'] | Host page |
| `payload` | string | yes | — | Score notes/content |
