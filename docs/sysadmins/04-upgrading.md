<!--
status: rewritten
reviewed-against: 2.4-main @ 5d7f20276e
reviewed: 2026-10-30
screenshots: none
summary: Bringing a hub up to the latest code on its release branch, with a backup taken first and a way back if something goes wrong.
-->
# Upgrading a hub

A hub installed as the [installation overview](01-installing.md) describes is a git
checkout of its release branch, `2.4-main`. Upgrading it means bringing that
checkout up to date, then the libraries Composer installs, then the database.
It takes a few minutes, most of it Composer and the migrations.

Upgrading a hub from the 2.4.6 release, 825 commits and 21 database
migrations behind, to the head of `2.4-main`, then rolling it back and
upgrading it again, was tested with the separate commands `muse upgrade`
now runs. Its content came through unchanged, and the upgraded database
matched a newly installed one. The steps as they stand, with `muse upgrade`,
`muse install verify` and the restore through `muse database load`, were
tested by upgrading a hub from an earlier `2.4-main` and rolling it back.

Run every command in the hub's directory, `/var/www/hub`. The hub's own
commands are `muse`'s, run with `sudo` as on
[Installing the hub](03-installing-the-hub.md): `muse` becomes the account
that owns the hub's files by itself, so what it writes stays the web
server's to write. If the files are your own, leave out `sudo`. If the hub
runs in Docker, run each `muse` command in the container instead:
`sudo docker exec -it hub php core/bin/muse ...`.

## Before you start

**See what is coming.** This fetches the branch and lists what the upgrade
would bring, without changing anything:

```bash
sudo php core/bin/muse repository update
```

**Check that the checkout has no changes of its own.**

```bash
sudo php core/bin/muse repository status
```

It should say the repository is clean. If it lists modified files, someone has
edited the hub's code in place. The update sets those edits aside with
`git stash` before it starts and does not put them back. Move anything that
belongs to your hub out of the code before you upgrade: templates, components
and plugins of your own belong under `app/`, which the update never touches.

