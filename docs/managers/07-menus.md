<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/menus
source-id: 3366
modified: 2014-10-10
imported: 2026-09-09
-->
# Menus

A menu is a named list of links. It does two jobs on a hub: it gives visitors
a way to move around, and it gives the pages it points at their URLs. A page
no menu item points at is reachable but hard to find, and often has an
address nobody would guess.

Most of a hub's content needs no menu item at all. Members find a dataset by
searching the resource catalogue, a question by browsing Answers, a group by
its name. What menus are for is the small set of destinations that have to be
one click from anywhere: the front page, the page that says what the hub is,
the way into the thing the hub exists for. A coastal-flooding research group
running a hub for its models needs four links across the top, not forty.

Menus are managed at **Menus → Menu Manager**. The **Menus** menu also lists
every menu defined on the hub, with **Add New Menu Item** under each.

## Menus, menu items, and modules

Three different things share the word "menu", and confusing them is the usual
reason a new menu appears nowhere.

- A **menu** is the container: a row in the database with a system name (its
  *menu type*), a title, and a description. It has no position, no styling
  and no presence on the site.
- A **menu item** is one link inside a menu. It has a *type*, which decides
  which page it opens and which options the form offers, and an *alias*,
  which — with its parents' aliases — is the page's URL.
- A **module** is what puts a menu on a page. A **Menu** module (`mod_menu`)
  is pointed at one menu by its menu type and given a template position; the
  template decides where on the page that position lands, and whether it
  renders at all.

Creating a menu and creating the module that displays it are two separate
steps, and neither does the other's job. A menu with no module is invisible.
A module in a position the template does not render is also invisible. The
module side is covered in [Modules](10-extensions/01-modules.md).

A plain hub ships one site menu — **Main Menu**, menu type `mainmenu` —
holding a single item, **Home**.

## How many menus a hub needs

Most hubs need one menu. A second is worth having when a set of pages has to
appear in a place of its own — a side menu that shows only on the pages of
one section, a short row of links in the footer. Beyond that, each new menu
costs you a module, a position, and a place to look when a link goes missing.

The test is not "are these links related" but "do these links appear
together, in one place, on the same set of pages". Two menus rendered in the
same position on the same pages should be one menu. A menu whose module is
assigned to every page and whose items are all in one section should usually
be items under a parent in the main menu instead.

Signs a hub has too many:

- Menus with no module against them in the Menu Manager. Nobody can see
  those; they exist only as URLs.
- Two menus with the same **Position**. They stack, in module ordering, and
  visitors read them as one list with an unexplained gap.
- A menu holding one item. Fold it into the main menu as a child of whatever
  it belongs under.

There is no limit and no performance cliff — this is about whether the next
person can find the link they need to change.

