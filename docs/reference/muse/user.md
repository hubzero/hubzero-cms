<!--
status: generated
source: core/libraries/Hubzero/Console/Command/User.php
-->

# muse user

User class for general user functions.

Implemented in [`User.php`](../../../core/libraries/Hubzero/Console/Command/User.php).

## Running the command alone

`muse user` — Default (required) command

## Tasks

### `muse user merge`

Merges two users together, disabling the source user

### `muse user unmerge`

Unmerges a previous merge, reenabling the source user

### `muse user disable`

Disables a user completely, without deleting

### `muse user password`

Sets a new password for a user, by username or ID (root only)