**Take the hub offline.** Visitors see a maintenance page while the code and
the database are out of step with each other. The administrator interface
stays available. Use **Site Offline** in **Site > Global Configuration**, as
[Taking the hub offline](../managers/05-configuring/01-hub.md#taking-the-hub-offline)
describes, or set `'offline' => '1'` in `app/config/offline.php`.

**Back up the database and `app/`.** These are what an upgrade changes, and
the only way back to them afterwards. Use the database name and account from
`app/config/database.php`:

```bash
mariadb-dump --single-transaction --no-tablespaces --routines --triggers \
    -u hub -p hub > ~/hub-before-upgrade.sql
sudo tar czf ~/hub-app-before-upgrade.tgz app
chmod 600 ~/hub-before-upgrade.sql
```

The dump holds every member's password hash, so keep it to yourself. Keep
both somewhere other than the hub's own directory, and check that the dump is
not empty before you go on. Hubs from this version on can make the dump with
`muse database dump`; in older ones that command does not work, so this page
uses MariaDB's own.

## Upgrade

1. **Bring the code up to date.**

   ```bash
   sudo php core/bin/muse repository update -f
   ```

   It tags the current state as a rollback point, then fast-forwards the
   checkout to the branch. It ends with **complete**. If the branch cannot be
   fast-forwarded because the checkout has commits of its own, it stops
   without changing anything.

   On a hub older than this version, it brings only what
   `muse repository update` last fetched, so run that first, as in
   [Before you start](#before-you-start), and it can say **complete** even
   when it stopped. Check with `git status`, which should say the branch is
   up to date with `origin/2.4-main`.

2. **Bring the libraries, the database and the cache up to the new code.**

   ```bash
   sudo php core/bin/muse upgrade
   ```

   It is the new code's own command, so it is there now even if the hub was
   older than it. It runs Composer, as the hub's account, which installs
   exactly the versions in the new `composer.lock`; then the migrations; then
   it clears the cache. It stops at the first of them that fails, and says
   which. It ends with *The hub is up to date with its code.* The warnings a
   new hub can show may appear; they are explained in
   [Warnings during the migrations](03-installing-the-hub.md#warnings-during-the-migrations).
   A migration that ended in a warning is tried again on every run.

3. **Reload PHP**, so no worker carries on with copies of the old code it had
   already compiled: `sudo systemctl reload php8.2-fpm` for Apache or nginx
   on Debian and Ubuntu, `sudo systemctl reload php-fpm` on Enterprise
   Linux, `sudo docker restart hub` for FrankenPHP.

4. **Check the hub, then put it back online.**

   ```bash
   sudo php core/bin/muse install verify
   ```

   It should report no failures. Then sign in to the administrator interface
   and to the site, and open the pages your members use most. Then set
   **Site Offline** back to **No**.

## If something goes wrong

The code and the database have to go back together. The database cannot be
taken back by running anything: the migrations only move it forward, so it
comes back from the dump.

1. **Put the database back**, first, while the new code is still in
   place: its `muse database load` is the one that works.

   ```bash
   sudo php core/bin/muse database load - --drop-all-tables --exact < ~/hub-before-upgrade.sql
   ```

   `-` reads the dump from standard input, which your shell opens, so the
   hub's account need not be able to read your home directory.
   `--drop-all-tables` removes every table first, including any the upgrade
   added, and `--exact` loads the dump just as it was. Under Docker, add `-i`:
   `sudo docker exec -i hub php core/bin/muse database load - ...`.

2. **Put the code back** to the rollback point the update made:

   ```bash
   sudo php core/bin/muse repository rollback
   ```

   It asks you to confirm the date of the snapshot.

3. **Put the libraries back** to the versions the old code expects:

   ```bash
   sudo php core/bin/muse install vendor
   ```

4. **Put `app/` back:**

   ```bash
   sudo rm -rf app
   sudo tar xzf ~/hub-app-before-upgrade.tgz
   sudo chown -R --reference=/var/www/hub app
   ```

5. Clear the cache, with `sudo php core/bin/muse cache clear`, and reload
   PHP, as in step 3 above.

Every update leaves a rollback point behind, a git tag named
`cmsrollbackpoint-` and the time. Once an upgrade has worked and the hub has
run on it for a while, clear out the older ones:

```bash
sudo php core/bin/muse repository clean
```

It asks two questions: whether to remove every rollback point except the
latest, and whether to throw away the changes earlier updates set aside with
`git stash`. Look at those first with `git stash show -p` if you do not know
what they are.

## When the update refuses

**"This checkout is not on a branch that follows another."** The checkout
is not on a branch, usually because someone checked out a release tag, or it
is on a branch of its own that follows nothing on GitHub. Put it back on the
release branch first, running `git checkout 2.4-main` in `/var/www/hub` as the
account that owns it: `sudo -u "$(stat -c %U /var/www/hub)" git checkout 2.4-main`,
or under Docker `sudo docker exec hub git checkout 2.4-main`.

**The fetch fails.** The server cannot reach GitHub, or the checkout's
`origin` points somewhere it can no longer reach. `git remote -v` shows where
it points.

**Composer reports that a package needs a newer PHP.** The new code needs a
PHP version the server does not have. Roll back the code with
`muse repository rollback`, upgrade PHP, and start again.

**PHP itself has a security release.** Under Apache or nginx it comes with
the server's other updates. FrankenPHP's image has to be rebuilt to get it,
and the container started again from the new image:

```bash
sudo docker build --pull -t hub-frankenphp /opt/hub-frankenphp
sudo docker rm -f hub
```

Then start it with the same `docker run` as in
[Preparing a server for FrankenPHP in Docker](02-preparing/09-docker.md). The hub's
files and certificates are outside the container, so nothing is lost.
