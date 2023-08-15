# plgUserSecret

## Description

On user login, this plugin checks whether the user has a secret
stored in the database. If not, it creates one for the user, and saves it.

On deidentification of a user, the user secret is replaced with NULL.

The user secret may be used in conjunction with a hub-wide secret and an email
campaign secret to provide a secure hash that can be emailed to users in a
newsletter campaign.

There is no user-facing component to this plugin.

## Architecture

This plugin uses the Hubzero plugin architecture.

## Dependencies

At migration, the `secret` column is created in `jos_users` table for use by 
this plugin. Down migration removes the column from the table.

## Versioning

1.0.0

## Authors

Jeanette Sperhac