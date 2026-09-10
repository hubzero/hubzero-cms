<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Language Manager

The Language Manager is where you choose which installed language the hub
speaks and where you change individual pieces of wording without editing a
file. It is reached from **Extensions → Language Manager** and has three
screens, listed across the top of each of them:

| Sub-navigation | What it is for |
|---|---|
| **Installed - Site** and **Installed - Administrator** | The languages installed for each half of the hub, and which one is the default. |
| **Content** | Content languages, which only matter on a hub running more than one language at once. |
| **Overrides** | Changing the text behind any language key, per language and per half of the hub. |

On most hubs only the third screen ever gets used. Hubzero ships one language,
`en-GB`, so there is nothing to choose on the first screen and nothing to
declare on the second; but every hub has wording it wants to change, and the
override editor is the supported way to change it.

[Languages](../../developers/07-extensions/03-languages.md) in the developers
book covers the other half of the subject: where an extension's INI files
live, how keys are named, and when they are loaded. Read it if you are about
to write an override — you need the key, and that page explains how keys are
built.

## Installed languages

**Installed - Site** and **Installed - Administrator** are the same screen with
the client switched. Each lists the language packs installed for that half of
the hub, one row each, read from the extensions table and from the language's
own `<tag>.xml` metafile:

| Column | Meaning |
|---|---|
| **Num** | Row number. |
| **Language** | The language's name, from its metafile. |
| **Language Tag** | The tag, such as `en-GB`. This is also the directory name. |
| **Location** | Site or Administrator, matching the screen you are on. |
| **Default** | Whether this is the language that half of the hub uses. |
| **Version**, **Date**, **Author**, **Author Email** | From the metafile. |

Only enabled language packs are listed. A pack whose metafile is missing or
unreadable is still shown — greyed as archived, with the tag repeated in place
of the name and the metafile columns empty — but it offers no radio button, so
it cannot be made the default. That state means the extensions table has a row
for a language whose files are not on disk.

To change the default, select the radio button on a row and press **Default**
in the toolbar. The setting is stored on the component itself, one value for
each client, so the site and the administrator interface can differ.

> **Note:** Changing the default does not move members who have chosen a
> language on their own profile, or a visitor who arrived on a
> language-specific URL.

The toolbar's **Install Language** button links to `com_installer`, which has
no language-specific screen; it lands on the Extension Manager's **Manage**
list. Install a language pack the same way as any other extension, through
[Extension Manager](../10-extensions/04-extension-manager.md).

## Content languages

A content language is a row in a table saying "this hub publishes content in
this language, under this URL code". It exists for the multilingual setup: the
**System - Language Filter** plugin reads content languages to decide which
URL prefixes are valid, and a language switcher module offers the published
ones to visitors. On a single-language hub the screen has no effect on
anything and can be left alone.

The list shows title, native title, language tag, URL language code, image
prefix, status, ordering, access level, whether the language has a home page,
and the row ID. The edit form takes:

| Field | Meaning |
|---|---|
| **Title** | The name shown in lists. |
| **Title Native** | The name in the language itself. |
| **URL Language Code** | Appended to the site URL — `/en/` with SEF on, `&lang=en` with it off. Must be unique. |
| **Language Tag** | The exact tag of the installed language pack this row stands for. |
| **Image Prefix** | Prefix of the flag image the language switcher uses. |
| **Status** | Published, unpublished, or trashed. |
| **Access** | The viewing level the language is offered to. |
| **Description**, **Metadata Options**, **Custom Site Name** | Per-language metadata, and a site name that replaces the global one for this language. |

> **Warning:** The toolbar on this list does not work. **Publish**,
> **Unpublish**, **Trash** and **Edit** post the selection under one name and
> the controller reads another, so they act on an empty selection and report
> nothing done; **Empty Trash** posts a task the controller does not
> implement. The only working routes into a content language are the title
> link, which opens the edit form, and the **Status** field inside that form.
> Both faults are recorded with the project.

The component also carries a multilingual status panel, listing whether the
language filter plugin is on, how many switcher modules are published, and
whether each published content language has a home page. It is reached from
`mod_multilangstatus`, an administrator module, but that module's link is
wrong and the panel's own layout reads variables the controller never sets, so
it does not render usefully. Treat the feature as unfinished.

## Overrides

This is the screen worth knowing. An override replaces the text behind one
language key, for one language and one half of the hub, without touching a
shipped file — so it survives an upgrade, where editing
`core/components/com_blog/site/language/en-GB/en-GB.com_blog.ini` would not.

Open **Extensions → Language Manager → Overrides**. The drop-down at the top
right picks which file you are editing, as a language and location pair —
*English (United Kingdom) - Site*, *English (United Kingdom) - Administrator*,
and so on for every installed language. The list below shows the overrides
already in that file, by **Constant** and **Text**, with a search box that
matches either.

To add one, press **New**:

| Field | What to put in it |
|---|---|
| **Language Constant** | The key whose text you are replacing, such as `COM_BLOG_BROWSE`. |
| **Text** | The replacement. Keep any `%s`, `%d` or `%1$s` placeholders — the hub substitutes values into them, and dropping one changes what the sentence says. |
| **For both locations** | Only offered when editing an administrator override. Writes the same override into the site file as well, which some plugins need because their strings are loaded on both sides. The two copies are independent afterwards. |
| **Language**, **Location**, **File** | Read-only. They tell you which file is about to be written. |

The right-hand panel searches for a key when you do not know it. Type the text
you can see on the page, choose whether you are searching **Constant** names or
**Value** text, press **Search**, and click a result to drop its constant into
the form. That search runs against a database table the screen fills the first
time you use it, by reading every INI file under `core/` and `app/` for the
selected language — expect a pause, and a *Please wait while the cache is
recreated* notice, on the first search after a change of language or location.

Overrides are written to a plain INI file per language and client:

```text
app/bootstrap/site/language/overrides/en-GB.override.ini
app/bootstrap/administrator/language/overrides/en-GB.override.ini
```

The translator merges that file over the shipped strings on every request, so
an override takes effect immediately. Deleting an override rewrites the file
without it and clears the cache.

> **Tip:** Because the file is plain INI in `app/`, it is the right place for
> a hub's wording changes and it belongs in whatever the hub keeps under
> version control. Editing that file by hand and editing it through this
> screen are the same operation.

## Permissions

The **Options** button opens the component's permissions grid — Configure,
Access Administration Interface, Create, Delete, Edit, Edit State — and
nothing else. There are no other settings. The screens themselves check those
rules: the override editor's **Save** needs Edit or Create, **Delete** needs
Delete, and the **Default** button on the installed list needs Edit State.
