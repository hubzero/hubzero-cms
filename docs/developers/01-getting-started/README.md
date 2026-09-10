<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/index
source-id: 3422
-->
# Getting started

What you need before you write code against Hubzero: a hub to develop
against, a way to reach its files and its database, and a sense of what
changed in the framework you are reading.

The CMS is PHP, and it does not run standalone. You develop against a
working hub, replacing its CMS directory with a checkout of
[hubzero/hubzero-cms](https://github.com/hubzero/hubzero-cms). Everything
under `core/` is the platform; everything under `app/` is that hub's own
state and is not in the repository.

## What you should know

The requirements are modest: PHP, and a text editor. Working knowledge of
the following makes the rest of this book easier:

- HTML and CSS
- JavaScript. The CMS ships [jQuery](https://jquery.com) 3.3.1 in
  `core/assets/js/jquery.js` and most of its own scripts are written
  against it.
- XML, which every extension manifest and configuration file is written in
- The model-view-controller pattern, which components and the admin
  interface follow
- Object-oriented PHP. The framework is namespaced under `Hubzero\` and
  autoloaded by Composer.

The platform targets PHP 8.2; `core/composer.json` pins the platform to
`8.2.30` and the lint workflow runs on 8.3.

## In this section

- [Release notes](releasenotes.md) — where release notes live, and the one
  historic change that still shapes the code you read.
- [Accessing files](fileaccess.md) — reaching a hub's files on the server,
  and reading and writing files from code.
- [Direct database access](databaseaccess.md) — the hub's credentials, the
  shell, and the `#__` table prefix.
- [Upgrade guide](04-upgrade.md) — the legacy class names an older extension
  uses and what replaced them.
- [Browser support](browsersupport.md) — what the code actually targets,
  which is less than you might expect.
- [Development environment](devenvironment.md) — getting a hub to develop
  against, and what this repository does and does not provide.

## Next

[Foundation](../03-foundation/README.md) covers how a request is served and
the names an extension uses constantly. [Contributing](../../contributing/README.md)
covers sending a change back.
