# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
 - Joomla! Framework replacements

## [2.1.3] - 2016-02-01
### Added
 - Add ability to archive a group or project moving to a readonly state.
 - Adds Travis CI badge
 [COM_UPDATE] Fixes minor docblock issue.
[COM_UPDATE] Allows webhooks to update the git repo. Meant for QA machines.
[COM_UPDATE] Adds config values for QA git updater.
 - Adding all access values to the list to choose from
- Whitelist for anti-spam plugins.
 
### Changed
 - [CORE] Bump version number.
 - [TRAVIS] Only notify on the change event for success or if build fails.
 - [PURR][#1294] For now, only process the search index queue (cron task) if Solr is configured as search provider.
 - Changing response object returned in accordance with changes to SciStarter API
 - Change import model to be extensible, allowing for greater flexibility (aka adding members to projects on import).
 - Convert Joomla refs to Hubzero equivelants in all components. (bulk find and replace)
 - Make sure nothing uses old Joomla mailer
 - Updating docblock, XML manifest, converting remaining bits of Joomla
- [PURR][#1283] Updates the projects component to reflect changes in Google API libraries


### Fixed
- [PURR][#1243] Fix prevents primary contact check before reverting the publication.
- [PURR][#1290] Fix Solr search Only return non-blocked users.
- Fix the permissions helper to check the permissions set on the administrator side for the cart component.
- [QUBES][#790] Fix do not cloak email addresses in emails sent out through the group announcements plugin.
- [QUBES][#791] Fix correctly displays the senders name in the generated email.
- [NCIP][#1331] Fix a number of issues with saving authors on wiki pages
- [CDMHUB][#1221] Fix do not link to projects group member does not have access to. (since addtion of sub-group projects)
- [NCIP][#1200] Fix publications to allow for optional license for a given master type.
- [HUBZERO][#10298] Fix display 'image not found' within a collection, instead of 500 error.
- [COM_DATAVIEWER][DATACENTERHUB][283] fix dataset download issue ???
- [NANOHUB][#317179][COM_RESOURCES] Adding missing permissions values ???
- [PCH][#194] Allows for a fixed length serial number to be designated in the storefront component.
- [COM_PROJECTS] Fixes invisible files upon upload and alleviates 'ghost' files within the Projects component.
- [QUBES][#784] Force group syncing off if a project doesn't have a group owner
- [CDMHUB][#1211][PLG_PROJECTS_TEAM] Slight reworking to avoid potential conflicts with adding individuals while 'sync group' is checked
- [HUBZERO][#10256] Fixes a variable name typo.
- [PURR][#1198][COM_PROJECTS] Adds a check to see if the initial commit has not been made.
- [PLG_SYSTEM_SUPERGROUPS] Reworkng to get super group component route building working in 2.x
- [CORE] Updating file with 2.0 paths
- [PLG_GROUPS_FORUM] Corrects language file typo.
- [PLG_AUTHENTICATION_SCISTARTER] Fixing API endpoint path and adding more info to description
- [COM_COLLECTIONS] Changing how namespace is used as it can potentially conflict with existing Following Group class
- [PLG_MEMBERS_ACCOUNT] Make sure var is an object before using it as such
- [HUBZERO][#10237][COM_MEMBERS] Adding autocomplete attribute to password fields to try and prevent browsers from auto-filling the fields
- [QUBES][#772][COM_PROJECTS] Make sure HTML formatted content doesn't have line-breaks converted into break tags (to much breaking)
- [PLG_CONTENT_EMAILCLOAK] Fixes misnamed method.
- [CATALYZECARE][#565][COM_MEMBERS] Make sure 'save to new' button works
- [HABRI][#738][COM_TAGS] Purge query cache before deleting record or it may accidentally remove records that should no longer be associated with it
- [PLG_AUTHENTICATION_SCISTARTER] Minor tweaks to URL building and error handling
- [COM_MEMBERS] Try to sanitize bad MS edncoded characters
- [PLG_CONTENT_EMAILCLOAK] Fixing syntac error
- [PLG_CONTENT_*] Removing remaining bits of Joomla
- [PURR][#1279][PLG_SYSTEM_UNCONFIRMED] Prevents the use of stale User activation state.
- [COM_MEMBERS] Allow password to be set when creating a new account via the admin interface.
- [VHUB][#1330][COM_MEMBERS] Removing invalid initiate declaration and automatic method
- [PLG_CONTENT_PAGEBREAK] Removing Joomla-specific code
- [PURR][#1272][PLG_CRON_PUBLICATIONS] Reworking a few spots to be more efficient. Make sure only non-blocked, confirmed, activated accounts get emailed once.
- [PURR][#1273][PLG_PROJECTS_FILES] Make sure text wrap has the correct class name or else javascript can't find a proper hook to latch onto
- [MOD_MYSESSIONS] Fixes the screenshot display.
- [QUBES][#777][PLG_COURSES_DISCUSSIONS] Make sure sections are created with a published state
- [COM_GROUPS][PLG_GROUPS_FILES][TPL_HUBBASIC2013] Adjustments to styles so group files displays a little nicer across templates
- [QUBES][#775][PLG_*_ACTIVITY] Make sure data selector is more specific or else child responses can end up in the top-level list
- [QUBES][#779][MOD_LOGIN] Add required script
- [HUBZERO][#10265][COM_MEMBERS] Make sure header properly matches action of editing ot creating an account
- [HUBZERO][#10272][PLG_MEMBERS_CITATIONS] Adding missing language strings
- [QUBES][#711][COM_GROUPS] Catching more places where 'trusted content' config needs to be inherited before saving
