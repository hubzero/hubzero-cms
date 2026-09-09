<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/migrations
source-id: 3525
modified: 2014-09-10
imported: 2026-09-09
-->
# Migrations

## Overview

Migrations allow you a group update its separate database without having to connect to the live database and manually updating the schema. Another benefit to using migrations is that they are automatically run every time the super groups code is updated from Gitlab!

## Creating a Migration

Migrations can be created easily with the HUbzero command line application "Muse". From the command line run the following command (in the web root):

`/{web_root}/cli/muse.php group scaffolding migration --group={group_cn} -e=com_{component}`

> **Note:** Simply replate {group_cn} with your groups cname and {component} with the component. The migration file will be automatically placed into the correct location, ready for you to modify and commit when ready.

## Running Migrations

Running migrations is almost as easy as creating them with once again help from the Hubzero command line application. From the command line run the following command (in the web root):

`/{web_roo}/cli/muse.php group migrate -if --group={group_cn}`

> **Note:** Simply replate {group_cn} with your groups cname. The -i argument means ignore dates (run migrations it could have missed) and -f means actually run.

> **Warning:** Note: You only need to manually run migrations in a dev environment. When groups code is updated on live, migrations are automatically run.
