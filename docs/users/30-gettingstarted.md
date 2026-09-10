<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: ok
source: https://help.hubzero.org/documentation/240/users/gettingstarted
source-id: 3334
modified: 2021-05-18
imported: 2026-09-09
source-state: unpublished
-->
# Getting started

The first hour on a hub, in order: create an account, confirm it, fill in
enough of a profile to be findable, arrange a dashboard, and then find the
part of the site you actually came for.

The point of the first four steps is the fifth. Nothing on a hub is much use
until other members can find you and add you to things — a group cannot invite
a member it cannot see, and a project owner has to pick you off a list by
name. Half an hour spent on an account and a profile saves a week of "who is
this person and why can't they open the files".

The example running through this chapter is a postdoc joining her lab's hub.
She has been told two things: run the group's infiltration model, and get at
the field data the lab is preparing for release. That is a typical reason to
be here, and it needs all five steps.

## 1. Create an account

Go to `/register`, or select **Create an account** under the login form.
The form asks for a username, a password, your name, and an email address,
plus whatever profile questions your hub has added. Full instructions,
including what each fieldset means and what the password rules are, are in
[Registration](20-registration.md).

> **Note:** If the hub has turned registration off, `/register` returns a
> "not found" page and the link under the login form is not shown. Ask the
> hub's support staff for an account instead.

## 2. Confirm your email address

Unless the hub has been set to skip it, you are sent a message with an
activation link. Follow it and log in. Until you do, the account exists but
cannot be used. Some hubs also require an administrator to approve the
account, which happens after you confirm.

The message is generated automatically, so check your spam folder if it does
not arrive within a few minutes.

Use an address you will still be reading in two years. The postdoc in the
example uses her university address rather than the lab's shared mailbox: hub
notifications about her project, her tool sessions, and her publication all go
to whichever address is on the account, and the account is hers, not the
lab's.

## 3. Fill in your profile

Your profile is at **Members > (your name) > Profile**, or directly at
`/members/myaccount`. Each row is one field with an **Edit** link beside it;
selecting it opens the field in place, with a **Privacy** menu next to the
value. See [Member profile](15-profile.md) for the field-by-field detail and for
uploading a picture.

Two things are worth doing straight away:

- Decide whether the whole profile is public or private, using the toggle at
  the top of the Profile tab. Per-field privacy only works while the profile
  itself is public. Which state a new account starts in depends on the hub's
  **Default Privacy** setting, so look rather than assume.
- Fill in your organisation and interests. They are what makes you findable
  by other members, and on hubs that run Solr search, member profiles are
  indexed.

For the postdoc, "findable" is the whole point of the step. The group's
manager searches the member list for her name and her department to send the
invitation; if the profile is private, or the organisation is blank, that
search returns nothing useful and she has to be added by hand.

## 4. Arrange your dashboard

The Dashboard tab is your own page of modules — current tool sessions and
disk usage, your groups, your projects, recent activity, whatever the hub
offers. It starts from a default arrangement and you rearrange it by dragging.
[Member dashboard](14-dashboard.md) covers adding, removing, and configuring
modules.

This is the one step you can skip. Nobody else sees your dashboard and nothing
depends on it. It is worth five minutes only because the panels you put there
are the ones you will otherwise go looking for in a menu every day — for the
postdoc, **My Sessions**, so she can get back into a running model, and
**My Projects**, so the field-data project is one click away.

> **Note:** A hub can switch dashboard personalisation off. If there is no
> **Add Modules** button and nothing will drag, that is why.

## 5. Find your way around

What a hub offers beyond that varies. These are the parts most hubs turn on.

### Groups

A [group](11-groups/README.md) is a shared space, private or public. Each one can
have its own pages, membership roles, announcements, blog, calendar,
collections, forum, wiki, and file area. Joining an existing group is usually
the fastest way to see what a hub is actually used for.

Groups are for people who keep working together: a lab, a course cohort, a
standards committee. A group outlives any one piece of work in it.

### Projects

A [project](16-projects.md) is a workspace for a piece of research: files,
notes, a to-do list, and a database area. Project file storage can be the
hub's own, or connected to Amazon S3, Dropbox, GitHub, or Google Drive through
the hub's filesystem plugins. When the work is ready, a project is the
starting point for a publication.

A project is not a group. It is private to the people invited to it, it is
about one body of work, and it exists partly so that data can be shared
inside the team before anyone is ready to show it to the world. The lab's
field data lives in a project for exactly that reason: three people are
cleaning it, and nothing is public until they say so.

### Publications

[Publications](18-publications.md) walks a contributor through releasing
data, code, or a paper in steps — files, authors, abstract, licence — that can
be revisited in any order. A published version can carry a licence and a minted
DOI. Where the hub uses curation, a curator reviews the draft and leaves notes
before it goes live; see [Curation](18-publications.md#curation).

This is where the lab's field season ends up: the project is the workspace,
the publication is the citable thing with a version number that a paper can
point at.

### Tools

Where a hub runs the tool platform, published tools launch in the browser and
execute on the hub's own machines. Running one, sharing a session, and
contributing your own are covered in [Tools](22-tools.md).

Nothing is installed on your computer, and a session keeps running when you
close the browser, which is why the postdoc can start the infiltration model
on a laptop and pick the session up the next morning from **My Sessions**.

![Animation of a simulation](media/gettingstarted-tools2.gif)

### Wiki and collections

The [wiki](23-wiki.md) holds member-written articles; an article's author can keep
a fixed list of authors or leave it open for anyone to edit.
[Collections](01-collections.md) are a lighter way to gather images, links, and
files into a board that other members can follow and re-collect. A collection
is not a publication: it points at things, it has no version and no DOI, and
nobody curates it.

## What is not here

The tool execution platform, the Solr search service, and the statistics
collection behind the hub's `/usage` page are separate software that runs
alongside the CMS. Chapters that depend on them say so, because on a hub where
they are not installed the corresponding pages are empty or absent.
