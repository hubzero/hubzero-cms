<!--
status: generated
source: core/plugins/members/*/*.xml
-->

# Members plugins

Parameters of every plugin in the `members` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Members - Account (`plg_members_account`)

Display a member's account information

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `ssh_key_upload` | Show local services details | list | `0 (No)` | Enabling this options allows users to upload their public ssh key via the members account plugin, and shows them their local services account details. Options: `0` No, `1` Yes. |

## Activity (`plg_members_activity`)

Display a list of activity on the site relevant to a user.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `email_digests` | Enable Email Digests | list | `0 (No)` | Allow users to receieve email digests of activity. NOTE: This may require the activation of other plugins to process the emails. Options: `0` No, `1` Yes. |

## Blog (`plg_members_blog`)

Display a blog

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `uploadpath` | Upload path | text | `/site/members/{{uid}}/blog` | File path for uploads |
| `cleanintro` | Clean Introtext | list | `1 (Yes)` | Strip tags from the introtext or show as is in lists of entries. Options: `0` No, `1` Yes. |
| `introlength` | Intro Length | text | `300` | The length of text the intros should be in lists of entries. |
| `feeds_enabled` | Feeds | list | `1 (Enabled)` | PLG_MEMBERS_BLOG_PARAM_FEEDSENABLED_DESCs. Options: `0` Disabled, `1` Enabled. |
| `feed_entries` | Feed Entries | list | `partial (Partial)` | The length of RSS feed entries. Options: `full` Full, `partial` Partial. |

## Citations (`plg_members_citations`)

Displays member citations

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |

## Collections (`plg_members_collections`)

Display collections

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `maxWidth` | Max Image Width | text | `290` | Max Image Width for tile listing |
| `defaultView` | Default View | list | `0` | Default view for the tab. Options: `feed` Activity feed, `collections` User's Collections. |

## Contributions (`plg_members_contributions`)

Display contributions for a member

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Members - Courses (`plg_members_courses`)

Display courses for a member

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Dashboard (`plg_members_dashboard`)

Display a dashboard of arrangable modules

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `allow_customization` | Allow Customization | radio | `1 (Yes)` | Allow users to customize the page or not. Options: `1` Yes, `0` No. |
| `position` | Module position | text | `memberDashboard` | The module position to use to gather draggable modules |
| `defaults` | Defaults | text | — | The default list of modules to show |

## Members - Groups (`plg_members_groups`)

Display a member's groups

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Impact (`plg_members_impact`)

Display a member's impact as a publication author

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `show_impact` | Show Impact | list | `0 (No)` | Show author impact section. Options: `0` No, `2` Yes, show publicly, `1` Yes, show to author only. |
| `contributions` | Include Contributions | list | `0 (No)` | List publications by user. Options: `0` No, `1` Yes. |

## Members - Messages (`plg_members_messages`)

Site message manager for members.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `default_method` | Default Method | list | `email (Email)` | The default messaging method. Options: `email` Email, `internal` Internal. |

## Points (`plg_members_points`)

Display a member's points and transaction history

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Members - Profile (`plg_members_profile`)

Display a member's Profile

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `access_email` | Email | list | `2 (Private)` | Show/Hide the user email. Options: `0` Public, `1` Registered users, `2` Private. |
| `access_optin` | E-mail Updates | list | `2 (Private)` | Show/Hide if the user has elected to receive updates or newsletters by email. Options: `0` Public, `1` Registered users, `2` Private. |

## Projects (`plg_members_projects`)

Display a member's projects

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `display_limit` | Display Limit | text | `50` | Number of items to return |
| `show` | Project privacy level to show | list | `none (No projects (show to only the profile owner))` | What projects can other site members see?. Options: `none` No projects (show to only the profile owner), `public` Public projects, `all` Public and private projects. |

## Members - Contributions - Publications (`plg_members_publications`)

Display a member's publications

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Members - Contributions - Resources (`plg_members_resources`)

Display a member's resources

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |

## Members - Resume (`plg_members_resume`)

Display user uploaded resume(s)

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |

## Members - Todo (`plg_members_todo`)

Display to do items from member projects

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |
| `display_limit` | Display limit | text | `50` | Number of items to return |

## Members - Usage (`plg_members_usage`)

Display usage information for a member

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_tab` | Show in menu | list | `1 (Yes)` | Show a link to this plugin in the Member menu?. Options: `0` No, `1` Yes. |

## Members - Contributions - Wiki (`plg_members_wiki`)

Display a member's wiki pages

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display Limit | text | `50` | Number of items to return |
