<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
screenshots: none
-->
# Getting started

What you need before you write code against Hubzero: a hub to develop
against, a way to reach its files and its database, and a sense of what
changed in the framework you are reading.

The CMS is a PHP web application. To try a change in a browser, you need a
running installation with a web server, a database, and configuration. The
repository's [Docker setup](06-devenvironment.md#developing-in-docker)
creates that installation directly from a checkout; you do not need an
existing hub or the separate tool execution platform.

## Your first development session

1. Follow [Developing in Docker](06-devenvironment.md#developing-in-docker)
   to start the hub and sign in to its administrator interface.
2. Read [Application structure](../03-foundation/01-structure.md).
   `core/` holds the shipped code; the installer creates `app/` for this
   hub's configuration, uploads, and local customizations. `app/` is
   Git-ignored, so changes there are not part of a contribution.
3. Choose an [extension type](../index.md#what-are-you-building) for your
   feature, or find the existing extension you want to change. A component
   named `com_blog`, for example, lives under `core/components/com_blog/`.
4. Try the affected page on your local hub. For code checks, start with
   [Running the tests](../15-testing.md#running-them); for sending a change
   back, read [Contributing](../18-contributing.md).

The [development environment](06-devenvironment.md) page also covers a
manual installation and working with a copy of an existing hub.

## What you should know

You need a text editor, Git, and either Docker with Compose or the server
software described in the installation guide. Working knowledge of the
following makes the code chapters easier:

- HTML and CSS
- JavaScript. The CMS ships [jQuery](https://jquery.com) 3.3.1 in
  `core/assets/js/jquery.js` and most of its own scripts are written
  against it.
- XML, which every extension manifest and configuration file is written in
- The model-view-controller (**MVC**) pattern: a controller handles the
  request, a model manages data, and a view renders the result
- Object-oriented PHP. The framework is namespaced under `Hubzero\` and
  autoloaded by Composer.

The documented setup uses PHP 8.2. `core/composer.json` resolves dependencies
as PHP `8.2.30`; that setting does not install PHP. The PHPUnit workflow
runs on 8.2 and the syntax/lint workflow runs on 8.3.

## Reading the code examples

Examples use an illustrative instrument-booking feature, with names such
as `com_booking` and `com_bookings`. These are examples to adapt, not
extensions included in the checkout. A controller excerpt assumes the
component is registered and its models, views, permissions, and language
files exist; a pasted class alone does not install a feature.

These terms recur throughout the developer guide:

| Term | Meaning here |
|---|---|
| Client | The part of the CMS handling a request: the public site, administrator interface, API, or command line |
| Service | A framework object that does shared work, such as translating strings or dispatching events |
| Container | The registry that supplies service objects by name |
| Facade | A short class name such as `Lang` that forwards calls to a service; import it in namespaced PHP files |
| Manifest | An extension's descriptive XML or JSON file, used for metadata and configuration |
| Migration | PHP code that installs or updates database tables and extension records |
| `#__` | A placeholder for the installation's database table prefix; use the real prefix when running SQL directly |

## In this section

- [Release notes](01-releasenotes.md) — where release notes live, and the one
  historic change that still shapes the code you read.
- [Accessing files](02-fileaccess.md) — reaching a hub's files on the server,
  and reading and writing files from code.
- [Direct database access](03-databaseaccess.md) — the hub's credentials, the
  shell, and the `#__` table prefix.
- [Upgrade guide](04-upgrade.md) — the legacy class names an older extension
  uses and what replaced them.
- [Browser support](05-browsersupport.md) — what the code actually targets,
  which is less than you might expect.
- [Development environment](06-devenvironment.md) — getting a hub to develop
  against, and what this repository does and does not provide.

## Next

[Foundation](../03-foundation/index.md) covers how a request is served and
the names an extension uses constantly. [Contributing](../18-contributing.md)
covers sending a change back.
