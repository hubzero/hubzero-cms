<!--
status: generated
source: core/components/com_blog/config/config.xml
-->

# Blog (com_blog)

Manage A Blog

Parameters from [`core/components/com_blog/config/config.xml`](../../../../core/components/com_blog/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `title` | Blog Title | text | — | A Title for your blog |
| `uploadpath` | Upload path | text | `/site/blog` | File path for uploads |

## Archive

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_from` | Included Posts From | list | `site (Site Blog)` | Where the posts are pulled from. Options: `site` Site Blog, `member` Member Blog, `group` Group Blog, `both` Site, Member, and Group Blogs. |
| `cleanintro` | Clean Introtext | list | `1 (Yes)` | Strip tags from the introtext or show as is in lists of entries. Options: `0` No, `1` Yes. |
| `introlength` | Intro Length | text | `300` | The length of text the intros should be in lists of entries. |

## Entry

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `show_authors` | Authors | list | `1 (Show)` | Show/Hide the authors of posts. Options: `0` Hide, `1` Show. |
| `allow_comments` | Comments | list | `1 (Allow)` | Allow/Disallow comments on entries. Options: `0` Disallow, `1` Allow. |
| `show_date` | Date | list | `3 (Published)` | Show/Hide the entry date. Options: `0` Hide, `1` Created, `2` Modified, `3` Published. |

## Feeds

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `feeds_enabled` | Feeds | list | `1 (Enabled)` | Enable/Disable RSS feeds. Options: `0` Disabled, `1` Enabled. |
| `feed_entries` | Feed Entries | list | `partial (Partial)` | The length of RSS feed entries. Options: `full` Full, `partial` Partial. |
