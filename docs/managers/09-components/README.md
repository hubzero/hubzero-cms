<!--
status: rewritten
reviewed-against: 2.4-main @ ddeb90135f
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/managers/components
-->
# Components

A component is a whole feature of the hub: its own pages, its own database
tables, and its own screens in the administrator interface. Most of what
members see on a hub comes from one component or another, and a hub manager
decides which of them are in use and how each behaves.

Every component in this book is reached from the **Components** menu in the
administrator interface. Each has an **Options** button that opens its
configuration and a **Permissions** tab that says which access groups may
use it; the generated
[configuration reference](../../reference/configuration/README.md) lists
every option of every component in one place.

Components that only support the interface itself, such as the cache,
categories, media, menus, modules, plugins, and template managers, are
covered in [Extensions](../extensions/README.md) and
[Content](../content/README.md) rather than here.

## Content and discussion

| Component | What it does |
|---|---|
| [Answers](answers.md) | Questions members ask each other, with answers, voting, and rewards. |
| [Blogs](blogs.md) | Hub, member, and group blogs. |
| [Forum](forum.md) | Discussion sections, categories, and threads. |
| [Knowledge base](kb.md) | How-to articles and answers to common questions. |
| [Wiki](wiki.md) | Collaboratively edited pages with revision history. |
| [Newsletters](newsletters.md) | Email newsletters, their templates, and mailing lists. |
| [Billboards](billboards.md) | The rotating banners on the hub's front page. |

## Research content

| Component | What it does |
|---|---|
| [Resources](resources.md) | The hub's catalogue of tools, datasets, presentations, and courses. |
| [Publications](publications/README.md) | Versioned, citable publications and their curation workflow. |
| [Projects](projects/README.md) | Private team workspaces for files, notes, and data. |
| [Citations](citations.md) | The bibliography of work about or using the hub. |
| [Collections](collections.md) | Member-curated boards of posts, files, and links. |
| [Courses](courses.md) | Online courses, their offerings, and their students. |
| [Events](events.md) | The hub calendar and event registration. |
| [Tools](tools.md) | The tool catalogue and the contribution pipeline. |

## Members and support

| Component | What it does |
|---|---|
| [Support](support.md) | Support tickets, their queues, and the reports members file. |
| [Wishlist](wishlist.md) | Feature requests members file and vote on. |
| [Storefront](storefront.md) and [Cart](cart.md) | Products the hub sells and the checkout that sells them. |
| [Tags](tags.md) | The hub's tag vocabulary and the pages that browse it. |

## Search, metrics, and maintenance

| Component | What it does |
|---|---|
| [Search](search/README.md) | The Solr search index: installing it, filling it, and tuning what it returns. |
| [Usage](usage.md) | Traffic and tool-session reports. |
| [Cron](cron.md) | Scheduled jobs and the plugins that run them. |

Members and access groups have their own chapters under
[Users](../users/README.md), and the hub's global settings are in
[Configuring](../configuring/README.md).
