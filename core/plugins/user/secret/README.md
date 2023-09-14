# plgUserSecret

## Description

On user login, this plugin checks whether the user has a secret stored in the
database. The user secret is a unique 32 character alphanumeric string.

If no secret is found, the plugin creates one for the user, and saves it.
If the user has an existing secret, that secret is retained. The database
requires each user secret to be unique.

On deidentification of a user, the user's stored secret is replaced with NULL.

### Use

There are numerous potential uses for unique user secrets. One example is:

The user secret may be used in conjunction with a hub-wide secret and a
campaign secret to provide a hash. This hash can be emailed to the user as part
of a link, enabling secure access to a URL.

## Architecture

The plugin uses the standard Hubzero plugin architecture, including a migration
script that creates/drops the `secret` database column, and a `secret.php`
script.

`secret.php` provides public `onUserLogin()` and `onUserDeidentify()` functions,
as well as protected functions that generate the secret, check for the secret,
and save or null the secret.

Plugin migration can be accomplished using the Hubzero `muse` script.

There is no user-facing component to this plugin.

## Dependencies

This plugin uses the `jos_users` table to store the user secret. On up
migration of this plugin, the `secret` column is created in the `jos_users`
table. Down migration removes the column from the table.

The plugin uses the Hubzero library function `Password::genRandomPassword()`
to generate the user secret.

## Versioning

1.0.0

## Authors

Jeanette Sperhac