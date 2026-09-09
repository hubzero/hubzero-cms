<!--
status: generated
source: core/components/com_kb/api/controllers/
-->

# Kb API

Endpoints under `/api/kb`, from the `com_kb` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/kb/list`](#get-kb-list) | Get a list of Knowledge Base Articles |

## GET /kb/list

Get a list of Knowledge Base Articles

API version 1.0, task `list` in [`entriesv1_0.php`](../../../core/components/com_kb/api/controllers/entriesv1_0.php#L24).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
