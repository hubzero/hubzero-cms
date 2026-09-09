<!--
status: generated
source: core/plugins/cron/*/*.xml
-->

# Cron plugins

Parameters of every plugin in the `cron` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Cron - Activity (`plg_cron_activity`)

Cron events for user activity

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `email_transport_mechanism` | PLG_CRON_FORUM_EMAIL_TRANSPORT_MECHANISM_LABEL | mailers | `0` | PLG_CRON_FORUM_EMAIL_TRANSPORT_MECHANISM_DESC |

## Cron - Cache (`plg_cron_cache`)

Cron events for cache

This plugin has no parameters.

## Cron - Courses (`plg_cron_courses`)

Cron events for courses

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `course` | Select course to receive emails | sql | `1` |  |

## Cron - Forum (`plg_cron_forum`)

Cron events for forums

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `email_transport_mechanism` | Email Transport | mailers | `0` | The email transport mechanism used to deliver digest messages |

## Cron - Groups (`plg_cron_groups`)

Cron events for groups

This plugin has no parameters.

## Cron - Members (`plg_cron_members`)

Cron events for members

This plugin has no parameters.

## Cron - Newsletter (`plg_cron_newsletter`)

Cron events for newsletter

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `newsletter_queue_limit` | Queued Emails Limit: | text | `25` | Number of queued emails to send out each time the cron process is run. |

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `newsletter_ips_limit` | IPs Limit: | text | `100` | Number of IPs to process each time the cron process is run. |

## Cron - Projects (`plg_cron_projects`)

Cron events for projects

This plugin has no parameters.

## Cron - Publications (`plg_cron_publications`)

Cron events for publications

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `userids` | List of user ids of authors | text | — | Only users with ids in the list (comma-separated) will get author stats |

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `startdate` | Start Date | text | — | Date how far back the cron job should check updated status of embargoed publications. Defaults to yesterday. |

## Cron - Resources (`plg_cron_resources`)

Cron events for resources

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `email_transport_mechanism` | Email Transport | mailers | `0` | The email transport mechanism used to deliver messages |

## Cron - Search (`plg_cron_search`)

Cron events for search

This plugin has no parameters.

## Cron - Storefront (`plg_cron_storefront`)

Cron events for the Storefront

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `publish_down_notification1` | Publish Down Notification 1 | text | `0` | Publish Down Notification 1: Days before publish down date |
| `publish_down_notification2` | Publish Down Notification 1 | text | `0` | Publish Down Notification 2: Days before publish down date |

## Cron - Support (`plg_cron_support`)

Cron events for support

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `support_ticketpending_new` | Has status: New | list | `0 (No)` | Ticket is open and new (no action taken yet). Options: `0` No, `1` Yes. |
| `support_ticketpending_waiting` | Has status: Waiting | list | `1 (Yes)` | Ticket has a status of waiting user action. Options: `0` No, `1` Yes. |
| `support_ticketpending_accepted` | Has status: Accepted | list | `0 (No)` | Ticket is open and has some action taken on it (tagged, comments, etc). Options: `0` No, `1` Yes. |
| `support_ticketpending_status` | Has status: | ticketstatus | `0` | The status of the ticket. |
| `support_ticketpending_severity` | Tickets with severity | list | `all (All)` | Ticket severity to message users about. Options: `all` All, `critical,major` High, `normal` Normal, `minor` Low. |
| `support_ticketpending_activity` | Last activity | list | `-2week (Older than 2 weeks)` | When the last activity on the ticket was. Options: `all` All, `-day` Older than 1 day, `-week` Older than 1 week, `-2week` Older than 2 weeks, `-3week` Older than 3 weeks, `-month` Older than 1 month, `-6month` Older than 6 months, `-year` Older than 1 year. |
| `support_ticketpending_group` | For group | text | — | Only users within the group specified will be messaged. |
| `support_ticketpending_owned` | Is assigned? | list | `0 (All)` | Ticket is assigned to someone or not. Options: `0` All, `1` No, `2` Yes. |
| `support_ticketpending_owners` | Owned by | textarea | — | A comma-separated list of usernames. |
| `support_ticketpending_submitters` | Submitted by | textarea | — | A comma-separated list of usernames. |
| `support_ticketpending_notify` | Who to notify | textarea | `{config.mailfrom}` | A comma-separated list of usernames or email addresses. |
| `support_ticketpending_excludeTags` | Exclude tickets tagged with | textarea | — | A comma-separated list of tags. |
| `support_ticketpending_includeTags` | Include tickets tagged with | textarea | — | A comma-separated list of tags. |
| `support_ticketpending_message` | Email message | ticketmessage | `0` | A message to send upon closing the ticket. |

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `support_ticketreminder_severity` | Tickets with severity | list | `all (All)` | Ticket severity to message users about. Options: `all` All, `critical,major` High, `normal` Normal, `minor` Low. |
| `support_ticketreminder_group` | For users in group | text | — | Only users within the group specified will be messaged. |

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `support_ticketlist_open` | Open/Closed | list | `1 (Open)` | Ticket is open or closed. Options: `1` Open, `0` Closed. |
| `support_ticketlist_status` | Has status: | ticketstatus | `0` | The status of the ticket. |
| `support_ticketlist_severity` | Tickets with severity | list | `all (All)` | Ticket severity to message users about. Options: `all` All, `critical,major` High, `normal` Normal, `minor` Low. |
| `support_ticketlist_created` | Created | list | `+week (Created in the past week)` | Ticket creation time. Options: `-day` Older than 1 day, `-week` Older than 1 week, `-2week` Older than 2 weeks, `-3week` Older than 3 weeks, `-month` Older than 1 month, `-6month` Older than 6 months, `-year` Older than 1 year, `+day` Created in the past day, `+week` Created in the past week, `+2week` Created in the past 2 weeks, `+3week` Created in the past 3 weeks, `+month` Created in the past month, `+6month` Created in the past 6 months, `+year` Created in the past year. |
| `support_ticketlist_activity` | Last activity | list | `all (All)` | When the last activity on the ticket was. Options: `all` All, `-day` Older than 1 day, `-week` Older than 1 week, `-2week` Older than 2 weeks, `-3week` Older than 3 weeks, `-month` Older than 1 month, `-6month` Older than 6 months, `-year` Older than 1 year. |
| `support_ticketlist_group` | For group | text | — | Only users within the group specified will be messaged. |
| `support_ticketlist_owned` | Is assigned? | list | `0 (All)` | Ticket is assigned to someone or not. Options: `0` All, `1` No, `2` Yes. |
| `support_ticketlist_owners` | Owned by | textarea | — | A comma-separated list of usernames. |
| `support_ticketlist_submitters` | Submitted by | textarea | — | A comma-separated list of usernames. |
| `support_ticketlist_notify` | Who to notify | textarea | `{config.mailfrom}` | A comma-separated list of usernames or email addresses. |
| `support_ticketlist_excludeTags` | Exclude tickets tagged with | textarea | — | A comma-separated list of tags. |
| `support_ticketlist_includeTags` | Include tickets tagged with | textarea | — | A comma-separated list of tags. |

### 

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `support_tickettemp_age` | Older than | list | `-week` | The age of the temporary directory is older than the selected value. Options: `1` Older than 1 day, `7` Older than 1 week, `14` Older than 2 weeks, `21` Older than 3 weeks, `30` Older than 1 month, `181` Older than 6 months, `365` Older than 1 year. |

## Cron - Users (`plg_cron_users`)

Cron events for users

This plugin has no parameters.
