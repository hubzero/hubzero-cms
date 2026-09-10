<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups_gitlab/setup
source-id: 3529
modified: 2014-09-10
-->
# Setup

Three parties have something to do before a super group can be developed
through GitLab: the hub, whoever administers the GitLab, and the developer.
Only the first is done in this software.

## Hub setup

1. Go to **Users** → **Groups** and select **Options**.
2. Open the **Super Groups** tab.
3. Set **Repo Management** to yes.
4. Put the GitLab **API endpoint** in **Repo URL** — the base the client
   appends `groups` and `projects` to, so a v4 API is
   `https://gitlab.example.org/api/v4`.
5. Put an access token in **Repo API Key**.
6. Select **Save & Close**.

The token is sent as a `PRIVATE-TOKEN` header on every call, and searches ask
GitLab for objects at access level 40 and above, so the token needs at least
**Maintainer** on whatever it is expected to find or create. The source says
so in as many words.

That is the whole of the hub-side configuration. There is nothing else to
install.

> **Note:** The client is created with TLS certificate verification turned
> off (`'verify' => false` in
> [`helpers/gitlab.php`](../../../core/components/com_groups/helpers/gitlab.php)),
> which lets it talk to a GitLab with a self-signed certificate — and means it
> will not notice one it should have refused.

> **Important:** Older documentation had a hub administrator generate an SSH
> key for `www-data`, add it to a GitLab account, and SSH once to accept the
> host key. None of that is needed here. The hub pushes over HTTPS, using a
> remote of the form `https://oauth2:<token>@<host>/<path>` built from the
> project's `http_url_to_repo`. There is no SSH from the hub to GitLab.

### Checking it

Saving a super group calls `validate()` first, which checks the option, the
URL and the key, then asks GitLab for `personal_access_tokens/self`. The
messages you may see are:

| Message | Meaning |
|---|---|
| *Gitlab is not setup properly for managing super group repositories…* | **Repo Management** is off, or the URL or key is empty |
| *The Gitlab API key was rejected. Check the Repo API Key in the Groups configuration.* | GitLab answered 401 |

> **Warning:** That first warning appears on **every** super group save on a
> hub that has not configured GitLab, including hubs that never intend to.
> It is noise, not a fault with the group. Recorded in
> It is recorded with the project.
## What the hub does when a super group is saved

Everything below happens in `_handSuperGroupGitlab()` in
[`admin/controllers/manage.php`](../../../core/components/com_groups/admin/controllers/manage.php),
after the group's directory and database have been created, and only when
`application_env` starts with `production`. On a development or staging hub
the whole step is skipped.

1. **Find or create a GitLab group** named `hub-<first label of the hub's
   host name>` — `hub-example` for `example.org`. If the search returns more
   than one match the save stops with an error.
2. **Find or create a project** in it named `sg_<group alias>`, with issues,
   merge requests, wiki and snippets enabled and the hub group's description
   as its own.
3. **Stop if the group directory already has a `.git`.** Setup runs once.
4. **Push the directory** by running
   [`gitlab_setup.sh`](../../../core/components/com_groups/admin/assets/scripts/gitlab_setup.sh):
   `git init`, a `master` branch tracking `origin`, an exclude list, `git add`,
   an initial commit authored as *&lt;site name&gt; Groups
   &lt;groups@&lt;host&gt;&gt;*, `git remote add origin`, and
   `git push -u origin master`.
5. **Protect `master`**, so merges into it need the right role in GitLab.

Two paths are excluded from the repository by that script, and should stay
excluded: `uploads/*`, which holds whatever the group's members upload, and
`config/db.php`, which holds the group's database password.

> **Note:** A super group that already exists and has no repository gets one
> the next time an administrator saves it, on a production hub with the
> options set. There is no separate migration step and no need to ask anyone
> to run one.

## Staging hubs

A hub whose `application_env` starts with `staging` behaves differently in one
place: selecting **Update Groups Code** for a group that has no `.git`
directory runs
[`gitlab_setup_stage.sh`](../../../core/components/com_groups/admin/assets/scripts/gitlab_setup_stage.sh),
which tars the group's directory as a backup beside it, empties the directory,
and clones the project into it.

> **Warning:** That script deletes the contents of the group's directory
> before cloning, `uploads` included — and `uploads` is not in the repository,
> so it is not restored by the clone. The tarball beside it is the only copy.
> Read the script before running it against anything you care about.

## GitLab setup

Creating accounts, granting people access to the hub's GitLab group, and
setting whatever review rules the hub wants are jobs in GitLab. The CMS never
creates a user, never links a hub account to a GitLab account, and never
grants anyone access to a project.

## Developer setup

Also GitLab's, not the hub's:

- An account, and membership of the project.
- An SSH key on that account, if you clone over SSH — added under the
  profile's **SSH Keys**. More than one key is fine, one per machine you work
  from. Cloning over HTTPS with a token instead works equally well.

Neither of those could be verified against this repository; they are how
GitLab works, not how the hub works.

## Known gaps in the integration

- The GitLab group name is derived two different ways. Creating a project uses
  the **first** label of the host name; the staging setup path uses the label
  before the top-level domain. On `example.org` those agree. On
  `www.example.org` the first gives `hub-www` and the second `hub-example`.
- `updateTask()` runs `gitlab_reset_remote.sh` when a repository's stored
  token no longer matches the configured one. That script does not exist in
  this repository.
- `gitlab_fetch.sh` and `gitlab_merge_and_migrate.sh` are in the scripts
  directory but nothing calls them, and they invoke `cli/muse.php`, a path
  that no longer exists — muse is `core/bin/muse`.
- `protectBranch()` calls `PUT /projects/:id/repository/branches/:branch/protect`.
  That is where the GitLab v3 API kept branch protection; current versions
  expose it elsewhere. Not verified against a live GitLab.
- Every response is assumed to be JSON that decodes to an array. A proxy
  error page or an HTML 404 from the URL in **Repo URL** produces a fatal
  error rather than a message.

All of these are recorded with the project.
