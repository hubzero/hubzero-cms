<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/conventions
-->
# Conventions

The rules a change has to follow to land in Hubzero. Most of them exist so that
one person can read code another person wrote; a few of them — the naming
rules especially — exist because the autoloader turns a class name into a file
path and gets it wrong when the name is wrong.

Each chapter describes what the code in this repository actually does, with the
counts to show how consistently, and says where core is not uniform so you know
which way to follow.

## In this section

- [PHP Coding Style](01-phpcodingstyles.md) — PSR-12, its three house
  departures, file headers, docblocks, and the checks the build runs.
- [PHP Naming Conventions](02-phpnamingconventions.md) — how a class name
  becomes a file path for components, plugins, modules, templates and
  migrations.
- [CSS Coding Style](03-csscodingstyles.md) — stylesheets and the LESS sources
  the templates compile from.
- [Database Schema Conventions](04-databaseschema.md) — the `#__` prefix
  placeholder, table and column names, indexes.
- [Commit Messages](05-commits.md) — the subject prefix, the body, and what the
  history on this branch actually looks like.

## See also

- [Contributions](../contributions.md) — the process a change goes through.
- [Working on the documentation](../documentation.md) — writing these pages.
- [Developers](../../developers/README.md) — how the pieces the conventions
  govern actually work.
