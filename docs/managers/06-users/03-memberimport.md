<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/users/memberimport
source-id: 3358
modified: 2015-10-27
-->
# Member import

The member importer creates and updates accounts in bulk from a data file. It
lives under **Users > Members > Import** and is open only to a Super User —
the controller redirects anyone else back to the Members list.

> **Warning:** An import can rewrite hundreds of accounts in one pass, and
> nothing it does is undone automatically. Always run a test first, and read
> [What an import can overwrite](#what-an-import-can-overwrite) before you run
> one for real.

Use the importer for the cases it is good at: a research group that needs
access at once, or a class whose instructor already holds everyone's details.
For anything smaller, having people register themselves is quicker and produces
better data.

## What the screen holds

Two links sit under the tab: **Imports**, the list of import jobs, and
**Hooks**, the scripts that can transform records on the way in.

The imports list shows one row per job.

| Column | Notes |
|---|---|
| Name | The job's name, with its notes beneath. Click to edit it. |
| # Record(s) | How many records the data file holds, counted when the file was attached. |
| Created On/By | When the job was created and by whom. |
| Last Ran On/By | The last real run. Dry runs do not count. |
| Run Count | How many real runs the job has had. |

Its toolbar:

| Button | What it does |
|---|---|
| **Sample import file** | Downloads a one-row example file, `members.csv`. |
| **Run** | Runs the checked job for real. |
| **Test Run** | Runs the checked job as a dry run. |
| **New**, **Edit**, **Delete** | Manage the jobs. |
| **Help** | The built-in help screen. |

## File formats

The importer sniffs the file's MIME type and picks a reader, so it is not
limited to CSV. Four readers ship:

| Reader | Accepts |
|---|---|
| CSV | `text/csv`, `text/plain` and files whose extension is `csv`. |
| Excel | `.xls` and `.xlsx`. |
| JSON | `application/json` and the `text/json` variants. |
| XML | `application/xml`. |

CSV remains the easiest to prepare and to check, and the sample file is a CSV.

## Which fields are required

The importer validates every record against the **Edit Profile** column of the
[Registration](02-registration.md) field table, not the **Create Account**
column. If a profile field is **Required** there, every record has to carry it
or the record fails. Fields that are hidden for editing are skipped.

The sample file is not a fixed list. It is generated when you press **Sample
import file**, from the accounts table plus every field currently in your
hub's profile builder plus the columns the record handlers understand
(`groups` and `projects`). Download a fresh one for your hub
rather than copying a column list from anywhere else — including from an older
version of this page.

Column names do not have to match the field names exactly. The importer knows a
long list of aliases — `lname`, `lastname` and `surname` all reach `surname`;
`mail`, `email` and `emailaddress` all reach `email` — and it shows you the
guesses it made before anything is imported. The full alias table is in
[`core/components/com_members/models/import.php`](../../../core/components/com_members/models/import.php).

A few columns behave in ways worth knowing about.

| Column | Notes |
|---|---|
| `id` | Leave blank to create a new account. Fill it in to update an existing one. |
| `username` | If blank on a new account, one is generated from the name, then from the part of the email address before the `@`, then from the name with a numeric suffix. |
| `password` | Set on new accounts and immediately expired, so the user must change it at first login. **If the column is missing or empty on a new account, the password is set to the username.** Always supply one. |
| `activation` | 1 for a confirmed email address, 0 or negative for unconfirmed. |
| `approved` | 1 or 2 for approved, 0 for awaiting approval. |
| `block` | 1 blocks the account. |
| `groups` | Hub group aliases, separated by commas or semicolons. |
| `projects` | Project aliases, separated by commas or semicolons. |

> **Warning:** The `groups` column *replaces* the account's hub group
> memberships. Any group not named in the column is removed, including manager,
> invitee and applicant roles. Leave the column out entirely if you do not mean
> to rewrite memberships.

## Preparing the file

1. Open **Users > Members > Import**.
2. Press **Sample import file** in the toolbar and save the download.
3. Rename it to something that identifies the batch.
4. Replace the word *Example* in every cell with real data, one row per
   account, and delete the columns you are not setting.
5. Save it as CSV, not as a spreadsheet, unless you mean to upload a
   spreadsheet.

## Creating the job and attaching the file

1. Press **New**.
2. Under **Details**, give the job a **Name** — it is required — and any
   **Notes**.
3. Under **Parameters**, set **Approved** and **Email new members**.
4. Under **Upload**, choose the file with **Data:**.
5. Under **Data**, set **Mode:**.
6. Press **Save**.

| Field | Notes |
|---|---|
| **Approved:** | *Are newly created accounts auto-approved?* See the note below. |
| **Email new members:** | Sends each new account the standard confirmation email, carrying the username and a confirmation link. It does **not** carry a password. |
| **File:** | Picks a file already in the job's file space, `app/site/import/<job id>/`. Uploading through **Data:** puts a file there. |
| **Mode:** | **Overwrite all fields** (`UPDATE`) replaces a matched account's data with the incoming record. **Modify only non-empty fields** (`PATCH`) leaves anything the record does not supply alone. |

> **Note:** The **Approved** parameter is saved with the job but nothing acts on
> it. Whether an imported account is approved is decided by the record's
> `approved` column, or by the accounts table default — approved — when the
> column is absent. Set the column if it matters.

Saving with a newly uploaded file takes you straight to the **Field Mapping**
screen, which lists every column found in the file next to the member field it
will be written to. The importer's guesses are pre-selected; columns it could
not place are highlighted and default to **(unknown)**, and anything left
unknown is ignored. Correct what is wrong and press **Save**.

You can reopen the mapping later by editing the job.

## Hooks

A hook is a PHP script that runs against each record as it passes through, so
that a file can be reshaped without editing it. Under **Hooks**, press **New**
and give the hook a **Type:**, a **Name:**, optional **Notes:** and a **Hook
Script** file.

| Type | Runs |
|---|---|
| **Post Parse** | After a record is read from the data file, before mapping. |
| **Post Map** | After the record's columns are mapped to member fields. |
| **Post Convert** | After the record is converted into a profile. |

The script is included with `$data` — the record at that stage — and `$dryRun`
in scope, and whatever it returns replaces the record. Scripts are stored in
`app/site/import/hooks/<hook id>/`, and **view raw** on the hooks list shows
one.

A job picks up its hooks on the edit screen, under **Hooks**: three
multi-select boxes, one per type, with **up** and **down** links to set the
order they run in. Ctrl- or Command-click selects more than one.

## Testing an import

Always dry-run first. Nothing is written and the results show exactly what a
real run would do.

1. Check the job's row in the imports list.
2. Press **Test Run**.
3. The run screen opens with a notice that this is a dry run and a five-second
   countdown. Press **Start Now** to go immediately, or wait. **Stop Import**
   halts it.
4. The progress bar fills as records are processed, and the results appear
   below it.
5. Click a record to expand it. Records with problems are flagged
   *[Contains Errors]* or *[Duplicate Record]*, and each shows the field values
   the importer worked out, the interests, custom data and group memberships it
   would set, and any data it could not use.

Read a sample of the records, not just the error count. A dry run reports what
would be written, so this is your only chance to catch a mapping that is
technically valid and factually wrong.

## Running the import

From the dry-run screen, press **Run For Real** under the countdown. From the
imports list, check the job and press **Run**.

The run screen is the same, without the dry-run notice. **Rerun Import** starts
the job again from the top.

## What an import can overwrite

- A record is matched to an existing account by `id` (or `uidNumber`) first,
  and by `username` if there is no id. Anything else creates a new account.
- A matched account is updated. In **Overwrite all fields** mode every field
  the record carries replaces what is there; in **Modify only non-empty
  fields** mode empty cells are left alone.
- A username cannot be changed by import. If a matched account's username
  differs from the record's, the record notes it and the existing username
  stands.
- The `groups` column replaces group memberships outright.
- Every new account's password is expired on creation, so its owner must change
  it at first login.

## Delivering passwords safely

Where the hub handles restricted data, do not put initial passwords in email.
The pattern that works:

1. Email everyone in the batch to say an account has been created and that an
   administrator will contact them with the password. Ask them to call you if
   they are not contacted when promised — that catches a mistyped email address
   or phone number.
2. Give each person their password by telephone.
3. Tell them the password is already expired and that they will be asked to
   change it as soon as they log in. An imported password always is; the
   importer expires it on creation.
4. Keep the import file encrypted, or somewhere that meets whatever rules apply
   to the data. Never send it by email or through a file-sharing service.

If you need to expire a password by hand later, open the account, go to the
**Password** tab, and set **Valid for (days)** to `0`.

> **Warning:** On that tab, check that your browser has not autofilled **New
> Password** before you save. If it has, saving replaces the user's password
> with one of your own.
