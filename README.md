# Hubzero
[![Documentation](https://img.shields.io/badge/docs-hubzero.github.io-0fa1ca)](https://hubzero.github.io/hubzero-cms/)
[![PHP lint](https://github.com/hubzero/hubzero-cms/actions/workflows/php-lint.yml/badge.svg?branch=2.4-main)](https://github.com/hubzero/hubzero-cms/actions/workflows/php-lint.yml)
[![Tests](https://github.com/hubzero/hubzero-cms/actions/workflows/tests.yml/badge.svg?branch=2.4-main)](https://github.com/hubzero/hubzero-cms/actions/workflows/tests.yml)

Hubzero is an open source platform for websites that support scientific
research, learning and collaboration. Each site is a hub: a focal point for
its community, with resources, groups, projects, publications and simulation
tools that run in the browser. More at https://hubzero.org.

This repository is the Hubzero CMS. `2.4-main` is the 2.4 release branch.

## Documentation

The documentation is published at https://hubzero.github.io/hubzero-cms/,
for system administrators, hub managers, hub users, tool developers and CMS
developers. It is written as Markdown under [`docs/`](docs/index.md); the
[writing guide](docs/STYLE.md) explains how to add to it.

- **Installing a hub:** [System administrators](https://hubzero.github.io/hubzero-cms/sysadmins/)
- **Developing:** `docker compose up -d` in a checkout starts a development
  hub; see [Development environment](https://hubzero.github.io/hubzero-cms/developers/getting-started/devenvironment/)
- **Contributing:** [Contributing](https://hubzero.github.io/hubzero-cms/developers/contributing/)

## Reporting a problem

Report bugs and other issues as
[GitHub issues](https://github.com/hubzero/hubzero-cms/issues). Report
security problems privately, as [`SECURITY.md`](.github/SECURITY.md)
describes.

## Checks

The badges show the latest run on `2.4-main` of the two workflows that check
the code: **PHP lint** (syntax, facade imports, and a ceiling on undefined
language keys) and **Tests** (the PHPUnit suite in `core/`). A failure email
after a push means that push broke one of them; the run, opened from the
badge, says which check. Dependabot's "composer in /core" runs are separate.

## License

MIT; see [`LICENSE`](LICENSE).
