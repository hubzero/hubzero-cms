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

The extension managers themselves — modules, plugins and templates — are
covered in [Extensions](../10-extensions/README.md), and the article, category
and media managers in [Content](../08-content/README.md), rather than here.

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
| [Feedback](feedback.md) | Success stories members submit and the notable quotes drawn from them. |
| [Poll](poll.md) | A single site-wide poll. Reachable, but a poll cannot be selected. |

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
| [Jobs](jobs.md) | The job board, its listings, and employer subscriptions. |
| [Services](services.md) | The subscriptions employers buy to post on the job board. |
| [Messages](messages.md) | An administrator-only inbox, separate from member messaging. Not reachable. |
| [Mailto](mailto.md) | The send-to-a-friend link on articles. No administrator side. |

## Search, metrics, and maintenance

| Component | What it does |
|---|---|
| [Search](search/README.md) | The Solr search index: installing it, filling it, and tuning what it returns. |
| [Usage](usage.md) | Traffic and tool-session reports. |
| [Cron](cron.md) | Scheduled jobs and the plugins that run them. |
| [Activity](activity.md) | The hub-wide activity log and the one chart that reports on it. |
| [What's new](whatsnew.md) | Aggregates recent content across six plugins and serves the hub's feeds. |
| [Cache](cache.md) | Clearing cached pages, module output, and component fragments. |
| [Check-in](checkin.md) | Releasing records left locked by an editor who never saved. |
| [Redirect](redirect.md) | Managed redirects, the 404 log, and the external-link interstitial. |
| [Control panel](cpanel.md) | The dashboard itself and the panels published to it. |
| [Languages](languages.md) | Installed languages, the default, and the string override editor. |

## Integrations and developer tools

| Component | What it does |
|---|---|
| [Developer](developer.md) | OAuth applications, API tokens, and the generated API documentation. |
| [SAML](saml.md) | The hub acting as an identity provider for external services. |
| [OAI-PMH](oaipmh.md) | The metadata endpoint external harvesters index the hub through. |
| [DataViewer](dataviewer.md) | Renders a database table as a searchable spreadsheet. |

Members and access groups have their own chapters under
[Users](../06-users/README.md), and the hub's global settings are in
[Configuring](../05-configuring/README.md).
