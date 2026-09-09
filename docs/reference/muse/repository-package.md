<!--
status: generated
source: core/libraries/Hubzero/Console/Command/Repository/Package.php
-->

# muse repository:package

Repository class.

Implemented in [`Package.php`](../../../core/libraries/Hubzero/Console/Command/Repository/Package.php).

## Running the command alone

`muse repository:package` — Default (required) command - just run check command

## Tasks

### `muse repository:package install`

Installs and/or updates packages required by the composer.lock file

Arguments:

- 3            The installation environment [development|production]
- github-user  The GitHub username to use when configuring the development environment

### `muse repository:package configure`

Configures the package repository for use in a given environment

Arguments:

- github-user  The GitHub username to use when configuring the development environment
