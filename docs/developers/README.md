<!--
status: rewritten
reviewed: 2026-09-09
-->
# Developers

How to extend the Hubzero CMS: the framework it is built on, and how to
write the components, plugins, modules, and templates that add to a hub.

The CMS is PHP. Its framework lives under `core/libraries/Hubzero/` and
provides the service container, routing, the database layer, events,
sessions, language, and the view layer; extensions live under
`core/components/`, `core/plugins/`, `core/modules/`, and
`core/templates/`, and a hub can override any of them under `app/`.

## Where to start

New to the codebase? Read [Getting started](getting-started/README.md)
for the development environment and the contribution process, then
[Foundation](foundation/README.md) for how a request is served.
Building something? Go straight to the chapter for that kind of
extension.

## In this book

- [Getting started](getting-started/README.md) — development
  environment, browser support, file and database access, release notes.
- [Foundation](foundation/README.md) — structure, constants, facades,
  service providers, and extensions.
- [Services](services/README.md) — cache, events, filesystem, language,
  server, and session.
- [The basics](basics/README.md) — configuration, requests and
  responses, redirects, dates, users, tags, cron, debugging, search.
- [Database](database/README.md) — queries, the ORM, and migrations.
- [Extensions](extensions/README.md) — what every extension shares:
  requirements, parameters, languages, deployment.
- [Modules](modules/README.md), [Components](components/README.md),
  [Plugins](plugins/README.md), and [Templates](templates/README.md) —
  one chapter set per kind of extension: structure, controllers, models,
  views, assets, languages, migrations, packaging.
- [Muse](muse/README.md) — the command-line tool.
- [Super groups](supergroups/README.md) and
  [Super groups with GitLab](supergroups-gitlab/README.md).
- [Testing](testing.md) and [Video tutorials](tutorials.md).

## Reference

The generated [configuration](../reference/configuration/README.md),
[REST API](../reference/api/README.md), [muse](../reference/muse/README.md),
and [events](../reference/events/README.md) references list what the code
declares today. Coding conventions and the contribution process are in the
[Contributing](../contributing/README.md) book.
