<!--
status: generated
source: core/components/com_forum/config/config.xml
-->

# Forum (com_forum)

Community forum

Parameters from [`core/components/com_forum/config/config.xml`](../../../../core/components/com_forum/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `filepath` | File path | text | `/site/forum` | The path to store uploaded files to. |
| `forum` | Feed Data From | list | `both (Site Forum and Group Forums)` | Where the discussions are pulled from for feeds. Options: `site` Site Forum, `group` Group Forums, `both` Site Forum and Group Forums. |
| `allow_anonymous` | Allow anonymous | radio | `1 (Yes)` | Allow users to make posts anonymously?. Options: `0` No, `1` Yes. |
| `threading` | Threading | list | `both` | Determines what type of comment nesting threads have. Options: `list` Flat, one level threads (traditional), `tree` Nested threads. |
| `threading_depth` | Threading level | text | `3` | How many levels deep a nested thread can go. |
