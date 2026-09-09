<!--
status: generated
source: core/components/com_jobs/api/controllers/
-->

# Jobs API

Endpoints under `/api/jobs`, from the `com_jobs` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | [`/jobs/list`](#get-jobs-list) | Display a list of jobs |
| `GET` | [`/jobs/{jobcode}`](#get-jobs-jobcode) | Display a job |

## GET /jobs/list

Display a list of jobs

API version 1.0, task `list` in [`jobsv1_0.php`](../../../core/components/com_jobs/api/controllers/jobsv1_0.php#L27).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | name | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /jobs/{jobcode}

Display a job

API version 1.0, task `job` in [`jobsv1_0.php`](../../../core/components/com_jobs/api/controllers/jobsv1_0.php#L95).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `jobcode` | integer | yes | — | The job code associated with the opening |
