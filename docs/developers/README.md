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

New to the codebase? Read [Getting started](01-getting-started/README.md)
for the development environment and the contribution process, then
[Foundation](03-foundation/README.md) for how a request is served.
Building something? Go straight to the chapter for that kind of
extension.

## In this book

- [Getting started](01-getting-started/README.md) — development
  environment, browser support, file and database access, release notes.
- [Foundation](03-foundation/README.md) — structure, constants, facades,
  service providers, and extensions.
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

## Reference

The generated [configuration](../reference/configuration/README.md),
[REST API](../reference/api/README.md), [muse](../reference/muse.md),
and [events](../reference/events/README.md) references list what the code
declares today. Coding conventions and the contribution process are in the
[Contributing](../contributing/README.md) book.
