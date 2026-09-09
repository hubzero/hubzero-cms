<!--
status: generated
source: core/plugins/update/*/*.xml
-->

# Update plugins

Parameters of every plugin in the `update` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Update - Cache (`plg_update_cache`)

Update event for cleaning the cache

This plugin has no parameters.

## Update - Support (`plg_update_support`)

Update event for support

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `support_ticket_state1` | Status 1: | ticketstate | `0` | The status of the ticket. Up to three statuses may be chosen. Choosing NO statuses is equivelant to 'all' for the open\|closed state of the ticket. |
| `support_ticket_state2` | Status 2: | ticketstate | `--` | The status of the ticket. Up to three statuses may be chosen. Choosing NO statuses is equivelant to 'all' for the open\|closed state of the ticket. |
| `support_ticket_state3` | Status 3: | ticketstate | `--` | The status of the ticket. Up to three statuses may be chosen. Choosing NO statuses is equivelant to 'all' for the open\|closed state of the ticket. |
| `support_ticket_severity` | Tickets with severity | list | `all (All)` | Ticket severity to message users about. Options: `all` All, `critical,major` High, `normal` Normal, `minor` Low. |
| `support_ticket_activity` | Last activity | list | `-2week (Older than 2 weeks)` | When the last activity on the ticket was. Options: `all` All, `-day` Older than 1 day, `-week` Older than 1 week, `-2week` Older than 2 weeks, `-3week` Older than 3 weeks, `-month` Older than 1 month, `-6month` Older than 6 months, `-year` Older than 1 year. |
| `support_ticket_group` | For group | text | — | Only users within the group specified will be messaged. |
| `support_ticket_owners` | Owned by | textarea | — | A comma-separated list of usernames. |
| `support_ticket_submitters` | Submitted by | textarea | — | A comma-separated list of usernames. |
| `support_ticket_notify` | Who to notify | textarea | `{config.mailfrom}` | A comma-separated list of usernames or email addresses. |
| `support_ticket_excludeTags` | Exclude tickets tagged with | textarea | — | A comma-separated list of tags. |
| `support_ticket_includeTags` | Include tickets tagged with | textarea | — | A comma-separated list of tags. |
| `support_ticket_closed` | Set status to: | ticketstate | `-1` | The status to set the ticket(s) to. |
| `support_ticket_message` | Email message | ticketmessage | `0` | A message to send upon closing the ticket. |
