<!--
status: rewritten
reviewed-against: 2.4-main @ 6dd778fb19
reviewed: 2026-10-29
screenshots: none
summary: Bringing a hub up to the latest code on its release branch, with a backup taken first and a way back if something goes wrong.
-->
# Upgrading a hub

A hub installed as [Installing a hub](../00-installing.md) describes is a git
checkout of its release branch, `2.4-main`. Upgrading it means bringing that
checkout up to date, then the libraries Composer installs, then the database.
It takes a few minutes, most of it Composer and the migrations.

The steps below were tested by upgrading a hub from the 2.4.6 release, 825
commits and 21 database migrations behind, to the head of `2.4-main`, then
rolling it back and upgrading it again. Its content came through unchanged,
and the upgraded database matched a newly installed one.

Run every command in the hub's directory, as the account that owns the hub's
files: `www-data` on Ubuntu, as in the install guide. Under FrankenPHP, use
`frankenphp php-cli` where the commands below say `php`.

```bash
cd /var/www/hub
```

## Before you start

**See what is coming.** This fetches the branch and lists what the upgrade
would bring, without changing anything:

```bash
sudo -u www-data php core/bin/muse repository update
```

**Check that the checkout has no changes of its own.**

```bash
sudo -u www-data php core/bin/muse repository status
```

It should say the repository is clean. If it lists modified files, someone has
edited the hub's code in place. The update sets those edits aside with
`git stash` before it starts and does not put them back. Move anything that
belongs to your hub out of the code before you upgrade: templates, components
and plugins of your own belong under `app/`, which the update never touches.

**Take the hub offline.** Visitors see a maintenance page while the code and
the database are out of step with each other. The administrator interface
stays available. Use **Site Offline** in **Site > Global Configuration**, as
[Taking the hub offline](../05-configuring/01-hub.md#taking-the-hub-offline)
describes, or set `'offline' => '1'` in `app/config/offline.php`.

**Back up the database and `app/`.** These are what an upgrade changes, and
the only way back to them afterwards. Use the database name and account from
`app/config/database.php`:

```bash
mariadb-dump --single-transaction --no-tablespaces --routines --triggers \
    -u hub -p hub > ~/hub-before-upgrade.sql
sudo tar czf ~/hub-app-before-upgrade.tgz app
```

Keep the backups somewhere other than the hub's own directory, and check that
the dump is not empty before you go on.

## Upgrade

1. **Bring the code up to date.**

   ```bash
   sudo -u www-data php core/bin/muse repository update -f
   ```

   It tags the current state as a rollback point, then fast-forwards the
   checkout to the branch. It ends with **complete**. If the branch cannot be
   fast-forwarded because the checkout has commits of its own, it stops
   without changing anything.

2. **Install the libraries the new code needs.**

   ```bash
   sudo -u www-data php core/bin/composer install --working-dir=core
   ```

   Composer installs exactly the versions in the new `composer.lock`, adding,
   updating and removing packages to match. A warning that it *"Cannot create
   cache directory"* is harmless.

3. **Bring the database up to date.** Run it once without `-f` to list what
   it will do, then with `-f` to do it:

   ```bash
   sudo -u www-data php core/bin/muse migration run
   sudo -u www-data php core/bin/muse migration run -f
   ```

   It ends with *Success: Up migration complete!* The dry run ends with the
   same words, so read the list above them to see what will run. The same
   warnings a new hub shows may appear again; they are explained in
   [Warnings during the migrations](../00-installing.md#warnings-during-the-migrations).
   A migration that ended in a warning is tried again on every run.

4. **Clear the cache.**

   ```bash
   sudo -u www-data php core/bin/muse cache clear
   ```

5. **Reload PHP**, so no worker carries on with copies of the old code it had
   already compiled: `sudo systemctl reload php8.3-fpm` for Apache or nginx;
   restart FrankenPHP.

6. **Check the hub, then put it back online.** Sign in to the administrator
   interface and to the site, and open the pages your members use most. Then
   set **Site Offline** back to **No**.

## If something goes wrong

The code and the database have to go back together. The database cannot be
taken back by running anything: the migrations only move it forward, so it
comes back from the dump.

1. **Put the code back** to the rollback point the update made:

   ```bash
   sudo -u www-data php core/bin/muse repository rollback
   ```

   It asks you to confirm the date of the snapshot.

2. **Put the libraries back** to the versions the old code expects:

   ```bash
   sudo -u www-data php core/bin/composer install --working-dir=core
   ```

3. **Put the database back.** Empty it first: the upgrade may have added
   tables that the dump does not mention, and loading the dump over them would
   leave them in place. The hub's own database account has the right to do
   this, on its own database only:

   ```sql
   DROP DATABASE hub;
   CREATE DATABASE hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   ```bash
   mariadb -u hub -p hub < ~/hub-before-upgrade.sql
   ```

4. **Put `app/` back:**

   ```bash
   sudo rm -rf app
   sudo tar xzf ~/hub-app-before-upgrade.tgz
   sudo chown -R www-data:www-data app
   ```

5. Clear the cache and reload PHP, as in steps 4 and 5 above.

Every update leaves a rollback point behind, a git tag named
`cmsrollbackpoint-` and the time. Once an upgrade has worked and the hub has
run on it for a while, clear out the older ones:

```bash
sudo -u www-data php core/bin/muse repository clean
```

It asks two questions: whether to remove every rollback point except the
latest, and whether to throw away the changes earlier updates set aside with
`git stash`. Look at those first with `git stash show -p` if you do not know
what they are.

## When the update refuses

**"This checkout is not on a branch that follows another."** The checkout
is not on a branch, usually because someone checked out a release tag, or it
is on a branch of its own that follows nothing on GitHub. Put it back on the
release branch first: `sudo -u www-data git checkout 2.4-main`.

**The fetch fails.** The server cannot reach GitHub, or the checkout's
`origin` points somewhere it can no longer reach. `git remote -v` shows where
it points.

**Composer reports that a package needs a newer PHP.** The new code needs a
PHP version the server does not have. Roll back the code with
`muse repository rollback`, upgrade PHP, and start again.
