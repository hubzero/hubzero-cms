<!--
status: generated
source: core/components/com_answers/config/config.xml
-->

# Answers (com_answers)

Manage Questions and Answers

Parameters from [`core/components/com_answers/config/config.xml`](../../../../core/components/com_answers/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `infolink` | About points | text | `/kb/points` | Link to page explaining the point system |
| `notify_users` | Users notified on every activity | textarea | — | A comma-separated list of usernames to be notified whenever a new question/answer is posted. |
| `restrict_users` | Restrict accounts to create new posts | list | — | Restrict accounts to create new posts. Options: `` Disabled, `active` Enabled. |
| `restrict_days` | Minimum number of days to post | int | `0` | Minimum number of days to post |
