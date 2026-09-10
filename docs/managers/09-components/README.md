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
| [Answers](02-answers.md) | Questions members ask each other, with answers, voting, and rewards. |
| [Blogs](04-blogs.md) | Hub, member, and group blogs. |
| [Forum](17-forum.md) | Discussion sections, categories, and threads. |
| [Knowledge base](19-kb.md) | How-to articles and answers to common questions. |
| [Wiki](39-wiki.md) | Collaboratively edited pages with revision history. |
| [Newsletters](23-newsletters.md) | Email newsletters, their templates, and mailing lists. |
| [Billboards](03-billboards.md) | The rotating banners on the hub's front page. |
| [Feedback](16-feedback.md) | Success stories members submit and the notable quotes drawn from them. |
| [Poll](25-poll.md) | A single site-wide poll. Reachable, but a poll cannot be selected. |

## Research content

| Component | What it does |
|---|---|
| [Resources](29-resources.md) | The hub's catalogue of tools, datasets, presentations, and courses. |
| [Publications](27-publications.md) | Versioned, citable publications and their curation workflow. |
| [Projects](26-projects.md) | Private team workspaces for files, notes, and data. |
| [Citations](08-citations.md) | The bibliography of work about or using the hub. |
| [Collections](09-collections.md) | Member-curated boards of posts, files, and links. |
| [Courses](10-courses.md) | Online courses, their offerings, and their students. |
| [Events](15-events.md) | The hub calendar and event registration. |
| [Tools](36-tools.md) | The tool catalogue and the contribution pipeline. |

## Members and support

| Component | What it does |
|---|---|
| [Support](34-support.md) | Support tickets, their queues, and the reports members file. |
| [Wishlist](40-wishlist.md) | Feature requests members file and vote on. |
| [Storefront](33-storefront.md) and [Cart](06-cart.md) | Products the hub sells and the checkout that sells them. |
| [Tags](35-tags.md) | The hub's tag vocabulary and the pages that browse it. |
| [Jobs](18-jobs.md) | The job board, its listings, and employer subscriptions. |
| [Services](32-services.md) | The subscriptions employers buy to post on the job board. |
| [Messages](22-messages.md) | An administrator-only inbox, separate from member messaging. Not reachable. |
| [Mailto](21-mailto.md) | The send-to-a-friend link on articles. No administrator side. |

## Search, metrics, and maintenance

| Component | What it does |
|---|---|
| [Search](31-search/README.md) | The Solr search index: installing it, filling it, and tuning what it returns. |
| [Usage](37-usage.md) | Traffic and tool-session reports. |
| [Cron](12-cron.md) | Scheduled jobs and the plugins that run them. |
| [Activity](01-activity.md) | The hub-wide activity log and the one chart that reports on it. |
| [What's new](38-whatsnew.md) | Aggregates recent content across six plugins and serves the hub's feeds. |
| [Cache](05-cache.md) | Clearing cached pages, module output, and component fragments. |
| [Check-in](07-checkin.md) | Releasing records left locked by an editor who never saved. |
| [Redirect](28-redirect.md) | Managed redirects, the 404 log, and the external-link interstitial. |
| [Control panel](11-cpanel.md) | The dashboard itself and the panels published to it. |
| [Languages](20-languages.md) | Installed languages, the default, and the string override editor. |

## Integrations and developer tools

| Component | What it does |
|---|---|
| [Developer](14-developer.md) | OAuth applications, API tokens, and the generated API documentation. |
| [SAML](30-saml.md) | The hub acting as an identity provider for external services. |
| [OAI-PMH](24-oaipmh.md) | The metadata endpoint external harvesters index the hub through. |
| [DataViewer](13-dataviewer.md) | Renders a database table as a searchable spreadsheet. |

Members and access groups have their own chapters under
[Users](../06-users/README.md), and the hub's global settings are in
[Configuring](../05-configuring/README.md).
