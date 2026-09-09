<!--
status: generated
source: core/components/com_wiki/config/config.xml
-->

# Wiki (com_wiki)

Manage wiki pages

Parameters from [`core/components/com_wiki/config/config.xml`](../../../../core/components/com_wiki/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `subpage_separator` | Subpage Separator | text | `/` | Subpage Separator |
| `homepage` | Default Main Page name | text | `MainPage` | Default Main Page name |
| `max_pagename_length` | Maximum Pagename Length | text | `100` | Maximum Pagename Length |
| `comments` | Comments | radio | `1 (On)` | Allow comments?. Options: `0` Off, `1` On. |
| `comment_ratings` | Comment Ratings | radio | `0 (No)` | Allow ratings on comments?. Options: `0` No, `1` Yes. |
| `automatic_toc` | Automatic table of contents | list | `inline (Inline (top of the page))` | Where to place a table of contents on pages that do not use a [[TableOfContents]] macro, once they reach the heading threshold. A macro on a page always overrides this. Options: `inline` Inline (top of the page), `sidebar` Sidebar, `off` Off. |
| `toc_threshold` | TOC heading threshold | text | `4` | Minimum number of headings a page must have before an automatic table of contents appears. Does not apply to an explicit [[TableOfContents]] macro. |

## Files

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `filepath` | Upload path | text | `/site/wiki` | File path for attachments |
| `mathpath` | Math upload path | text | `/site/wiki/math` | File path for math images |
| `tmppath` | Temp upload path | text | `/site/wiki/tmp` | File path for temp files |

## Cache

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `cache` | Enable Cache | radio | `0 (No)` | Select whether to cache the contents of wiki pages. Options: `0` No, `1` Yes. |
| `cache_time` | Cache Time | text | `15` | The time before the wiki page is recached |
