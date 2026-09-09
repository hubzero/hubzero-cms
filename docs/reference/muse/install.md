<!--
status: generated
source: core/libraries/Hubzero/Console/Command/Install.php
-->

# muse install

Install  - fresh HUBzero installation.

Implemented in [`Install.php`](../../../core/libraries/Hubzero/Console/Command/Install.php).

## Running the command alone

`muse install` — Run the full HUBzero installation process

## Tasks

### `muse install vendor`

Install composer dependencies only

### `muse install check`

Run pre-flight checks without installing

### `muse install appdir`

Create the app directory structure only

### `muse install database`

Configure database connection only

### `muse install schema`

Load database schema and essential data

### `muse install settings`

Configure site settings and generate config files

### `muse install sample`

Load sample/demo data into database

### `muse install migrations`

Run database migrations to update schema

### `muse install admin`

Create the initial admin user account
