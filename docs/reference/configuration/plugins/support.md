<!--
status: generated
source: core/plugins/support/*/*.xml
-->

# Support plugins

Parameters of every plugin in the `support` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Support - Answers (`plg_support_answers`)

Various functions for the Report Abuse Component

This plugin has no parameters.

## Support - Blog Comments (`plg_support_blog`)

Various functions for the Report Abuse functionality in the Support Component

This plugin has no parameters.

## Support - Captcha (`plg_support_captcha`)

Captcha plugin for support tickets

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `modCaptcha` | Module CAPTCHA Type | list | `text (Text-based)` | Select the type of CAPTCHA to use for module forms. Options: `text` Text-based, `image` Image-based. |
| `comCaptcha` | Component CAPTCHA Type | list | `image (Image-based)` | Select the type of CAPTCHA to use for component forms. Options: `text` Text-based, `image` Image-based. |

### Image

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `bgColor` | Background Color | text | `#2c8007` | Enter color(6 letter hex value ) |
| `textColor` | Text Color | text | `#ffffff` | Enter color(6 letter hex value) |
| `imageFunction` | Select Image Function | list | `Adv (Distorted letters)` | Select wether you want to show distorted letters or plane letters. Options: `Plain` Plain letters, `Adv` Distorted letters. |

## Support - Comments (`plg_support_comments`)

Various functions for the Report Abuse Component

This plugin has no parameters.

## Support - Forum Abuse reports (`plg_support_forum`)

Various functions for the Report Abuse Component

This plugin has no parameters.

## Support - KB Comments (`plg_support_kb`)

Various functions for the Report Abuse Component

This plugin has no parameters.

## Support - Markdown Parser (`plg_support_markdown`)

Loads a Markdown parser and performs any called actions on text passed to it

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `type` | Style | list | `Markdown` | Choose the flavor of Markdown to use. Options: `Markdown`, `GithubMarkdown` Github Markdown, `MarkdownExtra` Markdown Extra. |

## Support - Publications (`plg_support_publications`)

Various functions for the Report Abuse Component

This plugin has no parameters.

## Support - Resources (`plg_support_resources`)

Various functions for the Report Abuse Component

This plugin has no parameters.

## Support - Slack (`plg_support_slack`)

Send notifications to Slack (http://slack.com) when tickets are created and updated.

### Endpoint one

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `endpoint` | Endpoint | text | — | Slack endpoint. E.g., http://your.slack.endpoint |
| `username` | Username | text | — | The default username that messages will be sent from. |
| `channel` | Channel | text | `#channel` | The default channel that messages will be sent to. |
| `notify_created` | Notify on creation | list | `1 (Yes)` | Send a message when tickets are created. Options: `0` No, `1` Yes. |
| `group_created` | Ticket Group | text | — | Only tickets belonging to the specified group will be processed. |
| `channel_created` | Alternate Channel | text | — | An alternate channel that messages will be sent to. If none specified, it will go to the default channel. |
| `notify_updated` | Notify on update | list | `1 (Yes)` | Send a message when tickets are updated. Options: `0` No, `1` Yes. |
| `notify_private` | Notify of private comments | list | `1 (Yes)` | Send a message when a ticket update is marked as private. Options: `0` No, `1` Yes. |
| `group_updated` | Ticket Group | text | — | Only tickets belonging to the specified group will be processed. |
| `channel_updated` | Alternate Channel | text | — | An alternate channel that messages will be sent to. If none specified, it will go to the default channel. |

### Endpoint two

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `endpoint2` | Endpoint | text | — | Slack endpoint. E.g., http://your.slack.endpoint |
| `username2` | Username | text | — | The default username that messages will be sent from. |
| `channel2` | Channel | text | `#channel` | The default channel that messages will be sent to. |
| `notify_created2` | Notify on creation | list | `1 (Yes)` | Send a message when tickets are created. Options: `0` No, `1` Yes. |
| `group_created2` | Ticket Group | text | — | Only tickets belonging to the specified group will be processed. |
| `channel_created2` | Alternate Channel | text | — | An alternate channel that messages will be sent to. If none specified, it will go to the default channel. |
| `notify_updated2` | Notify on update | list | `1 (Yes)` | Send a message when tickets are updated. Options: `0` No, `1` Yes. |
| `notify_private2` | Notify of private comments | list | `1 (Yes)` | Send a message when a ticket update is marked as private. Options: `0` No, `1` Yes. |
| `group_updated2` | Ticket Group | text | — | Only tickets belonging to the specified group will be processed. |
| `channel_updated2` | Alternate Channel | text | — | An alternate channel that messages will be sent to. If none specified, it will go to the default channel. |

### Endpoint three

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `endpoint3` | Endpoint | text | — | Slack endpoint. E.g., http://your.slack.endpoint |
| `username3` | Username | text | — | The default username that messages will be sent from. |
| `channel3` | Channel | text | `#channel` | The default channel that messages will be sent to. |
| `notify_created3` | Notify on creation | list | `1 (Yes)` | Send a message when tickets are created. Options: `0` No, `1` Yes. |
| `group_created3` | Ticket Group | text | — | Only tickets belonging to the specified group will be processed. |
| `channel_created3` | Alternate Channel | text | — | An alternate channel that messages will be sent to. If none specified, it will go to the default channel. |
| `notify_updated3` | Notify on update | list | `1 (Yes)` | Send a message when tickets are updated. Options: `0` No, `1` Yes. |
| `notify_private3` | Notify of private comments | list | `1 (Yes)` | Send a message when a ticket update is marked as private. Options: `0` No, `1` Yes. |
| `group_updated3` | Ticket Group | text | — | Only tickets belonging to the specified group will be processed. |
| `channel_updated3` | Alternate Channel | text | — | An alternate channel that messages will be sent to. If none specified, it will go to the default channel. |

### Message

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `link_names` | Link names | list | `0 (No)` | Whether names like @regan or #accounting should be linked. Options: `0` No, `1` Yes. |
| `allow_markdown` | Allow Markdown | list | `1 (Yes)` | Whether Markdown should be parsed in messages. Options: `0` No, `1` Yes. |

## Support - Transfer (`plg_support_transfer`)

Supporting easy transfer of Questions/Wishes/Tickets

This plugin has no parameters.

## Support - Wiki Comments (`plg_support_wiki`)

PLG_SUPPORT_WIKI_XML_DESC

This plugin has no parameters.

## Support - Wishlist (`plg_support_wishlist`)

Various functions for the Report Abuse Component

This plugin has no parameters.
