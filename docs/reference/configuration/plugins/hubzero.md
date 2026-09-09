<!--
status: generated
source: core/plugins/hubzero/*/*.xml
-->

# Hubzero plugins

Parameters of every plugin in the `hubzero` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## HUBzero - Autocompleter (`plg_hubzero_autocompleter`)

Creates an auto-complete field for different types of entry (tags, groups, users)

This plugin has no parameters.

## Hubzero - Comments (`plg_hubzero_comments`)

HUBzero Comments

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `comments_depth` | Depth | text | `3` | The number of levels comments can be nested. 1 level would be just comments, no replies to comments. |
| `comments_limit` | Limit | text | `25` | The number of records to display at a time. |
| `comments_sorting` | Sorting | list | `asc (Oldest first)` | The chronological order the comments are displayed. Options: `asc` Oldest first, `desc` The chronological order the comments are displayed. |
| `comments_viewable` | Viewable by | list | `0 (Anyone)` | Set the view access type. Options: `0` Anyone, `1` Logged-in users. |
| `comments_editable` | Can edit | list | `1 (Yes)` | Users can edit their comments. Options: `0` No, `1` Yes. |
| `comments_deletable` | Can delete | list | `0 (No)` | Users can delete their comments. Options: `0` No, `1` Yes. |
| `comments_votable` | Can vote | list | `1 (Yes)` | Users can cote comments up or down. Options: `0` No, `1` Yes. |
| `comments_close` | Close Comments | list | `year (After 1 year)` | Auto-close comments on entries from the date. Options: `never` Never, `now` Immediately, `day` After 1 day, `week` After 1 week, `month` After 1 month, `6months` After 6 months, `year` After 1 year. |
| `comments_feed` | Feeds | list | `1 (Enabled)` | Enable/Disable RSS feeds. Options: `0` Disabled, `1` Enabled. |

## HUBzero - [system] Template Info (`plg_hubzero_systemplate`)

Return information about the current active template

This plugin has no parameters.

## HUBzero - [system] Ttickets Info (`plg_hubzero_systickets`)

Return stats for support tickets

This plugin has no parameters.

## HUBzero - [system] Users Info (`plg_hubzero_sysusers`)

Return stats for users

This plugin has no parameters.
