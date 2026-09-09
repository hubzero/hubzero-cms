<!--
status: generated
source: core/components/com_publications/config/config.xml
-->

# Publications (com_publications)

Manage publications

Parameters from [`core/components/com_publications/config/config.xml`](../../../../core/components/com_publications/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `enabled` | Component ON/OFF | list | `0 (Off)` | Enable Publications component, or redirect to Resources component. Options: `0` Off, `1` On. |
| `contribute` | User Contributions Outside of Projects | list | `1 (Allow)` | Allow users to work on drafts from within Publications component without starting a project. Options: `0` Do not allow, `1` Allow. |
| `contribute_default` | Default master type | mastertype | `files` | The master types to offer when 'User Contributions Outside of Projects' is set to 'Allow'. You may choose a specific master type or allow for selecting from a list of all contributable types. |
| `forks` | Allow Forks | list | `0 (No)` | Allow entries to be forked. A fork is a copy of an entry that allows the user to use the source materials as a starting point for a derivation. Options: `0` No, `1` Yes. |
| `email` | Email Notifications | list | `0 (Off)` | Allow component to send out emails. Options: `0` Off, `1` On. |
| `default_category` | Default Category | text | `dataset` | Optionally set the category of newly created publications to the specified value if exists |
| `defaultpic` | Default Publication Thumbnail | text | `/components/com_publications/site/assets/img/resource_thumb.gif` | Default placeholder image for all publications |
| `video_thumb` | Default Video Thumbnail | text | `/components/com_publications/site/assets/img/video_thumb.gif` | Default placeholder thumbnail image for video-type files in publication gallery |
| `gallery_thumb` | Default Image Thumbnail | text | `/components/com_publications/site/assets/img/gallery_thumb.gif` | Default placeholder thumbnail image for image-type files in publication gallery |
| `masterimage` | Default Master Image | text | — | Default image for publication page cover (experimental layout) |
| `webpath` | Publication Files Directory | text | `/site/publications` | Master path for storing published data |
| `bundle_max_bytes` | Bundle Max Size (bytes) | text | `4294967296` | Maximum total source size for on-demand bundle.zip creation. Above this, the bundle download is suppressed and users get individual file downloads. Default 4 GB. Set to 0 to disable the limit. |
| `bundle_async` | Asynchronous bundle building | radio | `0 (No)` | Build download bundles out-of-request in a background worker (download shows 'preparing' until ready) instead of synchronously on the web request. Recommended for sites with large datasets. Requires the 'Build publication download bundles' cron job to be scheduled. Options: `0` No, `1` Yes. |
| `bundle_max_concurrent` | Max concurrent bundle builds | text | `1` | How many bundle builds may run at once when asynchronous building is enabled. Default 1 (serial). |
| `sftppath` | SFTP Directory | text | `/site/publications/ftp` | Path for storing SFTP accessible publication bundle links |
| `sftpsize` | FTP-Enabling File Size | text | `5000` | The file size limit, in MB, that enforces a file can only be downloaded via sFTP. The default is 5000 (5 GB). |
| `ftpdoc` | FTP download guide | text | — | A guide regarding how to download large dataset using ftp client |
| `sftptypeblacklist` | FTP File Type Blacklist | text | — | Comma Separated List of Publication Types that shouldn't/ can't be downloaded via sFTP |
| `documentation` | URL to Documentation | text | — | URL to a page with information on publications and contribution process (leave blank if no such page exists) |
| `deposit_terms` | URL to  Terms of Deposit | text | — | URL to a page with information on Terms of Deposit |
| `include_author_name_in_search` | Include Author Name in Search | list | `0 (No)` | Should publications' authors' names be included when searching?. Options: `0` No, `1` Yes. |
| `contact_email` | Contact Email Address | text | — | The email address that user reaches out to for help |
| `search_category` | Search category | list | `1 (On)` | Search category in tag section of publication submission. Options: `0` Off, `1` On. |
| `data_publishing_self_assessment_guide` | Data Publishing Self Assessment Guide | text | — | A self assessment guide for user to understand what is required in data publishing |
| `accessdoc` | Accessibility document | text | — | File accessibility document |
| `department` | School and department | textarea | — | All schools and departments in the organization. Enter school or department separated by a comma |

## Curation

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `curatorreplyto` | Reply Email Address | text | — | Set the email address that curation emails appear to come from. If left blank, the address will default to the site's global configuration 'From email' address. |
| `curatorgroup` | Name of Curators Group | text | — | (Hub) group of members authorized to perform pre-publication curation (all publication types) and manage curation assignment. |
| `graceperiod` | Grace Period for Changes | list | `0 (None)` | Allow authors to make changes to a published resource within the grace period after approval. Options: `0` None, `1` 1 month. |
| `autoapprove` | Auto-approve | list | `0 (No)` | Automatically approve new submissions. Options: `0` No, `1` Yes. |
| `autoapproved_users` | Auto-approved Users | textarea | — | A comma-separated list of usernames to be auto-approved |

## Doi

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `aboutdoi` | About DOI link | text | — | A link to an explanation of what Digital Object Identifiers (DOI) are [optional] |
| `datacite_ezid_doi_service_switch` | DOI Service | doiservicetype | `1` | Enable DataCite or EZID DOI Service |
| `doi_shoulder` | DOI Namespace Start | text | — | First part of DOI namespace (what goes right after doi: and before /, e.g. 10.5072 ) |
| `doi_prefix` | DOI Namespace End | text | — | Hub-specific DOI namespace end (usually 2-3 characters going after /, e.g. F2K) |
| `datacite_doi_service` | DataCite DOI Service Url | text | — | DataCite DOI service address |
| `datacite_doi_userpw` | DataCite DOI Service User:Password | text | — | DataCite DOI Service User:Password |
| `ezid_doi_service` | EZID DOI Service Url | text | — | EIZD DOI service address |
| `ezid_doi_userpw` | EZID DOI Service User:Password | text | — | EZID DOI Service User:Password |
| `doi_xmlschema` | DOI XML Schema | text | — | URL of XML schema to validate against |
| `doi_publisher` | DOI Publisher | text | — | Publisher name (may use full HUB name) for DOI service |
| `doi_publisher_identifier` | Publisher Identifier | text | — | The publisher identifier on https://www.re3data.org |
| `doi_publisher_identifier_scheme` | Publisher Identifier Scheme | text | — | re3data |
| `doi_publisher_identifier_scheme_uri` | Publisher Identifier Scheme URI | text | — | https://www.re3data.org/ |
| `doi_resolve` | DOI Resolve Url | text | `https://doi.org/` | URL for resolving DOIs |
| `doi_verify` | DOI Verification Url | text | `http://n2t.net/ezid/id/` | URL for verifying DOIs |
| `master_doi` | Issue master DOI for publication? | list | `0 (No)` | Master DOI links to /main page listing all previously published versions. Options: `0` No, `1` Yes. |

## Sections

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_authors` | Contributors | list | `1 (Show)` | Show/Hide the list of authors on publication page. Options: `0` Hide, `1` Show. |
| `format_authors` | Format Author Display | list | `0 (Unformatted)` | Format list of authors on publication page. Options: `0` Unformatted, `1` APA format. |
| `show_ranking` | Ranking | list | `0 (Hide)` | Show/Hide the ranking. Options: `0` Hide, `1` Show. |
| `show_rating` | Rating | list | `0 (Hide)` | Show/Hide the rating. Options: `0` Hide, `1` Show. |
| `show_date` | Date | list | `3 (Published)` | Show/Hide publication date on publication page. Options: `0` Hide, `3` Published. |
| `show_notes` | Notes | list | `1 (Show)` | Show/Hide publication release notes on publication page. Options: `1` Show, `0` Hide. |
| `show_tags` | Tags | list | `1 (Show)` | Show/Hide publication tags on publication page. Options: `1` Show, `0` Hide. |
| `show_series` | Series | list | `1 (Show)` | Show/Hide series that publication belongs to on publication page. Options: `1` Show, `0` Hide. |
| `show_linked_data` | Linked Data | list | `1 (Show)` | Show/Hide a link to an OAI-ORE compliant linked data representation in the HTML source for each publication. Options: `0` Hide, `1` Show. |
| `show_citation` | Citation | list | `1 (Manual And Auto)` | Show example/instructions for citing this publications. Options: `0` Hide All, `1` Manual And Auto, `2` Auto Generated, `3` Manually Inputed. |
| `suggest_licence` | Suggest License | list | `0 (No)` | Allow users to suggest licenses. Options: `1` Yes, `0` No. |
| `citation_format` | Citation Format | list | `apa (APA)` | Choose format for citations. Options: `apa` APA, `ieee` IEEE. |
| `supportedtag` | Supported Tag | text | — | A tag to display to indicate Org supported tools |
| `supportedlink` | Supported Link | text | — | A link to display to describe Org supported tools |
| `audiencelink` | Audience Link | text | — | URL to a page describing audience levels |

## Aip

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `repository` | Trusted Digital Repository | list | `0 (No)` | Trusted Digital Repository. Options: `0` No, `1` Yes. |
| `aip_path` | AIP Path | text | `/srv/AIP` | AIP path (for trusted digital repos) |
| `aip_group` | MkAIP Admin Group | text | — | Group of administrators to get notifications about archived datasets |
