# The HUBzero® Platform for Scientific Collaboration
[![Documentation](https://img.shields.io/badge/docs-hubzero.github.io-0fa1ca)](https://hubzero.github.io/hubzero-cms/)
[![PHP lint](https://github.com/hubzero/hubzero-cms/actions/workflows/php-lint.yml/badge.svg?branch=2.4-main)](https://github.com/hubzero/hubzero-cms/actions/workflows/php-lint.yml)
[![Tests](https://github.com/hubzero/hubzero-cms/actions/workflows/tests.yml/badge.svg?branch=2.4-main)](https://github.com/hubzero/hubzero-cms/actions/workflows/tests.yml)

## What is HUBzero?

HUBzero is an open source software platform for building powerful Web sites that support scientific discovery, learning, and collaboration. Some refer to such web sites as "collaboratories" supporting "team science." We call them "hubs" because each site becomes a focal point for its user community. The HUBzero platform now supports dozens of hubs across a variety of disciplines, including cancer research, pharmaceuticals, biofuels, microelectromechanical systems, climate modeling, water quality, volcanology, and more.

For more information about HUBzero, visit https://hubzero.org.

## Installation

Instructions for installing the Hubzero platform from packages are in the
[Installation](https://hubzero.github.io/hubzero-cms/installation/) book.

## Documentation

Documentation for hub managers, hub users, tool developers, and CMS developers
is published at https://hubzero.github.io/hubzero-cms/ and written as Markdown
under [`docs/`](docs/README.md) in this repository. See the
[writing guide](docs/STYLE.md) to contribute a page.

## Contributing

Hubzero is an open source project, with code contributions from numerous groups and organizations. If you'd like to contribute, please read the
[Contributing](https://hubzero.github.io/hubzero-cms/contributing/) book.

## Issues

Please report any bugs or issues at https://help.hubzero.org/support.

## Testing

Every push to `2.4-main` runs two GitHub Actions workflows, and the badges at
the top of this page show whether the latest push passed:

- **PHP lint** checks syntax, that global facades are imported where they are
  used, and that the number of undefined language keys does not grow.
- **Tests** runs the PHPUnit suite in `core/`.

A red badge or a failure email after a push means that push broke one of
them; open the run from the badge to see which check failed. Dependabot's
"composer in /core" runs are separate security-update attempts and are not
part of this.

This project is also tested with BrowserStack.
