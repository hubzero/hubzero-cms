<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
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

## The five things to know before you run one

The importer is a good tool with sharp edges, and all five of these bite on
live accounts rather than on the file.

1. **It updates as readily as it creates.** A record with an `id`, or with a
   `username` that already exists, is a change to somebody's live account. A
   file you meant as forty new people can quietly rewrite forty existing ones
   if the usernames collide.
2. **The `groups` column replaces hub group memberships outright.** Not adds
   to — replaces. Every group not named in the cell is left, and the
   manager, invitee and applicant roles go with it. See [What an import can
   overwrite](#what-an-import-can-overwrite).
3. **A missing password becomes the username.** If the `password` column is
   absent or empty on a new account, the account is created with its own
   username as its password. Nothing warns you and the password rules are not
   applied. Always supply the column.
4. **Every imported password is expired.** That is the good news: whatever you
   set, the owner has to change it at first login. It is also why the password
   in your file only has to survive one login.
5. **Validation reads the wrong column.** Records are checked against the
   **Edit Profile** column of the registration field table, not **Create
   Account** — so tightening your registration form does not tighten the
   importer, and the checks that do run are fewer than you would expect. See
   [What the importer actually checks](#what-the-importer-actually-checks).

None of it is undone by anything on this screen. There is no "roll back this
run" and the [import archive](04-memberimportarchive.md) keeps a record of the
job, not of the data as it was before. The dry run is the safety net, and it
is a good one — use it every time, including on a job you have run before.

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

## What the importer actually checks

This section exists because the answer surprises people, and because a manager
who assumes the importer enforces the registration form's rules will import
data the registration form would have rejected.

Every record is validated as though the account's owner were editing their own
profile: the check runs in *edit* mode, and so reads the **Edit Profile**
column of the [Registration](02-registration.md) field table. Requiring a field
in the **Create Account** column — the one that governs the public
registration form — has no effect on the importer at all.

In edit mode the check also switches three fields off itself. Username is
treated as read only and both password fields as hidden, so **the importer
never validates a password**: the password rules, the blacklist and the
minimum length are not consulted, which is how the username-as-password
fallback in point 3 above gets through unnoticed.

What is left is the handful of account-level fields whose **Edit Profile**
cell says **Required**: full name, email address, the opt-in flag and the terms
of use. Profile-builder fields are not checked on import in this release — the
code that would check them is commented out — so a field you made required in
the profile builder will not stop a record that omits it.

With the settings a hub ships with, the **Edit Profile** cells for those fields
are not **Required**, which means the practical answer is that almost nothing
is validated. Beyond a name, an email address and a username all being present
and non-empty, a record is accepted. In particular:

- An email address is not checked for being a valid address.
- An email address already in use by another account is not rejected, so two
  accounts can end up sharing one.

> **Note:** Mode changes this too. In **Modify only non-empty fields** mode a
> missing required field is skipped rather than reported, so records that would
> fail in **Overwrite all fields** mode pass. Do your dry run in the mode you
> intend to run in.

If you want the importer to enforce something, set that field to **Required**
in the **Edit Profile** column before you run — and remember you have then also
made it required for every member editing their own profile.

## Which fields the file may carry

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

The precise rule is worth having, because the difference between the safe and
the destructive case is one empty cell. A row whose `groups` cell is empty is
left alone — memberships are not touched. A row whose `groups` cell has
anything in it ends up in exactly those groups and no others. Where the account
was already a member of a group the cell names, its existing role is kept, so
a manager stays a manager; where it was a member of a group the cell does not
name, it is removed as member, manager, invitee and applicant alike.

That makes the column genuinely useful for the case it was built for —
enrolling a cohort into one group in a single pass — and dangerous for updating
anything else. If your file is an update of names or email addresses, delete
the `groups` column before you upload it.

> **Note:** These accounts are placed in an *access* group as well, and not by
> this column. Every account the importer creates goes into the **New User
> Registration Group** from the component's **Options**, Registered by default.
> If a batch needs extra permissions, put them in the extra
> [access group](06-accessgroups.md) afterwards, from the member records.

## An import from start to finish

The rest of this page follows one job through. It is the scenario from the
[section introduction](README.md#a-worked-example): forty participants from a
partner institution need accounts before a workshop opens, and their institution
has sent a spreadsheet of names and email addresses.

Two decisions are already made before you touch the screen. These are new
people, not existing members, so the file carries no `id` column and every row
should create an account. And they all belong in one hub group for the duration
of the workshop, so the file carries a `groups` column naming that one group —
which is safe here precisely because none of these accounts exists yet and none
has memberships to lose.

## Preparing the file

1. Open **Users > Members > Import**.
2. Press **Sample import file** in the toolbar and save the download.
3. Rename it to something that identifies the batch.
4. Replace the word *Example* in every cell with real data, one row per
   account, and delete the columns you are not setting.
5. Save it as CSV, not as a spreadsheet, unless you mean to upload a
   spreadsheet.

Before you go on, look for the two columns that decide how much damage a
mistake does. If the file has a `username` column, check that none of those
usernames already belongs to somebody: a collision turns a create into an
update. If it has a `groups` column, check that every row names the workshop
group and nothing else.

Add a `password` column and fill it, even though the importer does not insist
on one. Leaving it out gives forty people an account whose password is their
own username. Generate the values however your hub normally does; they only
have to last one login, because the importer expires them all.

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
real run would do. This is the only point in the process where a mistake costs
nothing, so spend time here rather than after.

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

For the workshop file, three things are worth opening a record to confirm.

- The record says it will **create** an account rather than update one. If it
  names an existing account, a username in your file is already taken.
- The group memberships it lists are the workshop group and nothing else.
- The name and email have landed in the fields you meant. A column mapped to
  the wrong field is valid data in the wrong place, and the importer will not
  notice.

## Running the import

From the dry-run screen, press **Run For Real** under the countdown. From the
imports list, check the job and press **Run**.

The run screen is the same, without the dry-run notice. **Rerun Import** starts
the job again from the top.

> **Warning:** **Rerun Import** does exactly what it says and is matched by
> nothing that undoes a run. Re-running a job whose file carries usernames will
> match the accounts the first run created and update them; re-running one
> without usernames will create a second set of accounts with generated
> usernames. Neither is what you want after a run that went wrong. Fix the
> file, then decide deliberately which it is.

## Afterwards

The forty accounts now exist, are in the workshop hub group, and are in the
Registered access group. Two things are still outstanding.

- They cannot see the restricted workshop material yet. That needs the
  `Workshop 2026` [access group](06-accessgroups.md), which the importer does
  not set — add the accounts to it from the member records, or from the
  account list.
- Nobody has a password they can use. See [Delivering passwords
  safely](#delivering-passwords-safely).

Check the result before you tell anyone the accounts are ready: filter the
Members list by **- Registration Date -** → **Today** and count the rows.

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
