<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tool-administrators/whitelistdirectories
source-id: 3566
modified: 2018-10-24
imported: 2026-09-09
-->
# Directory parameter whitelist

A tool session can be started with file and directory names supplied in the
launch URL. The **Directory Parameter Whitelist** decides which directories
those names may point at. This page is CMS-side and was checked against
`com_tools` in this repository.

## What parameter passing does

A tool is launched at `/tools/<alias>/invoke`. A `params` query argument on
that URL carries a list of values to hand to the tool, one per line:

```text
directory:/home/hubname/username/mycase
file(input):/home/hubname/username/mycase/run.xml
int(runs):20
```

Each line is `type:value` or `type(name):value`. The three types are
`directory`, `file`, and `int`. A name in parentheses is optional; without
one, the tool receives an unnamed parameter of that type.

`com_tools` validates every line before it starts a session. Validation is
all or nothing: one bad line rejects the whole launch. If every line passes,
the component forwards the `params` text exactly as it arrived — the middleware
gets the original values, not the expanded and normalised ones the check
worked on.

## What the whitelist does

For `directory` and `file` parameters the component:

1. Expands a leading `~/` to the member's home directory. If the account has
   no absolute home directory, the launch fails.
2. Rejects any value that is not an absolute path.
3. Normalises the path, resolving `.` and `..` and collapsing repeated
   separators. A `file` value may not end in a separator, `.`, or `..`.
4. Rejects any value containing a control character or invalid UTF-8.
5. Rejects everything if the whitelist is empty.
6. Requires the normalised value to begin with one of the whitelisted
   directories.

Step 6 compares whole path elements: each entry is trimmed and given a
trailing `/` before the comparison, so `/home` matches `/home/hubname/...`
but not `/homework`. Because the check runs after normalisation, `..` cannot
be used to climb out of a whitelisted directory.

`int` parameters are not affected by the whitelist. They are checked for
control characters and then against `^[-+]?[0-9]+$`.

## Setting the whitelist

Any account with `core.manage` on `com_tools` can change it.

1. Sign in to `/administrator`.
2. Go to **Components → Tools**. The pipeline list opens.
3. Select **Options** in the toolbar.
4. On the **Defaults** tab, edit **Directory Parameter Whitelist**. It is a
   comma-separated list of absolute directories. The shipped default is
   `/home`.
5. Select **Save & Close**.
6. Repeat on every hub that needs it — production, stage, development, and so
   on. The setting is per hub, not shared.

The option is `params_whitelist`; see the
[generated `com_tools` parameter reference](../../reference/configuration/components/tools.md)
for the rest of the component's settings.

> **Warning:** Clearing the field does not disable the check, it fails every
> `file` and `directory` parameter. Leaving parameter passing usable means
> keeping at least one directory in the list.

## When validation fails

A launch with an unusable parameter does not start a session. The member gets
the **Bad Parameters** page, which shows the rejected `params` text and a link
to open a support ticket. Nothing is written to the session tables.

## What is not checked here

The CMS checks the shape of the parameters and the whitelist prefix. It does
not check that the paths exist, that the member can read them, or that the
tool can do anything with them. Those are the tool platform's business, and
the platform validates the parameters again on the execution host. The
platform is separate software and is not in this repository, so its side of
the check could not be verified.

Two consequences of forwarding the original text are worth knowing:

- The middleware receives `~/` and `..` unresolved, and has to expand and
  normalise them itself. The whitelist decision was already made on the
  resolved form, so the two must agree.
- Resuming an existing session re-sends the parameters stored with it, and
  they are not checked again. Tightening the whitelist does not retract
  sessions started under the old one.

For the pipeline that gets a tool to the point of being launchable, see
[Tools](../../managers/03-maintenance/02-tools.md) in the hub managers book.
