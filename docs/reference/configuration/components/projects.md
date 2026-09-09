<!--
status: generated
source: core/components/com_projects/config/config.xml
-->

# Projects (com_projects)

Manage projects

Parameters from [`core/components/com_projects/config/config.xml`](../../../../core/components/com_projects/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `suggest_feature` | Allow Feature Suggestion? | list | `1 (Yes)` | Display links on the Features page for suggesting new features. Note: This requires the Wishlist component be enabled. Options: `0` No, `1` Yes. |
| `min_name_length` | Minimum characters in name | text | `6` | Min number of characters a project alias name can contain |
| `max_name_length` | Maximum characters in name | text | `25` | Max number of characters a project alias name can contain |
| `reserved_names` | Reserved project names | text | `clone, temp, test` | Words that cannot be used as project names |
| `imagepath` | Image path | text | `/site/projects` | File path to project images (thumbnails) |
| `defaultpic` | Default picture | text | `/components/com_projects/site/assets/img/project.png` | Default placeholder image for project pictures |
| `showthumbemail` | Show project thumb in emails | list | `0 (Hide)` | Show project thumbnail in html emails (must be OFF for firewalled hubs). Options: `0` Hide, `1` Show. |
| `logging` | Log activity | list | `0 (Do not log)` | Enable detailed activity logging. Options: `0` Do not log, `1` Log all activity except for AJAX calls, `2` Log all activity. |
| `messaging` | Messaging | list | `0 (Off)` | Enable hub messaging. Options: `0` Off, `1` On. |
| `accesslevel` | Default access | accesslevel | `5` | Set the default access level for new projects |
| `layout` | Default project page layout | list | `standard (Standard (left side menu))` | Set the default project page layout for team members. Options: `standard` Standard (left side menu), `extended` New (top menu). |
| `sidebox_limit` | Side module item limit | text | `3` | Number of items per box in project page side modules |
| `group_prefix` | Project group prefix | text | `pr-` | Prefix for project group name (system group provisioned for each project to handle permissions) |
| `documentation` | URL to documentation | text | `/projects/features` | URL to a page with the user guide |

## Projectsetup

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `grantinfo` | Collect grant info at setup? | list | `0 (No)` | Ask project creator for grant information, e.g. [NSF] grant id, title, PI. Options: `0` No, `1` Yes. |
| `confirm_step` | Setup - 'Agree to Terms' screen | list | `0 (Off)` | Enable 'Agree to Terms' step during setup. Options: `0` Off, `1` On. |
| `edit_settings` | Allow project settings editing? | list | `0 (No)` | Enable a screen to edit project settings after project setup. Options: `0` No, `1` Yes. |
| `edit_description` | Collaborators can edit description? | list | `0 (No)` | Allow collaborators/authors to edit the project description?. Options: `0` No, `1` Yes. |
| `custom_profile` | Use custom profile description template? | list | `0 (No)` | Allows administrators to define project info fields. Options: `0` No, `custom` YES - Use custom description. |
| `restricted_data` | Ask about sensitive data? | list | `0 (No)` | Include a question about sensitive data (HIPAA/FERPA/Export Control). Options: `0` No, `2` YES, with one general question, `1` YES, with HIPAA/FERPA etc. options. |
| `approve_restricted` | Must approve sensitive data projects? | list | `0 (No)` | Require approval for sensitive data projects?. Options: `0` No, `1` Yes. |
| `init_team` | Setup - Managers | list | `0 (Only project creator)` | This determines who is auto-added as a project manager when creating a new project. Note: This assignment happens before the project creator is given the choice of syncing group membership or having selective members. Options: `0` Only project creator, `1` Group managers (if owned by a group). |
| `sync_behavior` | Members - Group syncing | list | `1 (Sync with group)` | This determines if group-owned projects default to auto-syncing all membership with the group or default to selective membership. Options: `0` Selective membership, `1` Sync with group. |
| `privacylink` | URL to Privacy Terms | text | `/legal/privacy` | URL to Privacy Terms |
| `HIPAAlink` | URL to HIPAA information | text | `/legal/privacy` | URL to HIPAA information |
| `FERPAlink` | URL to FERPA information | text | `/legal/privacy` | COM_PROJECTS_CONFIG_FERPALINK_DESC |

## Admingroups

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `creatorgroup` | Restrict project creation to group | text | — | Alias of a group that can create projects (optional, will restrict all others!) |
| `admingroup` | Admin group | text | — | Alias of an administrative group that gets notified when a new project is created or over quota |
| `sdata_group` | Sensitive data reviewers group | text | — | Alias of an administrative group that can access a special project listing to review info on HIPAA/FERPA/export control |
| `ginfo_group` | Sponsored projects reviewers group | text | — | Alias of an administrative group that can access a special project listing to review and edit sponsored project information and bump up quota |
| `reportgroup` | Report access group | text | — | Alias of an administrative group that has access to custom reports |

## Filerepo

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `webpath` | Files Git repo path | text | `/srv/projects` | Master path for storing project file repos |
| `offroot` | Files repo path is... | list | `1 (absolute)` | Did you specify files Git repo path relative to web root?. Options: `0` relative to web root, `1` absolute. |
| `gitpath` | Git path | text | `/opt/local/bin/git` | Path to Git |
| `defaultQuota` | Default quota (GB) | text | `5` | Default disk quota for project files in gigabytes |
| `premiumQuota` | Premium quota (GB) | text | `30` | Premium disk quota for project files in gigabytes |
| `approachingQuota` | Quota warning at (%) | text | `90` | Issue disk quota warning when disk space is used at a certain percentage amount of the given quota |
| `pubQuota` | Publication quota (GB) | text | `1` | Default disk quota for published files in gigabytes |
| `premiumPubQuota` | Premium publication quota (GB) | text | `30` | Premium disk quota for published files in gigabytes |
| `simpleSizeReporting` | Simple Size Reporting | list | `0 (Off)` | Simple size reporting ignores version history and bundle size of published content when reporting disk usage. Options: `0` Off, `1` On. |
