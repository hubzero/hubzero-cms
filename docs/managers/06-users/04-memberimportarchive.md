<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
summary: What has changed for anyone following member import instructions written against an earlier release, and where the current walkthrough is.
-->
# Member import: changes from earlier instructions

[Member import](03-memberimport.md) is the current walkthrough of the
importer, checked against this release. Read this page only if you have an
import file, a set of notes, or a procedure written against an earlier
release. Each item below is somewhere the earlier instructions lead to an
import that does not do what you expect. If you are starting fresh, go
straight to [Member import](03-memberimport.md) instead.

## What changed

**There is no fixed column list.** Earlier instructions printed a table of
thirty-odd columns — `uidNumber`, `mailPreferenceOption`, `nativeTribe` and
the rest — as though that were the schema. It is not. The sample file is
generated when you press **Sample import file**, from the accounts table
plus every field currently in your hub's profile builder plus the `groups`
and `projects` columns. Two hubs with different profile schemas get
different sample files. Always download a fresh one rather than reusing a
column list.

**CSV is not the only format.** The importer sniffs the file's MIME type and
picks a reader; CSV, Excel, JSON and XML all work.

**Columns are mapped, not matched by name.** After a file is uploaded the
importer shows a **Field Mapping** screen with its guess for each column and
lets you correct it. Column names close to the field name — `lname`,
`userPassword`, `emailConfirmed` — are recognised automatically.

**The import mode labels changed.** They now read **Overwrite all fields** and
**Modify only non-empty fields**. Earlier instructions called them *Update*
and *Patch*, and described Update as "when creating new members", which was
never what it meant: the modes decide how a record that matches an existing
account is merged.

**The "Email new member" parameter does not send a password.** It sends the
standard account confirmation email, which carries the username and a
confirmation link. It never delivered the login and password in plain text.

**The "Approved" parameter has no effect** in this release. Whether an imported
account is approved comes from the record's `approved` column.

## Delivering passwords

The procedure for handing out restricted data is in the current chapter,
corrected: see
[Delivering passwords safely](03-memberimport.md#delivering-passwords-safely).
One detail there is worth repeating, because it removes a step from the
earlier procedure: the importer already expires every password it sets, so a
new account's owner is forced to change it at first login without anyone
editing the account.
