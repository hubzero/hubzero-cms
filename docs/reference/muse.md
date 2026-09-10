<!--
status: generated
source: core/libraries/Hubzero/Console/Command/
-->

# Muse console reference

`muse` is the Hubzero command-line tool, at `core/bin/muse`. Run it from the hub's root directory as a user that can read the configuration. `muse help` lists the commands; `muse <command> help` describes one. This page is generated from the command classes' docblocks, so it says what the code says rather than what the built-in help prints.

| Command | Tasks | Purpose |
|---|---|---|
| [`muse app:package`](#muse-app-package) | 5 | Repository class for adding and removing composer packages |
| [`muse app:repository`](#muse-app-repository) | 3 | Repository class for adding and removing composer package repositories |
| [`muse app`](#muse-app) | 0 | Migration class |
| [`muse cache:css`](#muse-cache-css) | 1 | Cache |
| [`muse cache`](#muse-cache) | 1 | Cache |
| [`muse configuration:aliases`](#muse-configuration-aliases) | 1 | Aliases configuration class for adding command aliases |
| [`muse configuration:hooks`](#muse-configuration-hooks) | 1 | Hooks configuration class for adding a new hook |
| [`muse configuration`](#muse-configuration) | 1 | Help class for rendering utility-wide help documentation |
| [`muse database`](#muse-database) | 2 | Database class |
| [`muse environment`](#muse-environment) | 0 | Environment class |
| [`muse extension`](#muse-extension) | 5 | Extension class |
| [`muse group`](#muse-group) | 3 | Group |
| [`muse help`](#muse-help) | 0 | Help class for rendering utility-wide help documentation |
| [`muse htmx`](#muse-htmx) | 2 | HTMX developer tooling command. |
| [`muse inertia`](#muse-inertia) | 2 | Inertia developer tooling command. |
| [`muse install`](#muse-install) | 9 | Install  - fresh HUBzero installation |
| [`muse log:post`](#muse-log-post) | 0 | Post log class |
| [`muse log:profile`](#muse-log-profile) | 0 | Profile log class |
| [`muse log:sql`](#muse-log-sql) | 0 | Sql log class |
| [`muse log`](#muse-log) | 1 | Log class |
| [`muse migration`](#muse-migration) | 2 | Migration class |
| [`muse repository:flavor`](#muse-repository-flavor) | 1 | Repository flavor class |
| [`muse repository:package`](#muse-repository-package) | 2 | Repository class |
| [`muse repository`](#muse-repository) | 12 | Repository class |
| [`muse resources`](#muse-resources) | 3 | Resources |
| [`muse scaffolding`](#muse-scaffolding) | 2 | Scaffolding class for generating template extensions |
| [`muse searchmigration`](#muse-searchmigration) | 1 | Migration class |
| [`muse test`](#muse-test) | 2 | Test class |
| [`muse user:terms`](#muse-user-terms) | 1 | User class for terms of use functions |
| [`muse user`](#muse-user) | 4 | User class for general user functions |

## `muse app:package`

Repository class for adding and removing composer packages.

Implemented in [`Package.php`](../../core/libraries/Hubzero/Console/Command/App/Package.php).

Run alone, `muse app:package` default (required) command - just call help

### `muse app:package show`

Shows a list of active packages

### `muse app:package available`

Shows a list of available remote packages

### `muse app:package install`

Installs a package

### `muse app:package update`

Updates a package according to version constraints

### `muse app:package remove`

Removes a package

## `muse app:repository`

Repository class for adding and removing composer package repositories.

Implemented in [`Repository.php`](../../core/libraries/Hubzero/Console/Command/App/Repository.php).

Run alone, `muse app:repository` default (required) command - just call help

### `muse app:repository show`

Shows a list of active repositories

### `muse app:repository add`

Adds a repository

### `muse app:repository remove`

Removes a repository

## `muse app`

Migration class.

Implemented in [`App.php`](../../core/libraries/Hubzero/Console/Command/App.php).

Run alone, `muse app` default (required) command - just executes run

## `muse cache:css`

Cache.

Implemented in [`Css.php`](../../core/libraries/Hubzero/Console/Command/Cache/Css.php).

Run alone, `muse cache:css` default (required) command - just executes run

### `muse cache:css clear`

No description available.

## `muse cache`

Cache.

Implemented in [`Cache.php`](../../core/libraries/Hubzero/Console/Command/Cache.php).

Run alone, `muse cache` default (required) command - just executes run

### `muse cache clear`

Clears all cached items in document root cache directory

## `muse configuration:aliases`

Aliases configuration class for adding command aliases.

Implemented in [`Aliases.php`](../../core/libraries/Hubzero/Console/Command/Configuration/Aliases.php).

Run alone, `muse configuration:aliases` default (required) command - just call help

### `muse configuration:aliases add`

No description available.

## `muse configuration:hooks`

Hooks configuration class for adding a new hook.

Implemented in [`Hooks.php`](../../core/libraries/Hubzero/Console/Command/Configuration/Hooks.php).

Run alone, `muse configuration:hooks` default (required) command - just call help

### `muse configuration:hooks add`

No description available.

## `muse configuration`

Help class for rendering utility-wide help documentation.

Implemented in [`Configuration.php`](../../core/libraries/Hubzero/Console/Command/Configuration.php).

Run alone, `muse configuration` default (required) command

### `muse configuration set`

Sets the defined key/value pair and saves it into the user's configuration

## `muse database`

Database class.

Implemented in [`Database.php`](../../core/libraries/Hubzero/Console/Command/Database.php).

Run alone, `muse database` default (required) command - just executes run

### `muse database dump`

Dumps the current site database into a file in the users home directory

### `muse database load`

Loads the provided database into the hubs currently configured database

## `muse environment`

Environment class.

Implemented in [`Environment.php`](../../core/libraries/Hubzero/Console/Command/Environment.php).

Run alone, `muse environment` default (required) command

## `muse extension`

Extension class.

Implemented in [`Extension.php`](../../core/libraries/Hubzero/Console/Command/Extension.php).

Run alone, `muse extension` default (required) command - just executes run

### `muse extension add`

Adds a new extension to the extensions table

### `muse extension delete`

Deletes an existing entry from the extensions table

### `muse extension install`

Installs an extension, adding it if it hasn't been already

### `muse extension enable`

Enables an existing extension

### `muse extension disable`

Disables an existing extension

## `muse group`

Group.

Implemented in [`Group.php`](../../core/libraries/Hubzero/Console/Command/Group.php).

Run alone, `muse group` default (required) command - just executes run

### `muse group scaffolding`

No description available.

### `muse group migrate`

No description available.

### `muse group update`

No description available.

## `muse help`

Help class for rendering utility-wide help documentation.

Implemented in [`Help.php`](../../core/libraries/Hubzero/Console/Command/Help.php).

Run alone, `muse help` default (required) command

## `muse htmx`

HTMX developer tooling command.

Implemented in [`Htmx.php`](../../core/libraries/Hubzero/Console/Command/Htmx.php).

### `muse htmx scaffold`

No description available.

### `muse htmx lint`

No description available.

## `muse inertia`

Inertia developer tooling command.

Implemented in [`Inertia.php`](../../core/libraries/Hubzero/Console/Command/Inertia.php).

### `muse inertia scaffold`

No description available.

### `muse inertia lint`

No description available.

## `muse install`

Install  - fresh HUBzero installation.

Implemented in [`Install.php`](../../core/libraries/Hubzero/Console/Command/Install.php).

Run alone, `muse install` run the full HUBzero installation process

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

## `muse log:post`

Post log class.

Implemented in [`Post.php`](../../core/libraries/Hubzero/Console/Command/Log/Post.php).

No documented tasks; run `muse log:post help` for its built-in help.

## `muse log:profile`

Profile log class.

Implemented in [`Profile.php`](../../core/libraries/Hubzero/Console/Command/Log/Profile.php).

No documented tasks; run `muse log:profile help` for its built-in help.

## `muse log:sql`

Sql log class.

Implemented in [`Sql.php`](../../core/libraries/Hubzero/Console/Command/Log/Sql.php).

No documented tasks; run `muse log:sql help` for its built-in help.

## `muse log`

Log class.

Implemented in [`Log.php`](../../core/libraries/Hubzero/Console/Command/Log.php).

Run alone, `muse log` default (required) command

### `muse log follow`

No description available.

## `muse migration`

Migration class.

Implemented in [`Migration.php`](../../core/libraries/Hubzero/Console/Command/Migration.php).

Run alone, `muse migration` default (required) command - just executes run

### `muse migration run`

Runs pending migrations according to options provided

### `muse migration history`

Shows a history of previously run migrations

## `muse repository:flavor`

Repository flavor class.

Implemented in [`Flavor.php`](../../core/libraries/Hubzero/Console/Command/Repository/Flavor.php).

Run alone, `muse repository:flavor` default (required) command

### `muse repository:flavor set`

No description available.

## `muse repository:package`

Repository class.

Implemented in [`Package.php`](../../core/libraries/Hubzero/Console/Command/Repository/Package.php).

Run alone, `muse repository:package` default (required) command - just run check command

### `muse repository:package install`

Installs and/or updates packages required by the composer.lock file

Arguments:

- 3            The installation environment [development|production]
- github-user  The GitHub username to use when configuring the development environment

### `muse repository:package configure`

Configures the package repository for use in a given environment

Arguments:

- github-user  The GitHub username to use when configuring the development environment

## `muse repository`

Repository class.

Implemented in [`Repository.php`](../../core/libraries/Hubzero/Console/Command/Repository.php).

Run alone, `muse repository` default (required) command - just run check command

### `muse repository status`

Checks the current status of the repository for upgrade eligibility

### `muse repository log`

Shows the past and pending changelog for the repository

### `muse repository update`

Updates the repository, by default performing a dry run

### `muse repository rollback`

Rolls the repository back to the last checkpoint

### `muse repository clean`

Performs cleanup operations including deleting automatic tags and stashes (if applicable)

### `muse repository syntax`

Verifies the validity of the syntax of any pending changes

### `muse repository cloneRepo`

Clones the specified repository.

### `muse repository updateGitURLconf`

Updates the specified remote origin of the specified repository.

### `muse repository checkoutRepoBranch`

Checks out the specified branch of the specified repository.

### `muse repository removeRepo`

Removes the specified Custom Extension repository directory.

### `muse repository renameRepo`

Renames the specified Custom Extension repository directory.

### `muse repository updateRepo`

Updates the specified Custom Extension repository directory.

## `muse resources`

Resources.

Implemented in [`Resources.php`](../../core/libraries/Hubzero/Console/Command/Resources.php).

Run alone, `muse resources` default (required) command - just executes run

### `muse resources gitstats`

No description available.

### `muse resources exportcsv`

No description available.

### `muse resources exportxml`

No description available.

## `muse scaffolding`

Scaffolding class for generating template extensions.

Implemented in [`Scaffolding.php`](../../core/libraries/Hubzero/Console/Command/Scaffolding.php).

Run alone, `muse scaffolding` default (required) command

### `muse scaffolding create`

No description available.

### `muse scaffolding copy`

No description available.

## `muse searchmigration`

Migration class.

Implemented in [`SearchMigration.php`](../../core/libraries/Hubzero/Console/Command/SearchMigration.php).

Run alone, `muse searchmigration` default (required) command - just executes run

### `muse searchmigration run`

Adds components to solr index

## `muse test`

Test class.

Implemented in [`Test.php`](../../core/libraries/Hubzero/Console/Command/Test.php).

Run alone, `muse test` default execute method

### `muse test run`

Runs available tests for the given extension

### `muse test show`

Shows a list of extensions with available tests

## `muse user:terms`

User class for terms of use functions.

Implemented in [`Terms.php`](../../core/libraries/Hubzero/Console/Command/User/Terms.php).

Run alone, `muse user:terms` default (required) command

### `muse user:terms clear`

No description available.

## `muse user`

User class for general user functions.

Implemented in [`User.php`](../../core/libraries/Hubzero/Console/Command/User.php).

Run alone, `muse user` default (required) command

### `muse user merge`

Merges two users together, disabling the source user

### `muse user unmerge`

Unmerges a previous merge, reenabling the source user

### `muse user disable`

Disables a user completely, without deleting

### `muse user password`

Sets a new password for a user, by username or ID (root only)
