<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/conventions/commits
-->
# Commit Messages

A commit message has a subject line and, for anything but a one-word fix, a
body. The subject says what changed and where. The body says why, and what the
reader would otherwise have to reconstruct from the diff.

## The subject

```
<extension>: <what the change does>
```

The prefix names the extension the change belongs to, written the way the
codebase writes it: `com_members`, `plg_editors_ckeditor5`, `mod_login`. Where a
change is not in one extension, use the subsystem: `Database`, `Console`,
`Http`, `Filesystem`, `Component`, `Plugin`, `Plugins`, `Documentation`. A
change spanning two extensions names both, comma separated.

Then a sentence. Capitalised, no full stop, describing what the commit does
rather than what was wrong.

```
com_cart: Add the Items Ordered heading string
com_wiki: Label the page state field with its real states
plg_groups_forum: Use the table prefix placeholder
Database: Read a column default from the variable that holds it
com_content, com_categories: Return early when no items are selected
```

Keep the subject under about 72 characters. The last two hundred commits on
this branch have a median subject of 57 characters and a longest of 89; the
50-character target older documentation gave is not what the history does, and
squeezing a sentence into 50 costs more clarity than it buys.

Do not use `[feat]`, `[fix]`, `[refactor]`, `[style]`, `[docs]` or `[test]`
tags. The repository carries 1,559 commits with them, all older; none of the
last three hundred. The extension prefix replaced them and says more.

`[PR #1234]` prefixes appear on merges made through the GitHub interface and
are added by the merge, not typed by hand.

## The body

Separate it from the subject with a blank line and wrap it at 72 characters.

Say why the change is needed before saying what it does. A reader six months
from now has the diff already; what they do not have is the reason, the
symptom, and what you ruled out.

```
com_projects: Fix the FERPA description key

The component manifest reads COM_PROJECTS_CONFIG_FERPALINK_DESC while
the language file defined COM_PROJECTS_CONFIG_FERPAALINK_DESC, so the
options screen rendered the raw key and the defined string was dead.
```

Three things worth writing down:

- **The symptom.** What a user or administrator saw. "The heading rendered as
  the raw key", not "fixed a string".
- **The mechanism.** Why the code did that. Name the file, the condition, the
  key.
- **What you did not change.** If a fix is deliberately narrow, or a related
  fault is left for a separate decision, say so. It stops the next person
  re-investigating.

A commit that only reformats code, or only renames things, gets its own commit
and says so. Never mix a behaviour change with a cleanup pass; the reviewer
cannot see the one for the other.

## References

If a change fixes a reported issue or follows from an outside discussion, name
it in the body:

```
Fixes: https://help.hubzero.org/support/ticket/12345
Refs: https://github.com/hubzero/hubzero-cms/pull/1923
```

Neither trailer appears in the last three hundred commits, so it is a
convention available to you rather than one in daily use. GitHub's own
`Fixes #123` closes an issue in this repository when the commit lands.

## A commit template

`git` will pre-fill the editor from a template. Point `commit.template` at one
in `~/.gitconfig`:

```ini
[commit]
	template = ~/.gitmessage
```

Then write `~/.gitmessage`:

```
#--------------------------------72----------------------------------|
# <extension>: <what the change does>
#
# Why the change is needed, what the symptom was, and what it does not
# cover. Wrap at 72.
#
# Fixes:
# Refs:
```

## Before you commit

Run the linters over what you changed. See
[PHP Coding Style](01-phpcodingstyles.md#checking-your-work) for the commands.
If the change touches `docs/`, run `sh tools/docs/rebuild.sh` and commit the
rebuilt `gh-pages/public/` with it; the Pages workflow fails on a stale copy.
