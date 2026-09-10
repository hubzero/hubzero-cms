<!--
status: generated
source: core/plugins/projects/*/*.xml
-->

# Projects plugins

Parameters of every plugin in the `projects` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Projects - Databases (`plg_projects_databases`)

Databases for Projects environment

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `db_host` | Database Host | text | `localhost` | The hostname of the database server |
| `db_user` | Database User [rw] | text | `datawriter` | User name for the MySQL account |
| `db_password` | Database Password [rw] | password | — | Password for the MySQL account |
| `db_ro_user` | Database User [ro] | text | `dataviewer` | User name for the MySQL account [used by the dataviewer] |
| `db_ro_password` | Database Password [ro] | password | — | Password for the MySQL account [used by the dataviewer] |
| `restricted` | Restricted to projects | text | — | Comma-separated aliases of projects that have databases plugin enabled (empty field means NO RESTRICTIONS) |

## Projects - Feed (`plg_projects_feed`)

Display activity for a project

This plugin has no parameters.

## Projects - Files (`plg_projects_files`)

Manage project files

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `maxUpload` | Max Upload Size (Bytes) | text | `104857600` | Maximum upload file size for project files in bytes (master default set to 100MB (104857600 bytes)) |
| `maxDownload` | Max Download Size (Bytes) | text | `104857600` | Maximum download file size for project files in bytes (master default set to 100MB (104857600 bytes)) |
| `gitSizeLimit` | Max Size (Bytes) for files to check into Git | text | `104857600` | Maximum size for project files to be checked into Git, in bytes (master default set to 100MB (104857600 bytes)) |
| `reservedNames` | Reserved Directory Names | text | `google , dropbox, shared, temp` | Reserved directory names |
| `disk_usage` | Report Disk Usage | radio | `0 (Size of .git (includes versions and deleted files))` | Specify how to report disk usage. Options: `0` Size of .git (includes versions and deleted files), `1` Size of files currently in project. |
| `enable_google` | Google Connection Enabled | list | `0 (No)` | Allow projects to connect with Google docs. Options: `0` No, `1` Yes. |
| `connectedProjects` | Connected Projects | text | — | Comma-separated aliases of projects that may connect to outside services (empty means all projects may connect) |
| `google_clientId` | Google Client ID | text | — | Google client ID |
| `google_clientSecret` | Google Client Secret | text | — | Google client Secret |
| `google_appKey` | Google API Key | text | — | Google API key |
| `auto_sync` | Auto Sync | list | `0 (No auto sync)` | Initiate new sync automatically within specified time period after previous sync. Options: `0` No auto sync, `0.15` Every 10 minutes, `0.5` Every half hour, `1` Every hour, `2` Every 2 hours, `6` Every 6 hours. |
| `latex` | Enable LaTeX Compile | list | `0 (No)` | Enable LaTeX Compile. Options: `0` No, `1` Yes. |
| `texpath` | Path to LaTeX | text | — | Path to LaTeX |
| `gspath` | Path to Ghostscript | text | — | Path to Ghostscript |
| `default_action` | Default Action | list | `browse (Browse (browse local files))` | The default action to execute when opening the files tab without an action otherwise specified. Options: `browse` Browse (browse local files), `connections` Connections (view available connections). |
| `handler_base_path` | Handler Base Path | text | `/srv/projects/{project}/files/{file}` | The path pattern to use when constructing tool launch links for files within the browse view. {project} will be dynamically replaced with the project's alias and {file} will be replaced with the file's name |
| `default_connection_name` | Default Connection Name | text | `%s Master Repository` | The label given to the primary versioned repository within the files connections inteface. The name supports the '%s' placeholder for the project name. |
| `project_quota` | Enable New Project Usage Indicator | list | `0 (No)` | Enable project usage. Options: `0` No, `1` Yes. |

## Projects - HIPAA Compliant (`plg_projects_hipaacompliant`)

Save HIPAA Compliance checkbox

This plugin has no parameters.

## Projects - Info (`plg_projects_info`)

Display project info

This plugin has no parameters.

## Projects - Links (`plg_projects_links`)

Manage external content for publications

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display limit | text | `50` | Number of items to return |

## Projects - Notes (`plg_projects_notes`)

Manage project notes

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display limit | text | `50` | Number of items to return |
| `enable_publinks` | Enable public links | radio | `0 (No)` | Enable project team members to generate public links to notes and list/unlist notes on project public page. Options: `0` No, `1` Yes. |

## Projects - Publications (`plg_projects_publications`)

Manage project publications and contribution process

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display limit | text | `50` | Number of items to return |
| `updatable_areas` | Updatable fields | text | — | Publication info areas that may be updated after version release |
| `image_types` | Gallery image formats | text | `jpg, jpeg, gif, png` | Allowable image types for gallery |
| `video_types` | Gallery video formats | text | `avi, mpeg, mov, mpg, wmv, rm, mp4` | Allowable video types for gallery |
| `googleview` | Use google viewer | radio | `0 (No)` | Allow certain types of docs to be served inline via Google Docs viewer. Options: `0` No, `1` Yes. |
| `restricted` | Restricted to projects | text | — | Comma-separated aliases of projects that have publications plugin enabled (empty field means NO RESTRICTIONS) |
| `new_pubs` | New publications | radio | `0 (No)` | Enable new publications UI elements. Options: `0` No, `1` Yes. |

## Projects - Team (`plg_projects_team`)

Display and manage project team

This plugin has no parameters.

## Projects - Todo (`plg_projects_todo`)

Display and manage project todo items

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `display_limit` | Display limit | text | `50` | Number of items to return |

## Projects - Watch (`plg_projects_watch`)

Let project members/public subscribe to project activity notifications

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `autosubscribe` | Auto-subscribe members? | radio | `0 (No (members may opt-in))` | Automatically subscribe members to email notifications on certain activities?. Options: `0` No (members may opt-in), `1` Yes (members are auto-subscribed and may opt-out). |
