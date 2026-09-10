<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/content/urls
source-id: 3369
imported: 2026-09-09
-->
# URLs

How the hub turns an address into an article, and how to point an old address
at a new one.

An address matters more on a hub than on an ordinary site, because hub
addresses end up in places you cannot edit later: a methods section, a grant
report, a data availability statement, a link in somebody else's paper. Once
`/about/citing` is in print it has to keep working. This chapter is about
knowing which address a page really has before you publish it, and about
keeping an old one alive after you move things.

Most managers need two things from it. The first is the rule that an
article's URL comes from the menu item pointing at it, not from anything you
typed on the article. The second is the Redirect Manager, for the day
somebody reorganises the site.

> **Note:** This is about articles. Resources, publications, groups, wiki
> pages and the rest build their addresses in their own components, and
> nothing here changes them. The Redirect Manager works on any address on the
> hub, though.

## The SEO settings

Search engine friendly URLs are configured in **Site → Global
Configuration**, on the **Site** tab, under **SEO Settings**.

| Setting | Default | What it does |
|---|---|---|
| **Search Engine Friendly URLs** | Yes | Builds path-style URLs instead of query strings. |
| **Search Engine Friendly Group URLs** | No | Nothing. The setting is stored but no code reads it. |
| **Use URL rewriting** | Yes | Drops `index.php` from the path. Needs the web server's rewrite rules in place first. |
| **Adds Suffix to URL** | No | Appends the document format, so a page ends in `.html`. |
| **Unicode Aliases** | No | Lets aliases hold non-ASCII characters instead of transliterating them. |

These are also listed in the
[global configuration reference](../../reference/configuration/README.md).

Leave these alone. The shipped values are the ones a hub wants: friendly URLs
on, rewriting on, everything else off.

> **Note:** **Use URL rewriting** is one of the few settings where the form's
> own default and the value a hub is installed with disagree. The field
> declares a default of **No**; the configuration the installer writes sets
> it to **Yes**. The stored value is what your hub is running, so a stock hub
> has rewriting on and serves addresses without `index.php` in them. Only a
> hub whose configuration was written some other way will see **No**.

Rewriting is the one setting here that can take the site down. It works only
while the web server has the matching rewrite rules; turn it on where they
are missing and every page on the hub 404s. That is not an afternoon change
on a live site. If you must change it, change it, load one page, and be ready
to change it straight back.

## How an article's address is decided

Two things can give an article a URL, and the first one wins.

### A menu item

If any menu item points at the article, the article's URL is that menu item's
route: its own alias, with the aliases of its parent menu items in front of
it. This is how the pages that ship with a hub are addressed. The sample
**Terms of Use** article is reached at `/about/terms` because a menu item
with alias `terms`, nested under a menu item with alias `about`, points at
it. The article's own alias, `terms`, and its category, `about`, play no part
— they happen to match here, and often will not.

Menu item routes are the addresses to publish and to link to. See
[Menus](../07-menus.md).

### The category path

An article with no menu item is matched by its category path and its own
alias:

```
/{category-path}/{article-alias}
```

The category path is the nested path of the category the article is in, so an
article with alias `site` in a category `this` nested under a category
`about` answers at `/about/this/site`. Only **published** articles are found
this way.

Two shorthands exist for a single-segment address, `/{alias}`:

- The article is in the **Uncategorised** category. This is the ordinary case
  for a page you want at the top level: file it under Uncategorised and it
  answers at `/{alias}`.
- Every segment of the article's category path is the same word as the
  article's alias. An article `about` in category `about` answers at
  `/about`. The code calls this out as supported for legacy reasons; do not
  build anything new on it.

| Category path | Article alias | URL |
|---|---|---|
| *(Uncategorised)* | `terms` | `/terms` |
| `about` | `site` | `/about/site` |
| `about/this` | `site` | `/about/this/site` |
| `about` | `about` | `/about` |

> **Note:** Older Hubzero documentation described URLs as built from a
> **section**, a category, and an article, with segments collapsing when they
> shared an alias. Hubzero 2.4 has no sections. Categories nest instead, to
> any depth, and the path above is what the router matches.

If nothing matches, the hub falls back to the component router, which
addresses an article as `/content/article/{category-path}/{article-alias}`
and a category as `/content/category/{path}`. Those addresses work, but they
are ugly and no one should be given them.

## Redirects

Suppose the hub has grown and the About material is being split up: the page
that has answered at `/about` for three years becomes `/about/overview`, and
a citation page moves in alongside it. The old address is in a funder report.
A redirect is how you keep it working.

The **Redirect Manager** is the supported way to send one address to another.
Open it at **Site → Maintenance → Routes**.

To catch that address:

1. Go to **Site → Maintenance → Routes** and select **New**.
2. Put the old address in **Source URL** — `/about`.
3. Put the new one in **Destination URL** — `/about/overview`.
4. Set **Response Code** to **301 Moved Permanently**, because the move is
   permanent and you want search engines and citation checkers to learn the
   new address.
5. Save, and make sure the link is enabled. A link with no destination, or a
   disabled one, does nothing.
6. Open `/about` in a browser. It should land on `/about/overview`.

Redirects are entirely reversible: disable the link and the old address goes
back to a 404. Nothing about a redirect touches the article itself.

The manager depends on the **System - Redirect** plugin, and says at the top
of the screen whether that plugin is enabled. With it on, every 404 the site
serves is recorded as a link with no destination, so the manager doubles as a
list of the broken addresses people are actually asking for.

A link has:

| Field | Meaning |
|---|---|
| **Source URL** | The address to catch. Required, and must be unique. The plugin matches the full URL, then the server-relative path with and without a leading slash. |
| **Destination URL** | Where to send them. Required to enable the link. |
| **Response Code** | **404 Not Found**, **301 Moved Permanently**, or **302 Found**. A new link with a destination defaults to 302. |
| **Comment** | A note for whoever reads the list next. |

Enable a link and the redirect takes effect; disable it and the address goes
back to a 404. The toolbar also archives, trashes, and — once you filter to
**Trashed** — permanently deletes links. This screen is one of the well
behaved ones: trashing is reversible and the permanent delete is a separate
button you can only reach after filtering to **Trashed**. See
[States, deleting and check-out](states.md) for why that is worth noticing. Checking several recorded 404s and
using **Update selected links to the following new URL** sets the same
destination on all of them at once.

Its parameters are in the
[Redirect configuration reference](../../reference/configuration/components/redirect.md).

## Redirecting with a menu item

A menu item of type **External URL** also acts as a redirect. When the
requested path matches the menu item's own route exactly, the hub redirects
to the item's **Link**, which may be another page on the hub.

This is worth knowing because it works for paths that belong to a component
rather than to an article. To move a group's audience from `/groups/mainclass`
to `/groups/spring2016class`:

1. Go to **Menus → Menu Manager** and open a menu that is not displayed
   anywhere — the **Default** menu on a stock hub.
2. If the menu has no **Groups** item, add one: **New**, then **Select** next
   to **Menu Item Type**, then **External URL** under **System Links**. Set
   **Menu Title** to `Groups` and **Link** to `groups`. **Save & Close**.
3. Add a second **External URL** item with **Menu Title** `Mainclass` and
   **Link** `/groups/spring2016class`.
4. Set its **Parent Item** to **Groups**, so its route becomes
   `groups/mainclass`. **Save & Close**.

Requests for `/groups/mainclass` now land on `/groups/spring2016class`.

> **Tip:** Prefer the Redirect Manager. It records the redirect where the
> next administrator will look for it, gives you the response code, and does
> not put an entry into a menu that someone may later publish by accident.
