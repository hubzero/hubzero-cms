<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups_gitlab/developing
source-id: 3530
modified: 2014-09-10
-->
# Developing

The cycle for a super group managed through GitLab is: work in a clone,
propose the change as a merge request, and have an administrator pull the
merged result into the hub.

The examples use `mygroup` as the group's alias, `hub-example` as the hub's
GitLab group and `theuser` as your GitLab user name. The project is
`sg_mygroup`, in the group `hub-example`; both names are built by the hub when
it creates the project, as described in [Setup](02-setup.md).

> **Note:** Everything up to the last section happens in GitLab and in git.
> None of it is the CMS, and none of it could be verified against this
> repository. Use it as a description of the arrangement the CMS expects, and
> your own GitLab's documentation for the details.

## Fork the project

Find `hub-example / sg_mygroup` in GitLab and fork it. The fork is yours: you
can push to it freely without touching the group the hub pulls from.

## Clone your fork

```bash
git clone git@gitlab.example.org:theuser/sg_mygroup.git
cd sg_mygroup
```

Clone anywhere you like. A development hub is the convenient place, because
you can then point a super group at the working copy and see the result.

> **Note:** Older documentation gave a one-line clone that moved the files up
> and removed the directory. That was for cloning *into* a group directory
> that already existed. A plain clone is what you want.

## Add the upstream remote

Your fork does not track the group's real repository. Add it:

```bash
git remote add upstream git@gitlab.example.org:hub-example/sg_mygroup.git
git remote -v
```

```text
origin    git@gitlab.example.org:theuser/sg_mygroup.git (fetch)
origin    git@gitlab.example.org:theuser/sg_mygroup.git (push)
upstream  git@gitlab.example.org:hub-example/sg_mygroup.git (fetch)
upstream  git@gitlab.example.org:hub-example/sg_mygroup.git (push)
```

Keeping the fork in step with upstream is the part people skip and regret. Do
it before every push.

## What belongs in the repository

Everything the hub put there when it created the project: `template/`,
`components/`, `macros/`, `migrations/`, `pages/`, `language/`.

Two things are deliberately excluded and must stay out:

- `uploads/` — the group's own files, which belong to its members and are not
  code.
- `config/db.php` — the group's database password.

The hub writes both into `.git/info/exclude` when it sets the repository up.
That file is local to the clone the hub made; a fresh clone does not have it,
so take care not to add either path by hand.

## Work

Ordinary git. Commit as you go. The hub sets the repository up on `master`
and protects that branch, so `master` is what a merge request targets.

Schema changes belong in
[migrations](../13-supergroups/06-migrations.md), in the group's top-level
`migrations` directory, so they run when the code is merged on the hub.

## Sync before you push

```bash
git fetch upstream
git checkout master
git merge upstream/master
```

Resolve any conflicts here, in your own clone, where it costs nothing. A merge
request from a fork that is behind is harder to review and may be sent back.

## Push and open a merge request

```bash
git push origin master
```

Then, in GitLab, open a merge request from your fork's `master` to
`hub-example/sg_mygroup`'s `master`. Write a description that says what
changed and why: the person approving it is reading the diff cold.

How long approval takes, and who does it, is your hub's policy. GitLab mails
you when the request is accepted or closed.

## Pulling the changes into the hub

The last step is a hub administrator's, in the administrator interface. The
screens are in **Users** → **Groups**.

1. Tick the super groups to update in the group list.
2. Select **Update Groups Code**. Nothing is changed yet: the hub fetches from
   the remote and lists the commits each group is behind by, or says *Your
   code is currently up to date!*
3. Look at the list. Each group with something to merge gets a **Merge
   Changes** tick box, ticked.
4. Select **Merge Groups Code**.

**Update Groups Code** is only on the toolbar when **Repo Management** is on
and the administrator holds the `core.manage` permission for `com_groups`.
Groups in the selection that are not super groups, or that have no `.git`
directory, are listed as failures and skipped.

The merge runs `muse group update -f` and then `muse group migrate -f` in the
group's directory. Concretely, for each group:

- Local modifications in the group's directory are **stashed** first, so
  anything edited on the server by hand is set aside rather than merged.
- A tag named `cmsrollbackpoint-<timestamp>` is written, so the state before
  the merge can be recovered.
- The merge is **fast-forward only**. A group whose directory has commits of
  its own will refuse to merge rather than produce a merge commit.
- The group's [migrations](../13-supergroups/06-migrations.md) run afterwards,
  against the group's own database.

> **Warning:** The controller means to skip migrations when the update fails,
> but the test it uses never matches, so migrations run either way. Read the
> output of the merge instead of assuming it stopped. Recorded in
> It is recorded with the project.
## Rolling back

The rollback point the merge wrote is reachable from muse:

```bash
php core/bin/muse group update rollback --group=mygroup -f
```

That resets the group's directory to the most recent
`cmsrollbackpoint-` tag. It does not undo migrations; a schema change is
reversed by running its `down()`, with
`muse group migrate -d=down -f --group=mygroup --file=Migration….php`.

`php core/bin/muse group update status --group=mygroup` reports what the
working copy looks like without changing anything.
