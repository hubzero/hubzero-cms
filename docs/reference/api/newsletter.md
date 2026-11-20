<!--
status: generated
source: core/components/com_newsletter/api/controllers/
-->

# Newsletter API

Endpoints under `/api/newsletter`, from the `com_newsletter` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/newsletter/archive`](#get-newsletter-archive) | Return data for past newsletters |
| `GET` | [`/newsletter/current`](#get-newsletter-current) | Return data for the current newsletter |
| `GET` | [`/newsletter/list`](#get-newsletter-list) | Return data for newsletters |

## GET /newsletter/archive

Return data for past newsletters

API version 1.0, task `archive` in [`newslettersv1r0.php`](../../../core/components/com_newsletter/api/controllers/newslettersv1r0.php#L84).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 5 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |

## GET /newsletter/current

Return data for the current newsletter

API version 1.0, task `current` in [`newslettersv1r0.php`](../../../core/components/com_newsletter/api/controllers/newslettersv1r0.php#L21).

## GET /newsletter/list

Return data for newsletters

API version 1.0, task `list` in [`newslettersv1r0.php`](../../../core/components/com_newsletter/api/controllers/newslettersv1r0.php#L45).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 5 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
