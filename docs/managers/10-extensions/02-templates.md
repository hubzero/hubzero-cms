<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/extensions/templates
source-id: 3407
modified: 2009-10-01
imported: 2026-09-09
-->
# Template Manager

A template controls how a page looks: the skeleton HTML, the stylesheets, the
scripts, and the module positions the page offers. The Template Manager sets
which template the site and the administrator interface use, tunes each
template's parameters, and edits template source files.

Select **Extensions** → **Template Manager**, or go to
`/administrator/index.php?option=com_templates`. The screen has two tabs,
**Styles** and **Templates**. Styles opens first.

Most hubs open this screen exactly twice: once just after installation, to
move the site off the splash template it is installed with, and once to pick
administrator colours that distinguish the live hub from the test one. After
that it is left alone for years. If your hub already looks the way you expect
and the administrator interface is a colour you can live with, you can stop
here.

## What this is not

It is not where the site's appearance is designed. Changing colours,
typography or layout beyond the handful of parameters a template exposes
means editing or writing a template, which belongs in `app/templates/` and is
a developer's job; see [Output
overrides](../../developers/11-templates/09-overrides.md).

It is not where module positions are chosen either. The template *offers*
positions; the [Module Manager](01-modules.md) puts modules into them.

> **Warning:** Making a different style the default changes the site for
> every visitor from that moment, and it is not only a change of appearance.
> Templates declare different positions, so a module sitting in a position
> the new template does not draw disappears without a word. Check the site
> immediately afterwards, and see [Positions](01-modules.md#positions).

## Templates and styles

A **template** is the code: a directory containing `index.php`,
`templateDetails.xml`, stylesheets, and optionally layout overrides. A
**style** is a saved set of one template's parameters. You assign styles, not
templates, and one template can have as many styles as you want — a blue one
and a green one, say — with different menu items using each.

Templates ship in [`core/templates/`](../../../core/templates); a hub's own
go in `app/templates/`, which takes precedence over a core template of the
same name.

| Template | Client | Notes |
|---|---|---|
| **hubzero** | Site | The current site template, in `app/templates/`. Declares `breadcrumbs` and `endpage` as positions and drops `introblock`. |
| **kimera** | Site | The older standard site template. Parameters for header style, primary and secondary colour, background pattern or image. |
| **lucent** | Site | An older site template again. Same declared positions as kimera, no parameters. |
| **Welcome** | Site | A pre-launch splash template. It draws no modules at all. Its **Template** parameter names the template to switch to, `kimera` by default. |
| **kameleon (admin)** | Administrator | The administrator template. Parameters for header style and colour theme. |

`core/templates/system` is not a selectable template. It holds the shared
error and module-chrome layouts every template falls back on.

## The Styles tab

A style is what is actually assigned, so this tab is where the site's
appearance is decided. Three styles ship in the install data:

| Style | Template | Client | Default on a fresh install |
|---|---|---|---|
| **Welcome Template** | welcome | Site | Yes |
| **kameleon (admin)** | kameleon | Administrator | Yes |
| **HUBzero Standard Site Template - 2015** | kimera | Site | No |

The site default being **Welcome Template** is the single most surprising
thing about a new hub. That template draws no modules at all, so the hub
looks empty until the default is moved to the kimera style — which is what
the **Get started** link on the splash page does, and what selecting the
kimera style and pressing **Default** here does. See [Why a fresh hub looks
empty](01-modules.md#why-a-fresh-hub-looks-empty).

The list shows every saved style. Its columns:

| Column | Meaning |
|---|---|
| Preview | For a site style, a link that opens the style in a new tab. Administrator styles cannot be previewed. |
| **Style** | The style name. Click it to edit. |
| **Client** | Site or Administrator. |
| **Template** | The template the style belongs to. |
| **Default** | Whether this is the default style for its client. |
| **Assigned** | Whether any menu items point at this style. |
| **ID** | The row's primary key. |

A search box matches the style name; two drop-downs filter by
**- Select Template -** and by client.

The Preview links only appear when **Preview Module Positions** is enabled in
**Options**.

### Toolbar

| Button | Effect |
|---|---|
| **Default** | Makes the selected style the default for its client. |
| **Edit** | Opens the selected style. |
| **Duplicate** | Copies the selected styles. |
| **Delete** | Deletes the selected styles. The default style cannot be deleted. |
| **Options** | Component settings and permissions. |
| **Help** | Opens the built-in help screen. |

There is no **New** button. New styles are made by duplicating an existing
one.

### Editing a style

| Field | Notes |
|---|---|
| **Style Name** | Required. |
| **Default** | For a site style, **No** or **All**. For an administrator style, a Yes/No radio. |
| **Menus assignment** | Site styles only, and only if you can edit menus and change state. Ticks the menu items that should use this style. |

The right-hand column shows the style's ID, the template's description from
`templateDetails.xml`, the template name and the client, none of them
editable. Below them the template's own parameters appear in collapsible
panels — **Basic Options** and **Advanced Options** for kimera. A template
with no parameters shows *No options found for this template.*

## The Templates tab

This tab lists the installed templates themselves, with a thumbnail, the
template name, the client, and the version, date and author read from
`templateDetails.xml`. Its only toolbar buttons are **Options** and **Help**;
templates are not created, enabled or deleted here.

> **Note:** The list comes from the extensions table, not from the
> filesystem, and the shipped install data registers four templates whose
> directories are not in the tree: `hubbasic`, `hubbasic2012`, `hubbasic2013`
> and `hubbasicadmin`. They appear here with no version, date or author,
> because there is no manifest to read them from. They are leftovers from
> older releases. Ignore them, and do not assign a style to one.

Click a template name to open **Template Manager: Customise Template**. That
screen lists the template's editable files in three groups, each entry a link
to a plain-text source editor with **Save**, **Save & Close** and **Cancel**:

- **Template Master Files** — the page skeletons the platform looks for by
  name: `index.php`, `error.php`, `print.php`, `component.php`, `offline.php`,
  `group.php`, `email.php`.
- **Assets** — every other `.css`, `.less`, `.scss`, `.js` or `.php` file in
  the template.
- **Overrides** — everything under the template's `html/` directory, which is
  where a template replaces a component's or module's own layout.

> **Warning:** Files under `core/` belong to the platform and are replaced on
> upgrade. Edit a template only after copying it into `app/templates/`.

> **Note:** The **Copy Template** form on this screen does not work. It posts
> a `copy` task that the templates controller does not implement, so the
> submission silently returns you to the list without creating anything. Copy
> a template on the filesystem instead.

## Module positions

Each template declares the positions it offers in `templateDetails.xml`. The
[Module Manager](01-modules.md) reads that list when you pick a position for
a module. kimera and lucent both declare:

`footer`, `banner`, `welcome`, `left`, `right`, `helppane`, `user3`,
`introblock`, `notices`, `search`

hubzero declares:

`footer`, `banner`, `welcome`, `left`, `right`, `helppane`, `user3`,
`notices`, `search`, `breadcrumbs`, `endpage`

kameleon declares:

`menu`, `submenu`, `toolbar`, `title`, `status`, `icon`, `cpanel`, `debug`

To see where they land, set **Preview Module Positions** to Enabled in
**Options** and append `?tp=1` to a site URL. Every position is then drawn as
a labelled outline, including empty ones.

## Changing the administrator colours

1. Go to **Extensions** → **Template Manager**.
2. On the **Styles** tab, select **kameleon (admin)** and choose **Edit**.
3. Set **Header** to **Light** or **Dark**. Light gives a light toolbar and a
   coloured menu; Dark gives the reverse.
4. Set **Theme** to one of the named colours, or to
   **- Custom (color specified below) -** and put a hex value in **Custom
   color**. The value must start with `#`.
5. Select **Save & Close**.

## Giving kimera a background image

1. Upload the image through **Content** → **Media Manager**.
2. Go to **Extensions** → **Template Manager** and edit the **kimera** style.
3. Open **Advanced Options** and put the image's path, relative to the
   document root, in **Background image**.
4. Select **Save & Close**.

> **Note:** A value in **Background image** overrides whatever
> **Background pattern** is set to under **Basic Options**.

## Options

**Options** has one setting, `template_positions_display`
(**Preview Module Positions**, disabled by default), plus a permissions tab.
See [the generated reference](../../reference/configuration/components/templates.md).