> **Note:** A menu is not an access control. **Access** on a menu item
> decides who sees the *link*; it does not decide who can open the page. That
> distinction is worked through under
> [Example: linking into one component's content](#example-linking-into-one-component-s-content).

## Menu Manager

**Menus → Menu Manager** opens **Menu Manager: Menus**. Each row shows the
menu's title, its menu type beneath, the number of published, unpublished and
trashed items in it, and the modules linked to it.

- The **title** opens the menu's items.
- The **menu type** opens the menu itself for editing.
- Under **Modules Linked to the Menu**, each module is a link to its
  settings. Where there is no module, the cell offers **Add a module for this
  menu type**.

The toolbar is **New**, **Edit**, **Delete**, **Rebuild**, **Options**, and
**Help**. **Delete** asks for confirmation first, and it means it: deleting a
menu deletes all of its menu items and the menu modules attached to it.
**Rebuild** repairs the nested-set bookkeeping behind the menu tree, which is
worth doing if items start appearing at the wrong depth.

### Creating a menu

1. **Menus → Menu Manager**, then **New**.
2. Fill in **Menu type**, the system name. It is what modules and menu items
   refer to, it must be unique, and it cannot sensibly be changed later.
   Whatever you type is lowercased and stripped of anything that is not a
   letter, a digit, or a hyphen, up to 24 characters; leave it blank and it
   is derived from the title.
3. Fill in **Title**, which is what the administrator interface shows, and
   optionally a **Description**.
4. **Save & Close**.
5. Back on the list, follow **Add a module for this menu type** to create the
   module that displays it. See [Modules](10-extensions/01-modules.md).

## Menu items

**Menu Manager: Menu Items** lists the items of one menu — pick the menu from
the first drop-down above the list, or open it from the Menu Manager. The
other filters are **Select Max Levels**, status, access level, and language,
and the search box matches the title or the alias.

The columns are **Title**, **Status**, **Ordering**, **Access**, **Menu Item
Type**, **Home**, **Language**, and **ID**. Sort by **Ordering** ascending to
reorder items; the list is a tree, so an item's indent is its depth.

The toolbar is **New**, **Edit**, **Publish**, **Unpublish**, **Check In**
(administrators only), **Trash**, **Home**, **Rebuild** (administrators
only), and **Help**. Filtering to **Trashed** replaces **Trash** with **Empty
trash**, which deletes for good. **Home** makes the selected item the site's
default page; there must always be exactly one.

### Adding a menu item

1. Open the menu and select **New**.
2. Next to **Menu Item Type**, select **Select**. A picker opens listing
   every type available on this hub, grouped by the extension that provides
   it, plus a **System Links** group at the end.
3. Choose a type. The form reloads with the fields that type needs.
4. Fill in **Menu Title**. Everything else has a usable default.
5. **Save & Close**.

The details are:

| Field | Notes |
|---|---|
| **Menu Item Type** | Set through the picker. Changing it changes the rest of the form. |
| **Menu Title** | Required. The text shown in the menu. |
| **Link** | Read-only for a component item — the picker fills it in. Editable for an **External URL** item. |
| **Alias** | This item's own path segment. Together with its parents' aliases it is the page's URL. Leave it blank and it is generated from the title. |
| **Note** | A private note, shown only in the administrator interface. |
| **Access** | Which viewing level sees the item. |
| **Status** | Published, Unpublished, or Trashed. |
| **Menu Location** | Which menu the item belongs to. Change it to move the item between menus. |
| **Parent Item** | **Menu Item Root**, or another item in the same menu. This is what nests items, and what builds the URL. |
| **Ordering** | Position among its siblings. Available once the item has been saved. |
| **Target Window** | **Parent**, **New Window With Navigation**, or **New Without Navigation**. |
| **Default Page** | Component items only. Makes this the site's home page. |
| **Language** | **All**, or one content language. |
| **Template Style** | A specific template style for this page, or the site default. |

Down the right-hand side are collapsible panels: the request fields the
chosen type declares (for **Single Article**, a **Select Article** picker),
**Link Type Options** (link title attribute, link CSS class, link image, and
whether to show the title next to the image), any layout options the type
declares, and **Module Assignment for this Menu Item**, which lists the
modules that will and will not show on this page.

> **Note:** A menu item's options are the ones its layout declares, in the
> component's `site/views/<view>/tmpl/<layout>.xml`. The component's own
> global options are *not* merged into the menu item form: the code that
> would load them is disabled behind an always-false condition, with a `TODO`
> saying that fixing it breaks the form. Recorded in
> It is recorded with the project.
### Where the types come from

The picker is built from every enabled component. For each one the hub looks
for a `<menu>` block in the component's `site/metadata.xml`. If there is
none — and only Articles ships that file at all, with the block missing from
it — it scans `site/views/`, taking each view that is not prefixed with an
underscore and each layout XML file inside it, and uses the title declared
there. That is why Articles offers **Single Article**, **Category Blog**,
**Category List**, **List All Categories**, **Featured Articles**, **Archived
Articles**, and **Create Article**: one entry per layout under
`core/components/com_content/site/views`.

A type's *request fields* come from the same XML. A layout that declares one
lets the menu item name a particular thing — **Single Article** declares an
article picker, **Category List** a category picker, **Resources → Resource
(standard) Layout** a resource ID. A layout that declares none can only point
at the view in general.

> **Warning:** The scan lists every layout it finds, whether or not the
> component can act on it. A type works only if the component has a task
> named after the layout, and it can point at a particular item only if the
> layout declares a request field. Several shipped types fail one test or the
> other: **Knowledge base → Article** and **Knowledge base → Category**
> declare no request field, so neither can name the article or category it is
> meant to open; **Resources → Standard browse layout**, **Resources item**
> and **Browse by tags** all fall through to the Resources front page;
> **Articles → Create Article** returns a 404. Open a new menu item on the
> site before you publish it. Recorded with the project.

The **System Links** group at the end is fixed, and holds three types that
belong to no component:

| Type | What it does |
|---|---|
| **External URL** | Links to any address, on this hub or elsewhere. Also acts as a redirect — see [URLs](08-content/urls.md#redirecting-with-a-menu-item). |
| **Menu Item Alias** | Points at another menu item, so one page can appear in two menus. Leave the **Alias** field empty when the two items share a parent. |
| **Text Separator** | A label with no link, for breaking a long menu into groups. |

### What a menu item does besides appear in a menu

A menu item is also the hub's record of what a URL means, and that has two
consequences worth knowing before you plan a menu.

**A page can be reachable with no menu item at all.** When a request arrives,
the site looks for the menu item whose route is the longest prefix of the
path. If none matches, the path is handed to the component's own router,
which resolves it on its own terms — an article by its category path and
alias, a resource by its ID, a group by its name. Those pages work. They are
simply unadvertised, and their addresses are whatever the router produces
rather than anything you chose. [URLs](08-content/urls.md) has the rules for
articles; each component's own chapter under
[Components](09-components/README.md) covers the rest.

**A menu item can change a page it did not send you to.** Because the match
is on a prefix, an item with alias `resources` becomes the active item for
`/resources/42` as much as for `/resources`, and the leftover segments go to
the component. Everything the active item carries then applies: its **Page
Display Options**, its **Template Style**, and the modules assigned to it. So
one menu item can change the look of every page beneath it, and the same page
can look different depending on which route the reader took. For articles,
which of the item's options beat the article's own depends on the layout —
see [Article Manager](08-content/articlemanager.md).

## Example: navigation for a new hub

A coastal-flooding research group has just had its hub installed and nothing
has been added to it. It needs four links across the top: the front page, a
page saying what the hub is, the member directory, and the resource catalogue
the hub exists to hold.

Two facts about a fresh install decide whether any of this is visible.

- The default site template style on a new hub is **Welcome**, a pre-launch
  splash that renders no modules and no component output whatsoever.
  Following its **get started** link switches the site's default style to
  **kimera**. Until that has happened nothing you do in the Module Manager
  shows anywhere. See [Templates](10-extensions/02-templates.md).
- The **Main Menu** module a plain install creates sits in `position-7`,
  which no template in this tree declares or renders, so a plain install
  comes up with no navigation at all. Installing the optional sample content
  moves that module to `user3` — the position both site templates use for the
  main navigation. Check which of the two you have before you start.

Then build the menu.

1. Write the two articles first — **Content → Article Manager**, **New** —
   one for the front page and one about the hub, both filed under
   **Uncategorised**. See [Article Manager](08-content/articlemanager.md).
2. **Menus → Menu Manager**, then **New**. Set **Menu type** to `hubnav` and
   **Title** to `Hub navigation`. **Save & Close**.
3. Open the new menu, select **New**, and next to **Menu Item Type** select
   **Select**, then **Articles → Single Article**. Set **Menu Title** to
   `Home`, choose the front-page article in **Select Article**, and
   **Save & Close**.
4. Add the second item the same way: **Articles → Single Article**, **Menu
   Title** `About`, the About article in **Select Article**, **Alias**
   `about`. Its URL becomes `/about`.
5. Add the third: **Members → Browse Members**, **Menu Title** `Members`,
   **Alias** `members`.
6. Add the fourth: **Resources → Main page**, **Menu Title** `Resources`,
   **Alias** `resources`.
7. Sort the list by **Ordering** ascending and put the four in the order you
   want them read.
8. Tick the `Home` item and select **Home** in the toolbar. A hub has exactly
   one home item, so this takes the flag off the shipped **Home** item in
   Main Menu.
9. Back on **Menus → Menu Manager**, in the **Hub navigation** row, follow
   **Add a module for this menu type**.
10. In the module, set **Title** to `Hub navigation`, **Show Title** to Hide,
    **Position** to `user3`, **Status** to Published and **Access** to
    Public. Under **Select Menu** choose `hubnav`, and under **Menu
    Assignment** choose **On all pages**. **Save & Close**.
11. Deal with the shipped module. **Extensions → Module Manager**, open
    **Main Menu**. On a hub with sample data it is published in `user3` and
    will now render alongside yours, so set **Status** to Unpublished. On a
    hub without, it is in `position-7` and renders nothing — unpublish it
    anyway, so the next administrator is not left wondering.

Why those four types and not others:

- **Single Article** for the front page, not **Featured Articles**. The
  shipped home item is a Featured Articles item and no screen in the
  administrator interface can put an article on it, so a hub left on it has
  an empty front page for ever. The warning is in
  [Article Manager](08-content/articlemanager.md).
- **Browse Members** rather than **Members main page**: it is the directory
  itself, with the search and sort controls. It is for signed-in members —
  a guest who follows it is sent to the login form and returned afterwards.
- **Resources → Main page** is the catalogue's front page, listing the major
  resource types. Because routes match as prefixes, this one item is also the
  active item for every `/resources/…` page beneath it, which is what gives
  the whole catalogue a consistent heading and template style.

If you would rather not have a second menu, the same result comes from
skipping step 2, adding the four items to **Main Menu**, and moving the
shipped module to `user3` instead of unpublishing it.

## Example: linking into one component's content

The same hub now has thirty short method notes, written as articles and filed
in a category **Methods** with two sub-categories, **Tides** and **Storm
surge**. The group wants three more links: one into the method notes as a
whole, one to the storm-surge notes alone, and one to the single note people
ask for most often.

All three are Articles types, and what separates them is what the page does
with the category or article you pick.

1. **Menus → Menu Manager → New**. **Menu type** `methods`, **Title**
   `Methods`. **Save & Close**.
2. **The list.** In the new menu, **New**, then **Menu Item Type** →
   **Select** → **Articles → List All Categories**. **Menu Title**
   `Methods`, **Alias** `methods`, and in **Select a Top Level Category**
   choose **Methods**. **Save & Close**.
3. **The filtered view.** **New**, then **Articles → Category Blog**. **Menu
   Title** `Storm surge`, **Alias** `storm-surge`, **Parent Item**
   `Methods`, and in **Choose a category** choose **Storm surge**.
   **Save & Close**. Its URL is `/methods/storm-surge`.
4. **The single item.** **New**, then **Articles → Single Article**. **Menu
   Title** `Calibrating the surge model`, **Parent Item** `Methods`, and pick
   the article in **Select Article**. **Save & Close**.
5. Add a module for the `methods` menu as in the previous example, or leave
   the menu without one and reach the pages by their URLs — the items still
   give the pages their addresses either way.

What each type changes on the page:

- **List All Categories** shows the *categories* beneath the one you chose —
  Tides and Storm surge — not the notes. Its options are about the category
  listing: **Top Level Category Description**, **Subcategories
  Descriptions**, **# Articles in Category**, and whether **Empty
  Categories** appear at all. Use it when the sub-categories are the thing
  worth choosing between.
- **Category Blog** shows the articles in one category, laid out with their
  intro text: **# Leading Articles** across the full width, then **# Intro
  Articles** in **# Columns**, then **# Links** as bare titles. It is the
  layout for a section people browse.
- **Category List** — not used here — shows the same articles as a table of
  titles with **# Articles to List** per page, **Table Headings** and a
  **Filter Field**. Same category, same articles, a very different page: pick
  it when readers are looking something up rather than reading down.
- **Single Article** shows one article and nothing else. Because the item
  names that article, it is also the article's canonical address; reached
  through this item, the options set on the item apply to it.

Note what none of them do: none of these items limits who can *find* the
articles. A note in the Methods category still answers at its category-path
address whether or not a menu item points at it. **Access** on a menu item
decides who sees the *link* — the module leaves out items above the reader's
viewing level — and nothing in the routing turns it into a check on the page.
What guards the page is the article's own access level and the component's
permissions. See [Access levels](06-users/07-accesslevels.md).

## Taking stock of navigation you inherited

Hubs accrete menus. Someone adds a menu for a workshop, someone else adds one
for a course that ended, a template changes and a position stops rendering,
and three years later nobody knows which of the seven menus the site actually
shows. Before you change anything, find out what is live.

Do this in order. The first four steps change nothing.

1. **Menus → Menu Manager.** This one screen answers most of it. Each row
   gives the menu's title, its menu type, the count of published,
   unpublished and trashed items, and, under **Modules Linked to the Menu**,
   every menu module pointed at that menu, written as *title* (*access level*
   in *position*).
2. **Find the menus nobody can see.** Any row whose modules cell offers **Add
   a module for this menu type** has no module at all. Its items still give
   pages their URLs, and those pages still answer — but no menu on the site
   lists them.
3. **Check the positions against the template.** A module in a position the
   current template does not declare renders nowhere. **Extensions →
   Template Manager** shows which template style the site uses, and the
   positions each template declares are listed in
   [Templates](10-extensions/02-templates.md). `position-7`, which a plain
   install uses for the shipped **Main Menu** module, is declared by no
   template in this tree.
4. **Read the counts.** Each of the three numbers is a link into the item
   list already filtered to that state. A menu with two published items and
   nineteen trashed ones has been abandoned; a menu with published items and
   no module is the case in step 2.
5. **Unpublish before you delete.** Set the module's **Status** to
   Unpublished, or trash the menu items, and leave the hub for a week. Both
   are reversible from the same screen. Deleting is not: **Delete** on the
   Menu Manager removes the menu, every item in it, *and* the modules
   attached to it, and **Empty trash** on a trashed item list is final.
6. **Watch what the URLs do.** Trashing or deleting a menu item takes away
   the address it defined. Component pages beneath it usually still answer,
   because the component's own router resolves them, but at whatever address
   the router produces rather than the one the item gave them. Anything
   printed, cited or bookmarked at the old address needs an entry in the
   **Redirect Manager** — **Site → Maintenance → Routes**. See
   [URLs](08-content/urls.md#redirects).

> **Warning:** The **Modules Linked to the Menu** column lists modules
> whether or not they are published. Seeing a module named there does not
> mean the menu appears anywhere. Follow the link and check **Status** and
> **Position** on the module itself.

## Grouping articles under a menu

To give a set of articles a section of their own — an About section, say —
build the menu first and then show it:

1. Create the articles. See [Article Manager](08-content/articlemanager.md).
2. **Menus → Menu Manager → New**. Give the menu a **Menu type** of `about`
   and a **Title** of `About`. **Save & Close**.
3. Open the new menu and add one item per article, each of type **Articles →
   Single Article**, choosing the article in the **Select Article** field.
4. Give the section a parent: add one more item, set the other items'
   **Parent Item** to it, and their URLs become `/about/<alias>`.
5. Back on the Menu Manager, follow **Add a module for this menu type**.
6. In the module, set a **Title**, set **Status** to **Published**, choose a
   **Position** — `left` and `footer` exist in the templates that ship — and
   under **Select Menu** choose the menu you just made.
7. Under **Menu Assignment**, set **Module Assignment** to **Only on the
   pages selected** and tick, in **Menu Selection**, the menu items the
   module should appear on.
8. **Save & Close**.

That is the side-menu pattern: the top navigation from the first example
stays in `user3` on every page, and this second menu appears in `left` only
on the pages of its own section.

The module's other options are **Start Level**, **End Level**, **Show
Sub-menu Items**, **Show as Disclosure Menu**, and **Show Top Level Items as
Links**; the advanced tab adds a menu tag ID, a menu class suffix, a layout,
and caching. There is no "Menu Style" option. Older documentation describes
one; it was removed years ago.

## Redirecting a URL

A menu item of type **External URL** whose route matches the requested path
redirects to its link, which is how one hub address is pointed at another
without moving any content. The Redirect Manager is usually the better tool;
both are covered in [URLs](08-content/urls.md#redirects).

## Options

**Options** on the Menu Manager sets the **Page Display Options** every menu
item inherits — browser page title, whether to show the page heading, the
page heading text, and a page class suffix — and the component permissions.
They are listed in the
[Menus configuration reference](../reference/configuration/components/menus.md).
