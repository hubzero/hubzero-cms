<!--
status: rewritten
reviewed-against: 2.4-main @ 68f32bba55
reviewed: 2026-09-10
screenshots: none
-->
# Contributing

Hubzero is an open source project with contributions from many groups and
organizations. This book explains how to work on the code in a way that
lands cleanly: the conventions the codebase follows, how commits are
written, how to run the tests, and how to send a change back.


## Reporting problems

Report bugs and security issues at https://help.hubzero.org/support.

## Contributions

How a change gets from your working copy into Hubzero. The short version: fork
[hubzero/hubzero-cms](https://github.com/hubzero/hubzero-cms), branch from the
release line you are fixing, open a pull request, and say in it what broke and
how you tested the fix.

### The repository

The CMS is one repository, `hubzero/hubzero-cms`, with two top-level trees:

| Tree | What it holds |
|---|---|
| `core/` | The platform: components, plugins, modules, templates, the `Hubzero` framework library, migrations, and the `muse` command-line tool |
| `app/` | A hub's own overrides and configuration. Git-ignored; it is a running hub's local state, not shipped code |

A contribution is a change under `core/`, `docs/`, `tools/` or `.github/`.
Nothing you write under `app/` reaches the repository.

Source files carry an MIT header:

```php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
```

A new file gets the current year. A file you modify keeps the earliest year
already in its header and extends the range:
`Copyright © 2015-2026 Purdue University. All Rights Reserved.` Do not change
the `@license` line. The documentation under `docs/` is
[MIT as well](../LICENSE.md).

Most of core still reads
`Copyright (c) 2005-2020 The Regents of the University of California.`, from an
earlier stewardship of the project. Leave those lines as they are; the change of
holder applies to new work.

The root [`LICENSE`](../../LICENSE) file and `core/composer.json` both say
MIT, and they agree with the header above. They did not until recently: the
root file was the GNU General Public License version 2 and the manifest
declared `GPL-2.0-or-later`, while every source file said MIT. The project
settled on MIT and the two outliers were corrected.

Two files outside `core/vendor/` remain third-party and stay under the GNU
General Public License, because relicensing someone else's work is not the
project's to do. Both are named at the foot of `LICENSE`, and their notices
live in the files themselves:

| File | Origin |
|---|---|
| `core/components/com_wiki/helpers/sanitizer.php` | The MediaWiki XHTML sanitizer, GPL version 2 or later |
| `core/plugins/user/domainrestriction/helpers/IPv6Net.php` | Copyright (c) 2011 Juergen Enge, GPL version 2 |

> **Note:** The sanitizer carries the project's MIT header above the original
> GPL notice. The two contradict each other inside one file. Do not treat that
> file as MIT on the strength of its header.

Everything under `core/vendor/` is installed by Composer and carries whatever
license its package declares.

Third-party code the CMS includes or derives from is listed in
[`ACKNOWLEDGMENTS.md`](../../ACKNOWLEDGMENTS.md), which also lists the
contributors.

### Branches

Each release line has a `X.Y-main` branch: `2.2-main`, `2.3-main`, `2.4-main`.
`2.4-main` is the current line and the repository's default branch. Point
releases are tagged from it.

Branch your work from the release line the fix belongs to, and open the pull
request against that same branch. Give the branch a name that means something
to you; nothing depends on it.

> **Warning:** `.github/CONTRIBUTING.md` still tells you to avoid a `master`
> branch and to work against a stable branch. There is no `master` branch in
> this repository and has not been for several release lines. Use `2.4-main`.

### Before you open a pull request

Install the development dependencies once:

```bash
(cd core && php bin/composer install)
```

Then:

```bash
(cd core && vendor/bin/parallel-lint --exclude vendor .)
(cd core && vendor/bin/phpunit -c phpunit.xml.dist)
php tools/lint/missing-facade-imports.php
```

Read [PHP Coding Style](19-conventions.md#checking-your-work)
for what each of those catches and how to run phpcs usefully against a
codebase that has no committed ruleset.

If you touched anything under `docs/`, rebuild the site and commit the result:

```bash
sh tools/docs/rebuild.sh
```

### What the build checks

Two workflows run on every pull request.

| Workflow | Checks |
|---|---|
| [`php-lint.yml`](../../.github/workflows/php-lint.yml) | `php -l` on every `*.php` under `core` and `app` outside `vendor`, then `tools/lint/missing-facade-imports.php` |
| [`pages.yml`](../../.github/workflows/pages.yml) | Runs the documentation builder's tests, regenerates `docs/reference` and fails if it differs, builds the site, checks every internal link, and fails if the committed `gh-pages/public` is stale |

The facade check is worth understanding before it fails on you. The CMS
registers `Route`, `User`, `Lang` and the rest as root-namespace aliases, so an
unqualified `Route::url()` inside a namespaced file resolves to
`Current\Namespace\Route` and fatals when that line runs. The file parses, so
`php -l` says nothing, and the fault only surfaces on the path that reaches it.
730 of these were fixed at once; the linter keeps them from coming back. Run it
with `--fix` to insert the missing `use` statements.

Neither workflow runs phpcs. Style is caught in review.

### The pull request

The [pull request template](../../.github/pull_request_template.md) asks for:

- links to the issue or ticket the change answers
- a summary of the problem in your own words
- a summary of what the change does
- **what you specifically did to test it** — the part reviewers most often have
  to ask for
- whether the change needs to reach a production hub before the next core
  rollout
- a named reviewer

Every pull request needs a review before it merges;
[`CODEOWNERS`](../../.github/CODEOWNERS) requests one automatically.

Write the commit messages the way
[Commit Messages](19-conventions.md#commit-messages) describes: an extension prefix, a
sentence, and a body that says why.

### What makes a change acceptable

Beyond the [conventions](19-conventions.md):

- **It works, and you say how you know.** A description of what you tested is
  worth more than an assertion that you did.
- **It is in English.** Variables, functions, and comments. Code the
  maintainers cannot read is code they cannot maintain.
- **It is finished.** If the feature needs language strings, a migration, or a
  documentation page, they come with it, not later.
- **It is one thing.** A behaviour change and a reformatting pass are two
  commits, and often two pull requests. A reviewer cannot see one through the
  other.
- **It does not widen access by accident.** A change to a permission check, an
  ACL rule, or a query's `WHERE` clause needs to say what it now allows that it
  did not before.
- **It is portable.** Relative paths, configuration values, and hostnames — no
  absolute paths from your machine and no IP addresses.

Before proposing a feature rather than a fix, consider:

1. Who is it useful to, and can it be switched off by everyone else?
2. Is the target audience a hub visitor, a hub manager, or a system
   administrator, and is the interface right for them?
3. What does it cost to maintain?
4. Does something similar already exist that could be extended instead?

### Reporting problems

- **Bugs and feature requests**: https://help.hubzero.org/support, or a GitHub
  issue using the [bug report template](../../.github/ISSUE_TEMPLATE/bug_report.md),
  which asks for the Hubzero version, the PHP version, the operating system,
  and steps to reproduce.
- **Security issues**: email support@hubzero.org. Do not open a public issue.
  See [`SECURITY.md`](../../.github/SECURITY.md).

Some faults come from a Composer package rather than from Hubzero. When that
happens the maintainers will usually work around it here and point you at the
package's own tracker.

### A development environment

You need a working hub to develop against; the CMS does not run standalone.
[Development Environment](../developers/01-getting-started/06-devenvironment.md)
covers getting one. Installing a hub from scratch is being rewritten for
the new web installer; see [Installation](../installation/README.md). The
platform targets PHP 8.2.

Once the hub runs, replace its CMS directory with your clone, restore the
`app/` directory from the original, install the Composer dependencies, and run
the migrations:

```bash
(cd core && php bin/composer install)
php core/bin/muse migration -f
```

`muse migration` runs in dry-run mode by default and lists what it would do;
`-f` is the full run. Older instructions pair `-f` with `-i`. That flag is
deprecated: it now behaves as `-a`, which only widens what is listed.

[muse](../developers/12-muse.md) is the CMS command line; it does a
great deal more than migrations.

> **Tip:** Break your development environment freely. Snapshot the virtual
> machine first and roll back when you have learned what you needed to.

## Working on the documentation

The documentation is Markdown under `docs/` in the hubzero-cms repository,
built into the site you are reading by the scripts under `gh-pages/`. It is
edited the same way as the code: on a branch, in a pull request, with the
site rebuilt and checked before it merges.

### Editing a page

Every page has an **Edit this page on GitHub** link in its footer. For
anything larger than a typo, clone the repository and work locally:

```bash
python3 -m pip install -r gh-pages/requirements.txt -r tools/docs/requirements.txt pytest
sh tools/docs/rebuild.sh
python3 -m http.server -d gh-pages/public 8000
```

Then open http://localhost:8000/. Rebuild after each change; the build
takes a few seconds.

The [writing guide](../STYLE.md) covers file naming, the metadata header,
links, code blocks, callouts, and house style.

### Reviewing an imported page

Most pages were imported from help.hubzero.org and carry a banner until
someone checks them against the code. To review one:

1. Read the page against the current code on `2.4-main`: the controllers,
   views, and `config.xml` of the component it describes. Fix or rewrite
   what has drifted. Replace pasted code with an include directive that
   pulls the real file.
2. Regenerate any screenshots from a current hub.
3. Change the header to `status: reviewed`, add `reviewed-against` with the
   branch and commit you checked, and `reviewed` with the date.
4. Rebuild, commit `gh-pages/public/` with the change, and open a pull
   request.

The [status page](https://hubzero.github.io/hubzero-cms/status/) shows what
is left in each book.

### Committing

Commit `gh-pages/public/` together with the source change. The Pages
workflow rebuilds the site from source on every push and fails if the
committed copy does not match, so a stale copy cannot be deployed by
accident.
