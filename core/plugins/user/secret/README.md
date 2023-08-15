# plgUserSecret

## Description

On user login, this plugin checks whether the user has a secret
stored in the database. If not, it creates one for the user, and saves it.
The user secret is a 32 character alphanumeric string. The database requires
each user secret to be unique.

On deidentification of a user, the user's stored secret is replaced with NULL.

### Use

The user secret may be used in conjunction with a hub-wide secret and a
campaign secret to provide a hash. This hash can be emailed to users as part of
a link, enabling secure access to a URL.

## Architecture

This plugin uses the Hubzero plugin architecture.

There is no user-facing component to this plugin.

## Dependencies

This plugin uses the `jos_users` table to store the user secret.

On up migration, the `secret` column is created in `jos_users` table for use by
this plugin. Down migration removes the column from the table.

## Versioning

1.0.0

## Authors

Jeanette Sperhac