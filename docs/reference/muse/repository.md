<!--
status: generated
source: core/libraries/Hubzero/Console/Command/Repository.php
-->

# muse repository

Repository class.

Implemented in [`Repository.php`](../../../core/libraries/Hubzero/Console/Command/Repository.php).

## Running the command alone

`muse repository` — Default (required) command - just run check command

## Tasks

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
