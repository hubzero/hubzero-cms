<!--
status: generated
source: core/components/com_answers/api/controllers/
-->

# Answers API

Endpoints under `/api/answers`, from the `com_answers` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/answers/questions`](#post-answers-questions) | Create a new question |
| `GET` | [`/answers/questions/list`](#get-answers-questions-list) | Display a list of questions |
| `DELETE` | [`/answers/questions/{id}`](#delete-answers-questions-id) | Delete a question |
| `GET` | [`/answers/questions/{id}`](#get-answers-questions-id) | Retrieve a question |
| `PUT` | [`/answers/questions/{id}`](#put-answers-questions-id) | Update a question |

## POST /answers/questions

Create a new question

API version 1.0, task `create` in [`questionsv1_0.php`](../../../core/components/com_answers/api/controllers/questionsv1_0.php#L133).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `email` | integer | no | 0 | Notify user of responses |
| `anonymous` | integer | no | 0 | List author as anonymous or not |
| `subject` | string | yes | — | Short, one-line question |
| `question` | string | no | — | Longer, detailed question |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `crated_by` | integer | no | 0 | User ID of entry creator |
| `state` | integer | no | 0 | Published state (0 = unpublished, 1 = published) |
| `reward` | integer | no | 0 | Reward points |
| `tags` | string | yes | — | Comma-separated list of tags |

## GET /answers/questions/list

Display a list of questions

API version 1.0, task `list` in [`questionsv1_0.php`](../../../core/components/com_answers/api/controllers/questionsv1_0.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## DELETE /answers/questions/{id}

Delete a question

API version 1.0, task `delete` in [`questionsv1_0.php`](../../../core/components/com_answers/api/controllers/questionsv1_0.php#L403).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Question identifier |

## GET /answers/questions/{id}

Retrieve a question

API version 1.0, task `read` in [`questionsv1_0.php`](../../../core/components/com_answers/api/controllers/questionsv1_0.php#L246).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Question identifier |

## PUT /answers/questions/{id}

Update a question

API version 1.0, task `update` in [`questionsv1_0.php`](../../../core/components/com_answers/api/controllers/questionsv1_0.php#L276).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Question identifier |
| `email` | integer | no | — | Notify user of responses |
| `anonymous` | integer | no | — | List author as anonymous or not |
| `subject` | string | no | — | Short, one-line question |
| `question` | string | no | — | Longer, detailed question |
| `created` | string | no | — | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `crated_by` | integer | no | — | User ID of entry creator |
| `state` | integer | no | — | Published state (0 = unpublished, 1 = published) |
| `reward` | integer | no | — | Reward points |
| `tags` | string | no | — | Comma-separated list of tags |
