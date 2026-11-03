<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
-->
# Developers

How to extend the Hubzero CMS: the framework it is built on, and how to
write the components, plugins, modules, and templates that add to a hub.

The CMS is PHP. Its framework lives under `core/libraries/Hubzero/` and
provides the service container, routing, the database layer, events,
sessions, language, and the view layer; extensions live under
`core/components/`, `core/plugins/`, `core/modules/`, and
`core/templates/`, and a hub can override any of them under `app/`.

## What are you building?

Most people arrive here to add one thing to one hub. Find it below and start
there; the chapters assume you will read one of them, not all of them.

| You want to | Write a | Start at |
|---|---|---|
| A section of the hub with its own pages, URLs, database tables and administration screens — a booking system, a catalogue, an instrument log | **component** | [Components](09-components/README.md) |
| Behaviour that hangs off something that already exists — react to a save, add a tab to a group, add a login method, push records to an external service | **plugin** | [Plugins](10-plugins/README.md) |
| A small block of content placed in a template position — a list, a counter, a search box | **module** | [Modules](08-modules/README.md) |
| A different look: an institution's brand, a redesigned page frame, a stylesheet override | **template** | [Templates](11-templates/README.md) |
| A command run from the shell or a timer — an import, a nightly job, a repair task | **muse command** | [Muse](12-muse.md#when-to-write-a-command) |
| A machine-readable endpoint for an external client | **API controller** | [The REST API](16-api.md) |

Whichever it is, three chapters apply to all of them:

- [Extensions](07-extensions/README.md) — what every extension shares: the
  manifest, parameters, language files, and how it gets deployed.
- [Database](06-database.md) — the query builder, the ORM, and the
  [migration](06-database.md#migrations) that creates your tables. Read the
  [table prefix](06-database.md#the-table-prefix) section before you write a
  query; it is the mistake that works on your hub and fails on everyone
  else's.
- [Conventions](19-conventions.md) — the style the tree is written in, and
  what a commit message looks like.

## Before you write anything

New to the codebase, read these in order:

1. [Getting started](01-getting-started/README.md) — getting a hub running to
   develop against.
2. [Foundation](03-foundation/README.md) — how a request is served, and how
   an extension is found and dispatched.
3. [Services](04-services/README.md) and [The basics](05-basics/README.md) —
   the facades you will use in every file: `Config`, `Request`, `Lang`,
   `User`, `Event`.

## In this book

- [Getting started](01-getting-started/README.md) — development
  environment, browser support, file and database access, release notes.
- [Foundation](03-foundation/README.md) — the tree, the path constants, how a
  class name becomes a file, the event system, the four extension kinds, the
  facades, and the service providers.
- [Services](04-services/README.md) — cache, events, filesystem, language,
  server, and session.
- [The basics](05-basics/README.md) — configuration, requests and
  responses, redirects, dates, users, tags, cron, debugging, search.
- [Database](06-database.md) — queries, the ORM, and migrations.
- [Extensions](07-extensions/README.md) — what every extension shares:
  requirements, parameters, languages, deployment.
- [Modules](08-modules/README.md), [Components](09-components/README.md),
  [Plugins](10-plugins/README.md), and [Templates](11-templates/README.md) —
  one chapter set per kind of extension: structure, controllers, models,
  views, assets, languages, migrations, packaging.
- [Muse](12-muse.md) — the command-line tool.
- [Super groups](13-supergroups/README.md) and
  [Super groups with GitLab](14-supergroups-gitlab.md).
- [Testing](15-testing.md) — the test suite, the linters, and what CI runs.
- [The REST API](16-api.md) — the API client, versioned controllers, and
  the docblock tags the endpoint reference is generated from.
- [Running on AWS](17-aws.md) — what in this repository is specific to it,
  which is very little.
- [Video tutorials](02-tutorials.md).
- [Contributing](18-contributing.md) — sending a change back, and working
  on these pages.
- [Conventions](19-conventions.md) — PHP style and naming, CSS, the
  database schema, and commit messages.

## Reference

The generated [configuration](../reference/configuration/README.md),
[REST API](../reference/api/README.md), [muse](../reference/muse.md),
and [events](../reference/events/README.md) references list what the code
declares today. They are produced from the source tree, so they say what the
code says rather than what anyone remembers writing.

> **Note:** The muse reference covers the framework's own commands only.
> Commands that ship inside a component are not in it — see
> [Component commands](12-muse.md#component-commands).
