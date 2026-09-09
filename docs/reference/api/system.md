<!--
status: generated
source: core/components/com_system/api/controllers/
-->

# System API

Endpoints under `/api/system`, from the `com_system` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/system/getSessionLifetime`](#get-system-getsessionlifetime) | Grabs the session lifetime, in minutes |
| `GET` | [`/system/info`](#get-system-info) | Display system information |
| `POST` | [`/system/media/tracking`](#post-system-media-tracking) | Records media tracking info |

## GET /system/getSessionLifetime

Grabs the session lifetime, in minutes

API version 1.0, task `getSessionLifetime` in [`systemv1_0.php`](../../../core/components/com_system/api/controllers/systemv1_0.php#L170).

## GET /system/info

Display system information

API version 1.0, task `info` in [`systemv1_0.php`](../../../core/components/com_system/api/controllers/systemv1_0.php#L26).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `values` | string | no | all | Amount of data to return |

## POST /system/media/tracking

Records media tracking info

API version 1.0, task `tracking` in [`mediav1_0.php`](../../../core/components/com_system/api/controllers/mediav1_0.php#L26).
