<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/memberimportarchive
source-id: 3359
modified: 2019-09-25
summary: Why the old member import walkthrough no longer applies, and where the current one is.
-->
# Member import (archive)

This page carried an older walkthrough of the member importer, built around a
fixed table of CSV columns. It is kept because it is linked from elsewhere.
Everything you need is now in [Member import](03-memberimport.md), checked
against the code in this release.

Read the list below if you have an old import file, an old set of notes, or a
procedure someone wrote down before this release. Each item is somewhere the
old instructions would lead you into an import that does not do what you
expect. If you are starting fresh, go straight to
[Member import](03-memberimport.md) instead.

## What changed

**The column list was never fixed, and is now clearly not.** The old page
printed a table of thirty-odd columns — `uidNumber`, `mailPreferenceOption`,
`nativeTribe` and the rest — as though that were the schema. It is not. The
sample file is generated when you press **Sample import file**, from the
accounts table plus every field currently in your hub's profile builder plus
the `groups` and `projects` columns. Two hubs with different profile schemas
get different sample files. Always download a fresh one rather than reusing a
column list.

**CSV is not the only format.** The importer sniffs the file's MIME type and
picks a reader; CSV, Excel, JSON and XML all work. The old page's "the import
only accepts CSV files" is wrong.

**Columns are mapped, not matched by name.** After a file is uploaded the
importer shows a **Field Mapping** screen with its guess for each column and
lets you correct it. Column names close to the field name — `lname`,
`userPassword`, `emailConfirmed` — are recognised automatically.

**The import mode labels changed.** They now read **Overwrite all fields** and
**Modify only non-empty fields**. The old page called them *Update* and
*Patch*, and described Update as "when creating new members", which was never
what it meant: the modes decide how a record that matches an existing account
is merged.

**The "Email new member" parameter does not send a password.** It sends the
standard account confirmation email, which carries the username and a
confirmation link. The old page said it delivered the login and password in
plain text.

**The "Approved" parameter has no effect** in this release. Whether an imported
account is approved comes from the record's `approved` column.

## Delivering passwords

The restricted-data procedure from the old page still holds and has been moved
into the current chapter, corrected: see
[Delivering passwords safely](03-memberimport.md#delivering-passwords-safely).
One detail there is worth repeating, because it makes the old page's third step
unnecessary — the importer already expires every password it sets, so a new
account's owner is forced to change it at first login without anyone editing
the account.
