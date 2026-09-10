<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/extensions/plugins
source-id: 3408
modified: 2009-10-01
imported: 2026-09-09
-->
# Plug-in Manager

A plugin is a piece of code that answers an event. The Plug-in Manager cannot
install or delete plugins; it enables and disables them, sets their access
level, changes the order they run in, and edits their parameters.

Select **Extensions** → **Plug-in Manager**, or go to
`/administrator/index.php?option=com_plugins`.

## How plugins work

Plugins live in [`core/plugins/`](../../../core/plugins), and a hub's own in
`app/plugins/`, which takes precedence. Each plugin is a directory two levels
down — `plugins/<folder>/<name>/` — containing `<name>.php` and
`<name>.xml`. The folder is the plugin's group, and it decides which events
the plugin can answer.

When the platform reaches a point where extensions may contribute, it imports
every enabled plugin in the relevant folder, in ordering order, and calls the
matching method on each. A plugin in the `authentication` folder is asked to
authenticate; one in `content` is given content to filter; one in `cron` is
asked for scheduled jobs.

Several folders work differently: instead of filtering something, the plugin
*is* a whole section of a page. Every tab on a group, a member profile, a
project, a resource or a publication is a plugin in the `groups`, `members`,
`projects`, `resources` or `publications` folder. Disabling one removes that
tab.

## The plugin groups

Thirty-seven folders ship with the platform, holding 332 plugins.

| Folder | Plugins | What they do |
|---|---|---|
| `answers` | 2 | Extras on question pages |
| `antispam` | 6 | Spam detectors used when content is posted |
| `authentication` | 13 | Log-in methods, local and third-party |
| `authfactors` | 2 | Second factors for two-factor authentication |
| `blog` | 2 | Blog entry extras |
| `captcha` | 3 | Challenge widgets for forms |
| `cart` | 3 | Store checkout steps |
| `citation` | 4 | Citation formats and importers |
| `content` | 13 | Filters applied to content as it is rendered |
| `courses` | 14 | Course tabs and asset handlers |
| `cron` | 14 | Scheduled jobs offered to the cron manager |
| `editors` | 8 | WYSIWYG and plain editors |
| `editors-xtd` | 4 | Buttons added below an editor |
| `extension` | 1 | Installer support for legacy extension packages |
| `filesystem` | 5 | Storage back ends for project files |
| `geocode` | 15 | Address and coordinate lookup services |
| `groups` | 18 | Tabs and features on group pages |
| `handlers` | 8 | Viewers for published content: video, audio, PDF, LaTeX, notebooks |
| `hubzero` | 5 | Shared services: comments, autocompleter, system templates and tickets |
| `members` | 20 | Tabs and features on member profiles |
| `metadata` | 1 | Metadata attached to content records |
| `newsletter` | 3 | Newsletter composition and sending |
| `oaipmh` | 2 | Records exposed over OAI-PMH |
| `projects` | 11 | Tabs and features on projects |
| `publications` | 17 | Publication tabs and curation steps |
| `resources` | 22 | Resource tabs and features |
| `search` | 26 | Indexers, one per searchable content type |
| `support` | 13 | Ticket handling and support extras |
| `system` | 29 | Code that runs on every request |
| `tags` | 14 | Behaviour attached to tags |
| `tools` | 1 | Tool session viewers |
| `update` | 2 | Housekeeping run after an update |
| `usage` | 7 | Usage statistics collectors |
| `user` | 11 | Reactions to account creation, change and deletion |
| `whatsnew` | 6 | Content types listed on the What's New page |
| `wiki` | 4 | Wiki macros and parsers |
| `xmessage` | 3 | Delivery channels for internal messages |

For the parameters each plugin exposes, see
[the generated plugin reference](../../reference/configuration/plugins/README.md).

> **Note:** A plugin also has to be enabled as an *extension* before its row
> matters. The Extension Manager and the Plug-in Manager both write the same
> `enabled` flag on the same `#__extensions` row, so either screen will do.

## The list

| Column | Meaning |
|---|---|
| Checkbox | Selects rows for the toolbar. A plugin whose files are missing has no checkbox. |
| **Plug-in Name** | The plugin's translated name, usually *Group - Name*, for example *Authentication - Facebook*. Click it to edit. |
| **Status** | Enabled or Disabled. Click the icon to toggle. |
| **Ordering** | The order plugins in the same folder run in. |
| **Type** | The plugin's folder — its group. |
| **Element** | The plugin's directory and file name. |
| **Access** | The access level required. |
| **ID** | The row's primary key. |

A plugin whose files are gone is shown with a marked row and forced to
disabled.

Three filters sit above the list, plus a search box that matches the name:

- **- Select Status -**: Enabled, Disabled.
- **- Select Type -**: any folder that has plugins installed.
- **- Select Access -**: any defined access level.

## Toolbar

| Button | Effect |
|---|---|
| **Edit** | Opens the selected plugin. |
| **Enable** / **Disable** | Changes the state of the selected plugins. |
| **Check In** | Releases plugins left checked out by an interrupted edit. |
| **Options** | Permissions for `com_plugins`. There are no other settings. |
| **Help** | Opens the built-in help screen. |

There is no **New** and no **Delete**. Plugins arrive and leave with the code.

## Editing a plugin

The edit screen has three editable fields:

| Field | Notes |
|---|---|
| **Status** | Enabled or Disabled. |
| **Access** | The access level required for the plugin to run for a given visitor. |
| **Ordering** | Where the plugin sits among the others in its folder. |

Beside them, read-only, are the plugin's name, ID, **Type** (its folder) and
**Element**, and its description from the XML manifest. Below, the plugin's
own parameters appear in collapsible panels. Many plugins have none.

The toolbar is **Save**, **Save & Close**, **Close** and **Help**.

## Enabling or disabling a plugin

1. Go to **Extensions** → **Plug-in Manager**.
2. Narrow the list with the **- Select Type -** filter or the search box.
3. Tick the plugin, then select **Enable** or **Disable**. Clicking the icon
   in the **Status** column does the same thing for a single plugin.

Disabling a plugin stops it running everywhere, front end and back end alike.
If the plugin supplies a tab, that tab disappears.

## Changing the order plugins run in

Order matters inside a folder. Content plugins transform the same text one
after another; authentication plugins are offered a log-in attempt in turn.

1. Go to **Extensions** → **Plug-in Manager**.
2. Filter by **- Select Type -** so you are looking at one group.
3. Select the **Ordering** column heading to sort by it. The up and down
   arrows and the order boxes only work while the list is sorted this way.
4. Move rows with the arrows, or type numbers into the boxes and select the
   save icon in the column heading.
