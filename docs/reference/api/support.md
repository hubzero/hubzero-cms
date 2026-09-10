<!--
status: generated
source: core/components/com_support/api/controllers/
-->

# Support API

Endpoints under `/api/support`, from the `com_support` API controllers. Authenticate with an OAuth bearer token or a session cookie; see the developers book for the API basics.

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | [`/support`](#post-support) | Create a new ticket |
| `POST` | [`/support/categories`](#post-support-categories) | Create a new support category |
| `GET` | [`/support/categories/list`](#get-support-categories-list) | Display ticket categories |
| `DELETE` | [`/support/categories/{id}`](#delete-support-categories-id) | Delete a support category |
| `GET` | [`/support/categories/{id}`](#get-support-categories-id) | Displays details for a support category |
| `PUT` | [`/support/categories/{id}`](#put-support-categories-id) | Update a support category |
| `GET` | [`/support/comments`](#get-support-comments) | Display comments for a ticket |
| `POST` | [`/support/comments` (v2.0)](#post-support-comments-v2-0) | Create a new comment |
| `POST` | [`/support/comments` (v2.1)](#post-support-comments-v2-1) | Create a new comment |
| `GET` | [`/support/comments/list`](#get-support-comments-list) | Display ticket comments |
| `GET` | [`/support/comments/{id}` (v2.0)](#get-support-comments-id-v2-0) | Displays details for a ticket comment |
| `GET` | [`/support/comments/{id}` (v2.1)](#get-support-comments-id-v2-1) | Displays details for a ticket comment |
| `PUT` | [`/support/comments/{id}` (v2.0)](#put-support-comments-id-v2-0) | Update a ticket comment |
| `PUT` | [`/support/comments/{id}` (v2.1)](#put-support-comments-id-v2-1) | Update a ticket comment |
| `GET` | [`/support/list`](#get-support-list) | Display a list of tickets |
| `POST` | [`/support/messages`](#post-support-messages) | Create a new support message |
| `GET` | [`/support/messages/list`](#get-support-messages-list) | Display ticket messages |
| `DELETE` | [`/support/messages/{id}`](#delete-support-messages-id) | Delete a support message |
| `GET` | [`/support/messages/{id}`](#get-support-messages-id) | Displays details for a support message |
| `PUT` | [`/support/messages/{id}`](#put-support-messages-id) | Update a support message |
| `GET` | [`/support/outstandingtickets`](#get-support-outstandingtickets) | Display a list of tickets that violate criteria |
| `GET` | [`/support/stats`](#get-support-stats) | Displays ticket stats |
| `POST` | [`/support/statuses`](#post-support-statuses) | Create a new support status |
| `GET` | [`/support/statuses/list`](#get-support-statuses-list) | Display ticket statuses |
| `DELETE` | [`/support/statuses/{id}`](#delete-support-statuses-id) | Delete a support status |
| `GET` | [`/support/statuses/{id}`](#get-support-statuses-id) | Displays details for a support status |
| `PUT` | [`/support/statuses/{id}`](#put-support-statuses-id) | Update a support status |
| `GET` | [`/support/tickets`](#get-support-tickets) | Display a list of tickets |
| `POST` | [`/support/tickets` (v2.0)](#post-support-tickets-v2-0) | Create a new ticket |
| `POST` | [`/support/tickets` (v2.1)](#post-support-tickets-v2-1) | Create a new ticket |
| `GET` | [`/support/tickets/list`](#get-support-tickets-list) | Display a list of tickets |
| `DELETE` | [`/support/tickets/{ticket}` (v2.0)](#delete-support-tickets-ticket-v2-0) | Delete a ticket |
| `DELETE` | [`/support/tickets/{ticket}` (v2.1)](#delete-support-tickets-ticket-v2-1) | Delete a ticket |
| `GET` | [`/support/tickets/{ticket}` (v2.0)](#get-support-tickets-ticket-v2-0) | Displays details for a ticket |
| `GET` | [`/support/tickets/{ticket}` (v2.1)](#get-support-tickets-ticket-v2-1) | Displays details for a ticket |
| `PUT` | [`/support/tickets/{ticket}` (v2.0)](#put-support-tickets-ticket-v2-0) | Update a ticket |
| `PUT` | [`/support/tickets/{ticket}` (v2.1)](#put-support-tickets-ticket-v2-1) | Update a ticket |
| `DELETE` | [`/support/{ticket}`](#delete-support-ticket) | Delete a ticket |
| `GET` | [`/support/{ticket}`](#get-support-ticket) | Displays details for a ticket |
| `PUT` | [`/support/{ticket}`](#put-support-ticket) | Update a ticket |
| `POST` | [`/support/{ticket}/comments`](#post-support-ticket-comments) | Create a new comment |
| `GET` | [`/support/{ticket}/comments/list`](#get-support-ticket-comments-list) | Display comments for a ticket |
| `DELETE` | [`/support/{ticket}/comments/{comment}` (v1.0)](#delete-support-ticket-comments-comment-v1-0) | Delete a ticket comment |
| `DELETE` | [`/support/{ticket}/comments/{comment}` (v2.0)](#delete-support-ticket-comments-comment-v2-0) | Delete a ticket comment |
| `DELETE` | [`/support/{ticket}/comments/{comment}` (v2.1)](#delete-support-ticket-comments-comment-v2-1) | Delete a ticket comment |
| `GET` | [`/support/{ticket}/comments/{comment}`](#get-support-ticket-comments-comment) | Displays details for a ticket comment |
| `PUT` | [`/support/{ticket}/comments/{comment}`](#put-support-ticket-comments-comment) | Update a ticket comment |

## POST /support

Create a new ticket

API version 1.0, task `create` in [`ticketsv1_0.php`](../../../core/components/com_support/api/controllers/ticketsv1_0.php#L379).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | yes | — | Scope type (group, member, etc.) |
| `scope_id` | integer | yes | — | Scope object ID |
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |

## POST /support/categories

Create a new support category

API version 2.1, task `create` in [`categoriesv2_1.php`](../../../core/components/com_support/api/controllers/categoriesv2_1.php#L266).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |

## GET /support/categories/list

Display ticket categories

API version 2.1, task `list` in [`categoriesv2_1.php`](../../../core/components/com_support/api/controllers/categoriesv2_1.php#L45).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `created_by` | integer | no | 0 | List categories created by a specific user (by id) |
| `modified_by` | integer | no | 0 | List categories modified by a specific user (by id) |
| `search` | string | no | — | A word or phrase to search for in the category title. |
| `created` | string\|integer | no | — | A timestamp (YYYY-MM-DD HH:mm:ss) for items created on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31 |
| `modified` | string\|integer | no | — | A timestamp (YYYY-MM-DD HH:mm:ss) for items modified on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31 |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | id | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |

## DELETE /support/categories/{id}

Delete a support category

API version 2.1, task `delete` in [`categoriesv2_1.php`](../../../core/components/com_support/api/controllers/categoriesv2_1.php#L470).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |

## GET /support/categories/{id}

Displays details for a support category

API version 2.1, task `read` in [`categoriesv2_1.php`](../../../core/components/com_support/api/controllers/categoriesv2_1.php#L336).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Entry identifier |

## PUT /support/categories/{id}

Update a support category

API version 2.1, task `update` in [`categoriesv2_1.php`](../../../core/components/com_support/api/controllers/categoriesv2_1.php#L380).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `created` | string | no | now | Created timestamp (YYYY-MM-DD HH:mm:ss) |
| `created_by` | integer | no | 0 | User ID of entry creator |

## GET /support/comments

Display comments for a ticket

API version 2.0, task `list` in [`commentsv2_0.php`](../../../core/components/com_support/api/controllers/commentsv2_0.php#L46).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | no | — | List comments from a specific ticket (by id) |
| `created_by` | integer | no | — | List comments from a specific user (by id) |

## POST /support/comments (v2.0)

Create a new comment

API version 2.0, task `create` in [`commentsv2_0.php`](../../../core/components/com_support/api/controllers/commentsv2_0.php#L106).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | — | Id of the ticket to make a comment on |
| `comment` | string | yes | — | Comment text |
| `group` | string | no | — | Group to assign the ticket to (by alias) |
| `owner` | integer | no | — | Id of the owner to assign ticket to |
| `severity` | string | no | — | Severity of the ticket |
| `status` | integer | no | — | Status of the ticket |
| `target_date` | string | no | — | Target date for completion of ticket (YYYY-MM-DD hh:mm:ss) |
| `cc` | string | no | submitter,owner | Comma separated list of email addresses to email updates to |
| `private` | boolean | no | no | Should the comment be flagged as private |
| `email_submitter` | boolean | no | no | Should the submitter be emailed about this comment |
| `email_owner` | boolean | no | no | Should the ticket owner be emailed about this comment |

## POST /support/comments (v2.1)

Create a new comment

API version 2.1, task `create` in [`commentsv2_1.php`](../../../core/components/com_support/api/controllers/commentsv2_1.php#L259).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | — | Id of the ticket to make a comment on |
| `comment` | string | yes | — | Comment text |
| `group` | string | no | — | Group to assign the ticket to (by alias) |
| `owner` | integer | no | — | Id of the owner to assign ticket to |
| `severity` | string | no | — | Severity of the ticket |
| `status` | integer | no | — | Status of the ticket |
| `target_date` | string | no | — | Target date for completion of ticket (YYYY-MM-DD hh:mm:ss) |
| `cc` | string | no | submitter, owner | Comma separated list of email addresses to email updates to |
| `private` | boolean | no | no | Should the comment be flagged as private |
| `email_submitter` | boolean | no | no | Should the submitter be emailed about this comment |
| `email_owner` | boolean | no | no | Should the ticket owner be emailed about this comment |

## GET /support/comments/list

Display ticket comments

API version 2.1, task `list` in [`commentsv2_1.php`](../../../core/components/com_support/api/controllers/commentsv2_1.php#L49).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | no | 0 | List comments from a specific ticket (by id) |
| `created_by` | integer | no | 0 | List comments from a specific user (by id) |
| `access` | integer | no | 0 | Show only private (1) or non-private (0) comments |
| `search` | string | no | — | A word or phrase to search for. |
| `created` | string\|integer | no | — | A timestamp (YYYY-MM-DD HH:mm:ss) for items created on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31 |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## GET /support/comments/{id} (v2.0)

Displays details for a ticket comment

API version 2.0, task `read` in [`commentsv2_0.php`](../../../core/components/com_support/api/controllers/commentsv2_0.php#L442).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Comment identifier |

## GET /support/comments/{id} (v2.1)

Displays details for a ticket comment

API version 2.1, task `read` in [`commentsv2_1.php`](../../../core/components/com_support/api/controllers/commentsv2_1.php#L605).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Comment identifier |

## PUT /support/comments/{id} (v2.0)

Update a ticket comment

API version 2.0, task `update` in [`commentsv2_0.php`](../../../core/components/com_support/api/controllers/commentsv2_0.php#L490).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `comment` | integer | yes | 0 | Comment identifier |

## PUT /support/comments/{id} (v2.1)

Update a ticket comment

API version 2.1, task `update` in [`commentsv2_1.php`](../../../core/components/com_support/api/controllers/commentsv2_1.php#L651).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `comment` | integer | yes | 0 | Comment identifier |

## GET /support/list

Display a list of tickets

API version 1.0, task `list` in [`ticketsv1_0.php`](../../../core/components/com_support/api/controllers/ticketsv1_0.php#L220).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## POST /support/messages

Create a new support message

API version 2.1, task `create` in [`messagesv2_1.php`](../../../core/components/com_support/api/controllers/messagesv2_1.php#L135).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `title` | string | yes | — | Entry title |
| `message` | string | yes | — | Body of the message |

## GET /support/messages/list

Display ticket messages

API version 2.1, task `list` in [`messagesv2_1.php`](../../../core/components/com_support/api/controllers/messagesv2_1.php#L45).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `search` | string | no | — | A word or phrase to search for in the title. |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | id | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |

## DELETE /support/messages/{id}

Delete a support message

API version 2.1, task `delete` in [`messagesv2_1.php`](../../../core/components/com_support/api/controllers/messagesv2_1.php#L283).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |

## GET /support/messages/{id}

Displays details for a support message

API version 2.1, task `read` in [`messagesv2_1.php`](../../../core/components/com_support/api/controllers/messagesv2_1.php#L185).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Entry identifier |

## PUT /support/messages/{id}

Update a support message

API version 2.1, task `update` in [`messagesv2_1.php`](../../../core/components/com_support/api/controllers/messagesv2_1.php#L219).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |
| `title` | string | no | — | Entry title |
| `message` | string | no | — | Body of the message |

## GET /support/outstandingtickets

Display a list of tickets that violate criteria

API version 2.0, task `list` in [`outstandingticketsv2_0.php`](../../../core/components/com_support/api/controllers/outstandingticketsv2_0.php#L35).

## GET /support/stats

Displays ticket stats

API version 1.0, task `stats` in [`ticketsv1_0.php`](../../../core/components/com_support/api/controllers/ticketsv1_0.php#L45).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `type` | string | no | submitted | Ticket type |
| `group` | string | no | — | Group CN |

## POST /support/statuses

Create a new support status

API version 2.1, task `create` in [`statusesv2_1.php`](../../../core/components/com_support/api/controllers/statusesv2_1.php#L155).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `color` | string | no | — | Hexcadecimal color code (ex: #AA3300) |
| `open` | integer | no | 0 | The associated Open (1) or Closed (0) state |

## GET /support/statuses/list

Display ticket statuses

API version 2.1, task `list` in [`statusesv2_1.php`](../../../core/components/com_support/api/controllers/statusesv2_1.php#L45).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `open` | integer | no | — | List statuses by open (1) or closed (0) state |
| `modified_by` | integer | no | 0 | List categories modified by a specific user (by id) |
| `search` | string | no | — | A word or phrase to search for in the title. |
| `limit` | integer | no | 25 | Number of result to return. |
| `start` | integer | no | 0 | Number of where to start returning results. |
| `sort` | string | no | id | Field to sort results by. |
| `sort_Dir` | string | no | asc | Direction to sort results by. |

## DELETE /support/statuses/{id}

Delete a support status

API version 2.1, task `delete` in [`statusesv2_1.php`](../../../core/components/com_support/api/controllers/statusesv2_1.php#L335).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |

## GET /support/statuses/{id}

Displays details for a support status

API version 2.1, task `read` in [`statusesv2_1.php`](../../../core/components/com_support/api/controllers/statusesv2_1.php#L221).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | 0 | Entry identifier |

## PUT /support/statuses/{id}

Update a support status

API version 2.1, task `update` in [`statusesv2_1.php`](../../../core/components/com_support/api/controllers/statusesv2_1.php#L255).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `id` | integer | yes | — | Entry identifier |
| `title` | string | no | — | Entry title |
| `alias` | string | no | — | Entry alias |
| `color` | string | no | — | Hexcadecimal color code (ex: #AA3300) |
| `open` | integer | no | 0 | The associated Open (1) or Closed (0) state |

## GET /support/tickets

Display a list of tickets

API version 2.0, task `list` in [`ticketsv2_0.php`](../../../core/components/com_support/api/controllers/ticketsv2_0.php#L49).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `owner` | integer | no | — | List tickets with a specific owner (userid) |
| `status` | integer | no | — | List tickets with a specific status id |
| `severity` | string | no | — | List tickets with a specific severity |
| `group` | string | no | — | List tickets with a specific group (by alias) |

## POST /support/tickets (v2.0)

Create a new ticket

API version 2.0, task `create` in [`ticketsv2_0.php`](../../../core/components/com_support/api/controllers/ticketsv2_0.php#L145).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `username` | string | no | — | The submitter's username |
| `name` | string | yes | — | The submitter's name |
| `email` | string | yes | — | The submitter's email address |
| `os` | string | no | Unknown | The submitter's operating system |
| `browser` | string | no | Unknown | The submitter's browser type |
| `report` | string | yes | — | Description of the user's problem |
| `status` | integer | no | 0 | The status code of the ticket |
| `severity` | string | no | normal | The severity of the issue |
| `owner` | integer | no | 0 | The id of the user to assign this ticket to |
| `group` | string | no | — | Alias of the group to assign the ticket to |
| `files` | binary | no | — | ***STUB*** NOT WORKING |

## POST /support/tickets (v2.1)

Create a new ticket

API version 2.1, task `create` in [`ticketsv2_1.php`](../../../core/components/com_support/api/controllers/ticketsv2_1.php#L350).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `username` | string | no | — | The submitter's username |
| `name` | string | yes | — | The submitter's name |
| `email` | string | yes | — | The submitter's email address |
| `os` | string | no | Unknown | The submitter's operating system |
| `browser` | string | no | Unknown | The submitter's browser type |
| `report` | string | yes | — | Description of the user's problem |
| `status` | integer | no | 0 | The status code of the ticket |
| `severity` | string | no | normal | The severity of the issue |
| `owner` | integer | no | 0 | The id of the user to assign this ticket to |
| `group` | string | no | — | Alias of the group to assign the ticket to |
| `files` | binary | no | — | ***STUB*** NOT WORKING |

## GET /support/tickets/list

Display a list of tickets

API version 2.1, task `list` in [`ticketsv2_1.php`](../../../core/components/com_support/api/controllers/ticketsv2_1.php#L64).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |
| `type` | integer | no | 0 | Ticket type (0 = user submitted, 1 = automatic submission by tool) |
| `owner` | integer | no | — | List tickets with a specific owner (userid) |
| `status` | integer | no | — | List tickets with a specific status id |
| `open` | integer | no | — | Specify open/closed state |
| `category` | integer | no | — | Category ID the ticket is assigned to |
| `severity` | string | no | — | List tickets with a specific severity |
| `group` | string\|integer | no | — | List tickets with a specific group (by alias or group ID) |
| `created` | string\|integer | no | — | A timestamp (YYYY-MM-DD HH:mm:ss) for items created on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31 |
| `closed` | string\|integer | no | — | A timestamp (YYYY-MM-DD HH:mm:ss) for items closed on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31 |

## DELETE /support/tickets/{ticket} (v2.0)

Delete a ticket

API version 2.0, task `delete` in [`ticketsv2_0.php`](../../../core/components/com_support/api/controllers/ticketsv2_0.php#L511).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |

## DELETE /support/tickets/{ticket} (v2.1)

Delete a ticket

API version 2.1, task `delete` in [`ticketsv2_1.php`](../../../core/components/com_support/api/controllers/ticketsv2_1.php#L731).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |

## GET /support/tickets/{ticket} (v2.0)

Displays details for a ticket

API version 2.0, task `read` in [`ticketsv2_0.php`](../../../core/components/com_support/api/controllers/ticketsv2_0.php#L335).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |

## GET /support/tickets/{ticket} (v2.1)

Displays details for a ticket

API version 2.1, task `read` in [`ticketsv2_1.php`](../../../core/components/com_support/api/controllers/ticketsv2_1.php#L545).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |

## PUT /support/tickets/{ticket} (v2.0)

Update a ticket

API version 2.0, task `update` in [`ticketsv2_0.php`](../../../core/components/com_support/api/controllers/ticketsv2_0.php#L386).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |
| `owner` | integer | no | — | Ticket owner |
| `status` | integer | no | — | Ticket status |
| `severity` | string | no | — | Ticket severity |
| `group` | string | no | — | Alias of group ticket should be assigned to |

## PUT /support/tickets/{ticket} (v2.1)

Update a ticket

API version 2.1, task `update` in [`ticketsv2_1.php`](../../../core/components/com_support/api/controllers/ticketsv2_1.php#L596).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |
| `owner` | integer | no | — | Ticket owner |
| `status` | integer | no | — | Ticket status |
| `severity` | string | no | — | Ticket severity |
| `group` | string | no | — | Alias of group ticket should be assigned to |

## DELETE /support/{ticket}

Delete a ticket

API version 1.0, task `delete` in [`ticketsv1_0.php`](../../../core/components/com_support/api/controllers/ticketsv1_0.php#L596).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |

## GET /support/{ticket}

Displays details for a ticket

API version 1.0, task `read` in [`ticketsv1_0.php`](../../../core/components/com_support/api/controllers/ticketsv1_0.php#L484).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |

## PUT /support/{ticket}

Update a ticket

API version 1.0, task `update` in [`ticketsv1_0.php`](../../../core/components/com_support/api/controllers/ticketsv1_0.php#L559).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |

## POST /support/{ticket}/comments

Create a new comment

API version 1.0, task `create` in [`commentsv1_0.php`](../../../core/components/com_support/api/controllers/commentsv1_0.php#L136).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `scope` | string | yes | — | Scope type (group, member, etc.) |
| `scope_id` | integer | yes | — | Scope object ID |
| `title` | string | yes | — | Entry title |
| `alias` | string | no | — | Entry alias |

## GET /support/{ticket}/comments/list

Display comments for a ticket

API version 1.0, task `list` in [`commentsv1_0.php`](../../../core/components/com_support/api/controllers/commentsv1_0.php#L45).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `limit` | integer | no | 25 | Number of result to return. |
| `limitstart` | integer | no | 0 | Number of where to start returning results. |
| `search` | string | no | — | A word or phrase to search for. |
| `sort` | string | no | created | Field to sort results by. |
| `sort_Dir` | string | no | desc | Direction to sort results by. |

## DELETE /support/{ticket}/comments/{comment} (v1.0)

Delete a ticket comment

API version 1.0, task `delete` in [`commentsv1_0.php`](../../../core/components/com_support/api/controllers/commentsv1_0.php#L476).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |
| `comment` | integer | yes | 0 | Comment identifier |

## DELETE /support/{ticket}/comments/{comment} (v2.0)

Delete a ticket comment

API version 2.0, task `delete` in [`commentsv2_0.php`](../../../core/components/com_support/api/controllers/commentsv2_0.php#L516).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |
| `comment` | integer | yes | 0 | Comment identifier |

## DELETE /support/{ticket}/comments/{comment} (v2.1)

Delete a ticket comment

API version 2.1, task `delete` in [`commentsv2_1.php`](../../../core/components/com_support/api/controllers/commentsv2_1.php#L677).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |
| `comment` | integer | yes | 0 | Comment identifier |

## GET /support/{ticket}/comments/{comment}

Displays details for a ticket comment

API version 1.0, task `read` in [`commentsv1_0.php`](../../../core/components/com_support/api/controllers/commentsv1_0.php#L382).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |
| `comment` | integer | yes | 0 | Comment identifier |

## PUT /support/{ticket}/comments/{comment}

Update a ticket comment

API version 1.0, task `update` in [`commentsv1_0.php`](../../../core/components/com_support/api/controllers/commentsv1_0.php#L436).

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `ticket` | integer | yes | 0 | Ticket identifier |
| `comment` | integer | yes | 0 | Comment identifier |
