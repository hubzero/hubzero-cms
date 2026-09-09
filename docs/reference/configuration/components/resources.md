<!--
status: generated
source: core/components/com_resources/config/config.xml
-->

# Resources (com_resources)

Manage resources

Parameters from [`core/components/com_resources/config/config.xml`](../../../../core/components/com_resources/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `defaultpic` | Default picture | text | `/components/com_resources/site/assets/img/resource_thumb.gif` | Default placeholder image for resource pictures |
| `tagstool` | Tool Fields | text | `screenshots,poweredby,bio,credits,citations,sponsoredby,references,publications` | Default fields for tools type |
| `tagsothr` | Default type fields | text | `bio,credits,citations,sponsoredby,references,publications` | Default fields for resource types |
| `accesses` | Access Levels | text | `Public,Registered,Special,Protected,Private` | Resource access levels |
| `doi` | DOI prefix | text | — | Digital Object Identifier prefix |
| `aboutdoi` | About DOI link | text | — | A link to an explanation of what DOIs are |
| `supportedtag` | Supported Tag | text | — | A tag to display to indicate Org supported tools |
| `supportedlink` | Supported Link | text | — | A link to display to describe Org supported tools |
| `browsetags` | Tag Browser | list | `on (On)` | Use the three column tag browser to find resources. Options: `on` On, `off` Off. |
| `browsetags_defaulttag` | Default Tag in Tag Browser | text | — | Default Tag in Tag Browser |
| `google_id` | Google Analytics ID | text | — | Unique Google Analytics ID (e.g. UA-8810725-2) used to track iTunes/podcast usage. |

## Files

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `webpath` | Resources path | text | `/site/resources` | File path for resources |
| `toolpath` | Tool path | text | `/site/resources/tools` | File path for tools |
| `uploadpath` | Upload path | text | `/site/resources` | File path for attachments |
| `maxAllowed` | Max upload (Bytes)\n 1000000 Bytes = 1 MB | text | `40000000` | Maximum upload file size |
| `file_ext` | Extensions | text | `jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz` | Allowed file types |
| `file_video_html5` | Play Videos in HUB | list | `1 (Yes)` | COM_RESOURCES_EXTENSIONS_DESCRIPTION. Options: `0` No, `1` Yes. |

## Creation

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `email_when_submitted` | Notify Upon Submission | textarea | `{config.mailfrom}` | A comma-separated list of email addresses to notify when new resources are submitted for publishing. |
| `autoapprove` | Auto-approve | radio | `0 (No)` | Automatically approve new submissions. Options: `0` No, `1` Yes. |
| `autoapprove_content_check` | Auto-approve Content Check | radio | `0 (No)` | Checks to make sure auto-approved submissions from the front-end have content before accepting the resource. Options: `0` No, `1` Yes. |
| `autoapproved_users` | Auto-approved Users | textarea | — | A comma-separated list of usernames to be auto-approved |
| `cc_license` | Apply License | radio | `1 (Yes)` | Show an option for marking new contributions with a specific license. Options: `0` No, `1` Yes. |
| `cc_license_custom` | Allow Custom License | radio | `0 (No)` | Show an option for allowing new contributions to have a custom license. Options: `0` No, `1` Yes. |
| `email_when_approved` | Email When Published | radio | `0 (No)` | Email contributors when a submitted resource is approved. Options: `0` No, `1` Yes. |

## Entry

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_authors` | Contributors | list | `1 (Show)` | Show/Hide the list of contributors. Options: `0` Hide, `1` Show. |
| `show_assocs` | Tags | list | `1 (Show)` | Show/Hide the item's tags. Options: `0` Hide, `1` Show. |
| `show_ranking` | Ranking | list | `1 (Show)` | Show/Hide the ranking. Options: `0` Hide, `1` Show. |
| `show_rating` | Rating | list | `1 (Show)` | Show/Hide the rating. Options: `0` Hide, `1` Show. |
| `show_date` | Date | list | `3 (Published)` | Show/Hide the item creation date. Options: `0` Hide, `1` Created, `2` Modified, `3` Published. |
| `show_metadata` | Metadata | list | `1 (Show)` | Show/Hide the metadata for this resource. Options: `0` Hide, `1` Show. |
| `show_citation` | Citation | list | `1 (Manual And Auto)` | Show example/instructions for citing this resource. Options: `0` Hide All, `1` Manual And Auto, `2` Auto Generated, `3` Manually Inputed. |
| `sort_children` | Sort Children | list | `1` | Determine the default sort value for children. Options: `date` Date, `ordering` Ordering, `title` Title, `author` Author. |
| `show_audience` | Audience | list | `0 (Hide)` | Show audience skill level for this resource. Options: `0` Hide, `1` Show. |
| `audiencelink` | Audience Link | text | — | URL to a page describing audience levels |

## Import

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `import_uploadpath` | Upload Path | text | `/site/resources/import` | Path where resource import files are uploaded to. |
