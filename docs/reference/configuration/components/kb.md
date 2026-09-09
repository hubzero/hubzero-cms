<!--
status: generated
source: core/components/com_kb/config/config.xml
-->

# Kb (com_kb)

Manage a knowledgebase

Parameters from [`core/components/com_kb/config/config.xml`](../../../../core/components/com_kb/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_date` | Date | list | `2 (Modified)` | Show/Hide the entry date. Options: `0` Hide, `1` Created, `2` Modified. |
| `allow_comments` | Comments | list | `1 (Allow)` | Allow/Disallow comments on entries. Options: `0` Disallow, `1` Allow. |
| `close_comments` | Close Comments | list | `year (After 1 year)` | Auto-close comments on entries from the date. Options: `never` Never, `now` Immediately, `day` After 1 day, `week` After 1 week, `month` After 1 month, `6months` After 6 months, `year` After 1 year. |
| `feeds_enabled` | Feeds | list | `1 (Enabled)` | Enable/Disable RSS feeds. Options: `0` Disabled, `1` Enabled. |
| `feed_entries` | Feed Entries | list | `partial (Partial)` | The length of RSS feed entries. Options: `full` Full, `partial` Partial. |
