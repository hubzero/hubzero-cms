<!--
status: generated
source: core/components/com_members/api/controllers/
-->

# Members API

Endpoints under `/api/members`, from the `com_members` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/members` (v1.0)](#post-members-v1-0) | Create a user profile |
| `POST` | [`/members` (v1.1)](#post-members-v1-1) | Create a user profile |
| `GET` | [`/members/checkpass` (v1.0)](#get-members-checkpass-v1-0) | Check password |
| `GET` | [`/members/checkpass` (v1.1)](#get-members-checkpass-v1-1) | Check password |
| `GET` | [`/members/fieldValues` (v1.0)](#get-members-fieldvalues-v1-0) | Retrieves option values for a profile field |
| `GET` | [`/members/fieldValues` (v1.1)](#get-members-fieldvalues-v1-1) | Retrieves option values for a profile field |
| `GET` | [`/members/list` (v1.0)](#get-members-list-v1-0) | Display a list of members |
| `GET` | [`/members/list` (v1.1)](#get-members-list-v1-1) | Display a list of members |
| `GET` | [`/members/organizations` (v1.0)](#get-members-organizations-v1-0) | Get a list of oranizations used throughout member profiles |
| `GET` | [`/members/organizations` (v1.1)](#get-members-organizations-v1-1) | Get a list of oranizations used throughout member profiles |
| `GET` | [`/members/{id}` (v1.0)](#get-members-id-v1-0) | Get user profile info |
| `GET` | [`/members/{id}` (v1.1)](#get-members-id-v1-1) | Get user profile info |
| `GET` | [`/members/{id}/accessgroups`](#get-members-id-accessgroups) | Get a member's accessgroups |
| `GET` | [`/members/{id}/groups` (v1.0)](#get-members-id-groups-v1-0) | Get a member's groups |
| `GET` | [`/members/{id}/groups` (v1.1)](#get-members-id-groups-v1-1) | Get a member's groups |
| `GET` | [`/members/{id}/tools/diskusage`](#get-members-id-tools-diskusage) | Get a resource based on tool name |
| `GET` | [`/members/{id}/tools/recent`](#get-members-id-tools-recent) | Get recent tools for a user |
| `GET` | [`/members/{id}/tools/sessions`](#get-members-id-tools-sessions) | Get a member's tool sessions |

## POST /members (v1.0)

Create a user profile

API version 1.0, task `create` in [`profilesv1r0.php`](../../../core/components/com_members/api/controllers/profilesv1r0.php#L157).

## POST /members (v1.1)

Create a user profile

API version 1.1, task `create` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L199).

## GET /members/checkpass (v1.0)

Check password

API version 1.0, task `checkpass` in [`profilesv1r0.php`](../../../core/components/com_members/api/controllers/profilesv1r0.php#L444).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `password1` | string | yes | — | Password to validate |

## GET /members/checkpass (v1.1)

Check password

API version 1.1, task `checkpass` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L561).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `password1` | string | yes | — | Password to validate |

## GET /members/fieldValues (v1.0)

Retrieves option values for a profile field

API version 1.0, task `fieldValues` in [`profilesv1r0.php`](../../../core/components/com_members/api/controllers/profilesv1r0.php#L577).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `field` | string | yes | — | Profile field of interest |

## GET /members/fieldValues (v1.1)

Retrieves option values for a profile field

API version 1.1, task `fieldValues` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L692).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `field` | string | yes | — | Profile field of interest |

## GET /members/list (v1.0)

Display a list of members

API version 1.0, task `list` in [`profilesv1r0.php`](../../../core/components/com_members/api/controllers/profilesv1r0.php#L30).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | name | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /members/list (v1.1)

Display a list of members

API version 1.1, task `list` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L30).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | name | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /members/organizations (v1.0)

Get a list of oranizations used throughout member profiles

API version 1.0, task `organizations` in [`profilesv1r0.php`](../../../core/components/com_members/api/controllers/profilesv1r0.php#L528).

## GET /members/organizations (v1.1)

Get a list of oranizations used throughout member profiles

API version 1.1, task `organizations` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L643).

## GET /members/{id} (v1.0)

Get user profile info

API version 1.0, task `read` in [`profilesv1r0.php`](../../../core/components/com_members/api/controllers/profilesv1r0.php#L285).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |

## GET /members/{id} (v1.1)

Get user profile info

API version 1.1, task `read` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L326).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |

## GET /members/{id}/accessgroups

Get a member's accessgroups

API version 1.1, task `accessgroups` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L450).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |
| `inherited` | boolean | no | no | include inherited user accessgroups |
| `titles` | boolean | no | no | return accessgroup titles |
| `all` | boolean | no | no | return accessgroup titles and ids as an associative array |

## GET /members/{id}/groups (v1.0)

Get a member's groups

API version 1.0, task `groups` in [`profilesv1r0.php`](../../../core/components/com_members/api/controllers/profilesv1r0.php#L397).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |

## GET /members/{id}/groups (v1.1)

Get a member's groups

API version 1.1, task `groups` in [`profilesv1r1.php`](../../../core/components/com_members/api/controllers/profilesv1r1.php#L517).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |

## GET /members/{id}/tools/diskusage

Get a resource based on tool name

API version 1.0, task `diskusage` in [`toolsv1r0.php`](../../../core/components/com_members/api/controllers/toolsv1r0.php#L260).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |

## GET /members/{id}/tools/recent

Get recent tools for a user

API version 1.0, task `recenttools` in [`toolsv1r0.php`](../../../core/components/com_members/api/controllers/toolsv1r0.php#L182).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |

## GET /members/{id}/tools/sessions

Get a member's tool sessions

API version 1.0, task `sessions` in [`toolsv1r0.php`](../../../core/components/com_members/api/controllers/toolsv1r0.php#L28).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Member identifier |
