<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
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

## Which of these does your hub actually use?

Forty components ship with a hub. Almost no hub uses forty. A typical hub
runs its resources or publications catalogue, groups, projects, support, and
perhaps three or four others; the rest sit installed, empty, and out of the
way. If you have inherited a hub, work out which are live before you read
any further, because most of this section will not apply to you.

Three screens answer it, and they answer different questions.

- **The Components menu** lists a component only when it is enabled *and*
  you hold its manage permission. What you see there is what you can
  administer, not what your hub has installed.
- **Extensions > Extension Manager**, on its **Hubzero Core** tab with the
  **Type** filter set to Component, lists every component the platform ships,
  enabled or not. This is the full inventory.
- **Menus > Main Menu** shows what a visitor is actually offered. A
  component with no menu item can still be reached by typing its URL, so an
  empty menu is not proof a feature is off.

The quickest read on whether a component is *used* rather than merely
enabled is to open it and look at its list. An empty Wiki Pages list or an
empty Questions list means nobody has ever used it, whatever the menus say.

### Turning one off

Tick the component in **Extensions > Extension Manager** and select
**Disable**. Every one of its site URLs then returns a not-found page and it
drops out of the Components menu. Nothing is deleted: the tables, the rows,
the uploaded files all stay, and selecting **Enable** puts the feature back
exactly as it was. Disabling is the whole of the lever — the components that
ship with the platform cannot be uninstalled from the administrator
interface at all, and there is no **Uninstall** button on that screen. See
[Extension Manager](../10-extensions/04-extension-manager.md).

> **Warning:** The change is immediate and hub-wide. Disabling a component
> people are using breaks their bookmarks at once and with no redirect, and
> takes any menu items pointing at it to a dead end. It is reversible, so the
> damage is measured in hours rather than permanently — but do it out of
> hours and tell people first.

## Choosing between the overlapping ones

Five of the components below do jobs that look alike, and a manager setting
up a hub is asked to choose between them with nothing to go on. A hub does
not need a forum *and* a question board *and* a knowledge base *and* a wiki.
Run one or two. Every extra one splits the same small number of posts
across more places, and a question asked in the wrong one gets no answer.

| If members need to … | Use |
|---|---|
| Ask a specific question and have one reply marked as the answer | [Answers](02-answers.md) |
| Hold a conversation that has no single answer, organised by topic | [Forum](17-forum.md) |
| Read a staff-written explanation that stays put and stays correct | [Knowledge base](19-kb.md) |
| Maintain a document together, with a history of who changed what | [Wiki](39-wiki.md) |
| Follow dated announcements from the hub, a member, or a group | [Blogs](04-blogs.md) |

Two combinations earn their keep. **Answers plus the knowledge base** is the
common one: members ask, and when the same question comes up a third time
you write the settled version as an article and close the loop — the
Answers component's **About points** option even defaults to a knowledge
base address. **Forum plus wiki inside groups** is the other: the group
argues in the forum and records the conclusion on a wiki page. Each of the
five chapters opens by saying what its component is good at and where a
sibling beats it.

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

The five overlapping ones are compared above under
[Choosing between the overlapping ones](#choosing-between-the-overlapping-ones).
The other four in this group do not overlap with anything: newsletters push
mail out, billboards fill the front page, feedback gathers testimonials, and
poll asks one question.

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
