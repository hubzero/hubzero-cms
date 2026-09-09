<!--
status: generated
source: core/components/com_citations/config/config.xml
-->

# Citations (com_citations)

Manage citations

Parameters from [`core/components/com_citations/config/config.xml`](../../../../core/components/com_citations/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `citation_single_view` | Citations Single View | list | `0 (No)` | Citations Link to single citation view page?. Options: `0` No, `1` Yes. |
| `default_citation_format` | Default Format | text | — |  |
| `citation_label` | Label | list | `number (Number)` | Displayed to the left of each citation in citations browse mode. Options: `number` Number, `type` Type, `both` Number and Type, `none` None. |
| `citation_rollover` | Show Abstract | list | `no (No)` | Display citation Abstract? Can be overridden per citation. Options: `no` No, `yes` Yes. |
| `citation_sponsors` | Citations Sponsors | list | `yes (Yes)` | Display citation sponsors. Options: `no` No, `yes` Yes. |
| `citation_coins` | Include COinS | list | `1 (Yes)` | Include COinS data in citations. Options: `0` No, `1` Yes. |
| `citation_openurl` | Use Open URL's | list | `1 (Yes)` | Display links to get citation sources through Open URL's. Options: `0` No, `1` Yes. |
| `citation_url` | Citation Link | list | `url (URL)` | The link for the title of the citation. Options: `url` URL, `custom` Custom URL. |
| `citation_custom_url` | Custom Citation URL | text | — | If custom URL is selected above, insert custom URL here. |
| `citation_cited` | Internally Cited Image | list | `0 (No)` | Show Interally Cited Image. Options: `0` No, `1` Yes. |
| `citation_cited_single` | Cited Single Image | text | — | Image path for internally cited resource - Single. |
| `citation_cited_multiple` | Cited Multiple Image | text | — | Image path for internally cited resource - Multiple. |
| `citation_show_tags` | Show Tags? | list | `no (No)` | Show tags related to citation?. Options: `no` No, `yes` Yes. |
| `citation_allow_tags` | Allow Tagging? | list | `no (No)` | Allow tagging on citations?. Options: `no` No, `yes` Yes. |
| `citation_show_badges` | Show Badges? | list | `no (No)` | Show badges that are attached to citations? Badges are basically tags used for labeling purposes only. Options: `no` No, `yes` Yes. |
| `citation_allow_badges` | Allow Badges? | list | `no (No)` | Allow attaching badges to citations? Badges are basically tags used for labeling purposes only. Options: `no` No, `yes` Yes. |

## Import/Export

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `citation_import` | Allow Single Submission | list | `1 (All Users)` | Allow single submission of citations. Options: `0` Not Allowed, `1` All Users, `2` Only Site Admins. |
| `citation_bulk_import` | Allow Bulk Import | list | `1 (All Users)` | Allow Bulk importing of citations. Options: `0` Not Allowed, `1` All Users, `2` Only Site Admins. |
| `citation_download` | Allow Single Export | list | `1 (Yes)` | Allow downloading of single citation. Options: `0` No, `1` Yes. |
| `citation_batch_download` | Allow Bulk Export | list | `1 (Yes)` | Allow bulk downloading of citations. Options: `0` No, `1` Yes. |
| `citation_download_exclude` | Exclude from Export | textarea | — | Fields to exclude from export. |
