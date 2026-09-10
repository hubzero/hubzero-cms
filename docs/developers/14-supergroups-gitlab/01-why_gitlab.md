<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups_gitlab/why_gitlab
source-id: 3528
modified: 2014-09-10
-->
# Why GitLab

A super group's code is a directory on the hub's server. Without a repository,
changing it means someone editing production files over SSH, and the only
record of what changed is the file system. Keeping the directory in a GitLab
repository moves the writing somewhere else and turns each change into
something that can be read before it lands.

## Nobody needs an account on the hub's server

The developer works on their own machine, or a development hub, against a
clone. They never log in to the production hub. The only thing that reaches
production is a `git pull`, run from the administrator interface by someone
who already had administrative access.

## Changes are read before they land

Two things enforce the review, one in GitLab and one in the hub:

- When the hub creates a group's project it protects the `master` branch, so
  only GitLab users with the right role can merge into it. That call is
  `protectBranch()` in
  [`helpers/gitlab.php`](../../../core/components/com_groups/helpers/gitlab.php).
- The hub only ever pulls. There is no path from the administrator interface
  that pushes a change into a group's repository, apart from the very first
  commit that creates it.

So a change arrives on the hub only if someone merged it in GitLab and then an
administrator pulled it.

> **Note:** Whether merge requests are actually reviewed, by whom, and how
> long that takes is a policy each hub sets in GitLab. Nothing in the CMS
> enforces a review; it enforces only that the hub does not write to the
> repository.

## Developers work on a copy

The usual arrangement is that each developer forks the group's project and
works in their fork, so nothing they do can affect the live group or another
developer until a merge request is accepted. Forking is a GitLab feature; the
hub knows nothing about it and does not create forks.

## Updates are a screen, not a deployment

Once a hub is configured, any administrator who can reach **Users** →
**Groups** can pull new code for one group or several at once, look at what
would change, and then merge it. The screens are described in
[Developing](03-developing.md#pulling-the-changes-into-the-hub).

Merging also runs the group's [migrations](../13-supergroups/06-migrations.md),
so a schema change travels with the code that needs it.

## The rest is GitLab

Issue tracking, a wiki per project, the code browser, protected branches
beyond `master`, review rules, CI — all of that is GitLab's, available to
whoever administers it, and outside anything this repository can confirm.

## What it costs

- **A GitLab to run.** The hub is a client of one; it does not provide one.
- **Accounts, by hand.** The CMS creates no GitLab users and links no hub
  account to a GitLab account. Access is granted in GitLab by whoever
  administers it.
- **Production only.** The hub creates a group's project only when
  `application_env` starts with `production`. On a development hub, saving a
  super group creates the directory and the database and stops there.
