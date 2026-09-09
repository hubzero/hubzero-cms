<!--
status: generated
source: core/components/com_groups/config/config.xml
-->

# Groups (com_groups)

Parameters from [`core/components/com_groups/config/config.xml`](../../../../core/components/com_groups/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## General

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `auto_approve` | Auto Approve Groups:? | list | `1 (Yes)` | Groups are auto approved at creation. Otherwise Hub administrator must manually approve group. Options: `0` No, `1` Yes. |
| `group_reviewer` | Group reviewers | text | — | Enter the comma-separated email addresses of the reviewers who approve the group submission and changes |

## Intro Page

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `intro_mygroups` | Show My Groups | list | `1 (Yes)` | Display list of my groups on group landing page?. Options: `0` No, `1` Yes. |
| `intro_interestinggroups` | Show Interesting Groups | list | `1 (Yes)` | Display list of groups having matching tags as the user on the group landing page?. Options: `0` No, `1` Yes. |
| `intro_populargroups` | Show Popular Groups | list | `1 (Yes)` | Display list of popular groups on group landing page?. Options: `0` No, `1` Yes. |
| `intro_featuredgroups` | Show Featured Groups | list | `1 (Yes)` | Display list of featured groups on group landing page?. Options: `0` No, `1` Yes. |
| `intro_featuredgroups_list` | Featured Group List | text | — | List of Featured Groups to Display |

## Membership & Access

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `join_policy` | Default Join Policy | list | `0 (Public)` | The default default join policy for new groups. Options: `0` Public, `1` Restricted, `2` Invite, `3` Closed. |
| `discoverability` | Default Discoverability | list | `0 (Visible)` | The default discoverability setting for new groups. Options: `0` Visible, `1` Hidden. |
| `display_system_users` | Display System Users? | list | `no (No)` | Display system users in group member lists?. Options: `no` No, `yes` Yes. |
| `invite_message` | Invite template | textarea | — | Provide a template or default message that group managers may use when inviting users to their group. |
| `membership_expiration` | Allow time-limited memberships | list | `0 (No)` | Let group managers give a membership an end date, after which it is revoked automatically. Memberships with no end date are unaffected. Turning this off stops new end dates being set; end dates already set are still honored. Requires the 'Group Membership Expiration' cron job to be enabled. Options: `0` No, `1` Yes. |
| `membership_expiration_warning_days` | Expiration warning days | text | `30,7,1` | Comma-separated list of how many days before the end date to warn the member, e.g. 30,7,1. Enter 0 to send no warnings at all; leaving the field empty restores the default rather than disabling warnings. |
| `membership_expiration_grace_hours` | Grace period (hours) | text | `0` | How long past the end date to wait before revoking. Access is not extended by the grace period on hubs that filter at read time; it only delays the removal. |
| `membership_max_term_days` | Maximum term (days) | text | `0` | Longest end date a group manager may set, in days from today. Zero means no limit. |
| `membership_expiration_batch` | Expiration batch size | text | `500` | How many lapsed memberships to revoke in a single cron run. The remainder is picked up on the next run. |

## Email

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `email_comment_processing` | Enable incoming user discussion comments via email | list | `0 (No)` | Allow user comments sent via email to be added to group forum posts. Options: `0` No, `1` Yes. |
| `email_forum_comments` | Allow outgoing email notifications to users for forum posts | list | `0 (No)` | Allow notification of individual users of forum posts via email. Options: `0` No, `1` Yes. |
| `email_member_groupsidcussionemail_autosignup` | Enable groups to auto setup new members to receive discussion email by default | list | `0 (No)` | Automatically setup new group users to get email from the group discussions. Options: `0` No, `1` Yes. |
| `enable_forum_email_digest` | Allow forum digest? | list | `0 (No)` | Allow digest options for forum emails. Options: `0` No, `1` Yes. |
| `enable_forum_email_categories` | Allow per category forum subscriptions? (forum digest must be disabled) | list | `0 (No)` | COM_GROUPS_CONFIG_EMAIL_FORUM_CATEGORIES_DESC. Options: `0` No, `1` Yes. |

## Upload

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `uploadpath` | Upload path | text | `/site/groups` | File path for pictures |
| `scan_uploads` | Scan Uploads | list | `1 (Yes)` | Scan uploads with ClamAV for virus checking. Options: `0` No, `1` Yes. |

## Pages

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `approvers` | Page Approvers | textarea | — | Users allowed to approve group pages containing php and/or scripts. Comma separated list of usernames. |
| `page_depth` | Max Page Depth | text | `5` | Maxiumum depth level of any given page. |
| `page_comments` | Comments | list | `0 (No)` | Display comments & comment form on group pages. Options: `0` No, `1` Yes. |
| `page_author` | Author Details | list | `0 (No)` | Display author details at the bottom of group pages. Options: `0` No, `1` Yes. |
| `page_modules` | Group Modules | list | `0 (No)` | Allow standard HUB groups to use page modules. Feature meant to be used with only super groups. Options: `0` No, `1` Yes. |

## Super Groups

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `super_components` | Super Group Components | list | `0 (No)` | Allow Super Group Components. Options: `0` No, `1` Yes. |
| `super_group_file_owner` | Super Group Group Owner | text | `access-content` | Filesystem owner of super group folder assets. |
| `super_gitlab` | Repo Management | list | `0 (No)` | Use GitLab to manage super group repositories. Options: `0` No, `1` Yes. |
| `super_gitlab_url` | Repo URL | text | — | URL for repo management application API endpoint (GitLab) |
| `super_gitlab_key` | Repo API Key | text | — | Key to use for authenticating API calls (GitLab) |
