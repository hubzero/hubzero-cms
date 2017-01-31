# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.1.3] - 2016-02-01
### Added
 - Add Travis CI badge
 - Add ability to update the git respository from github webhook.
 - Add all access values to the list to choose from when creating an account.
 - Add whitelist for anti-spam plugins.
 - [HUBZERO][#10237] Add autocomplete attribute to password fields to try and prevent browsers from auto-filling the fields.
 - Allow password to be set when creating a new account via the admin interface.
 - Add ability to archive a group or project moving to a readonly state.
 
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
 - [PLG_CONTENT_*] Removing remaining bits of Joomla


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
- [QUBES][#784] Fixes project membership management issue, allows for the removal of a member.
- [CDMHUB][#1211] Slight reworking to avoid potential conflicts with adding individuals while 'sync group' is checked 
- [HUBZERO][#10256] Fixes name link in forum post.
- [PURR][#1198][COM_PROJECTS] Enforces project initial repository setup.
- Fixes super group component route building since 2.x upgrade
- Fixes 'red' to 'read' in group forum area description.
- Fixes SciStarter API endpoint path and adding more info to description
- Fixes how namespace conflict in Collections
- Fixes object access error in Member Accounts.
- [QUBES][#772][COM_PROJECTS] Fixes HTML formatting issues concerning extraneous break tags being inserted.
- Fixes misnamed method in the Content - EmailCloak plugin.
- [CATALYZECARE][#565] Fixes sure 'save to new' button.
- [HABRI][#738] Fixes caching issue with tag record upon deletion to prevent dangling objects.
- Fixes poorly MS-encoded characters in the Members component.
- [PURR][#1279] Fixes account confirmation loop due to cached User session data.
- [VHUB][#1330] Fixes 'unknown column' error when adding a new Quota class.
- [PURR][#1272] Make sure only non-blocked, confirmed, activated accounts get emailed once about publication statistics.
- [PURR][#1273][PLG_PROJECTS_FILES] Fixes folder expansion in Project Files area.
- Fixes the screenshot display on the member dashboard.
- [QUBES][#777] Make sure course discussion sections are created with a published state
- Fixes styles so group files displays a little nicer across templates
- [QUBES][#775] Fixes nesting of actvity feed posts.
- [QUBES][#779] Fixes missing javascript file for login within super groups.
- [HUBZERO][#10265] Fixes administrative header properly to match action of editing of creating an account
- [HUBZERO][#10272] Fixes missing language strings 'publish / unpublish' on member citations.
- [QUBES][#711] Fixes inheritence 'trusted content' configuration on group pages.
