<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/managers/components/storefront
-->
# Storefront

The storefront is the hub's catalogue: the products people browse, the
purchasable variants of each product, and the collections that group them. It
holds no cart and no orders — a shopper's basket, checkout and order history
belong to the [Cart](06-cart.md) component, which the storefront hands off to.
This chapter covers the administrator's side; the
[Hub users](../../users/26-storefront.md) book covers shopping.

Most hubs never open this component. It is off out of the box — the row
written at install time leaves both Storefront and Cart disabled — and a hub
that publishes datasets, runs tools and hosts groups has no use for either.
Turn it on only when the hub genuinely sells something: a licensed code that
members download after paying, a paid workshop place, a piece of hardware.
If you are here because you want to *give* members a file, that is
[Resources](29-resources.md) or [Publications](27-publications.md), not this.

## How these four components relate

Four components in this book deal with money, and they are two unrelated
pairs. Knowing which is which saves an afternoon.

| If you want | You need | And also |
|---|---|---|
| To sell products to members | **Storefront** — the catalogue | **[Cart](06-cart.md)** — the basket, the checkout and the order record. Neither works alone; the storefront's product page calls into Cart to add an item, and Cart's screens read the catalogue back out of Storefront. Enable both or neither. |
| To run a job board where employers pay | **[Jobs](18-jobs.md)** — the board | **[Services](32-services.md)** — the employer subscriptions the board sells. Services exists only for Jobs; nothing else reads it. |

The two pairs share no code, no tables and no checkout. Storefront and Cart
do not sell a job-board subscription, and Services cannot sell a product.
Enabling the store does not give you the job board, and vice versa.

Open it under **Components > Storefront**. Three sub-menu links sit at the
top: **Products**, **Collections**, and **Option Groups**. Four more
screens — SKUs, options, restrictions and serial numbers — are reached from
inside those.

> **Note:** If **Storefront** is not in the **Components** menu, it is still
> disabled. Enable it — and Cart — under **Extensions > Extension Manager >
> Manage** before anything on this page applies.

Some vocabulary. A **product** is the thing you sell, with a description and
an image. An **option group** is a choice a buyer makes (Platform, License
term); an **option** is one answer (Windows, Linux). A **SKU** is one
purchasable combination of options, and carries the price, the inventory and,
for software, the file. A **collection** is a category the store's front page
and browse pages are built from.

## Products

The list shows **Title**, **Alias**, **Type**, **SKUs (published)**,
**State**, and **Access**. Click a heading to sort. Filter by a search term
and by product type. The SKU cell links to that product's SKUs and carries a
`[ + ]` link that adds one. Click the state to publish or unpublish.

The toolbar has **New**, **Edit**, **Delete**, **Publish**, **Unpublish**,
**Options**, and **Help**. Delete asks for confirmation on a second screen
and is permanent.

### Creating or editing a product

| Field | Notes |
|---|---|
| Title | Required. |
| Alias | The last segment of the product's URL. A product with no alias is addressed by its numeric ID. |
| Tagline | Required. A one-line summary shown above the description. |
| Description | Required. The main body, in the editor. |
| Features | Optional second body block, shown below the description. |
| Type | The product type, from the `#__storefront_product_types` table. Only **Software Download** has extra settings; see below. |
| Allow multiple | Whether a buyer may put more than one in the cart. |
| Quantity text | Replaces the word "Quantity" on this product's page. Falls back to the component option, then to "Quantity". |
| State | Unpublished or Published. |
| Start Publishing, Finish Publishing | Optional window during which the product is visible. |
| Access Level, or access groups | See [Access](#access) below. |
| Collections | Checkboxes for every published collection. A product may be in several. |
| Product option groups | Checkboxes for the option groups this product's SKUs choose from. |
| Image | Drag-and-drop upload, available only after the product is saved once. |

Save the product before adding a type-related setting, a SKU, or a picture;
the form says as much until the record exists.

For a **Software Download** product, an **Edit type-related options** link
opens a meta screen with **Is EULA Required?**, the **EULA** text itself
(which a SKU can override), and a **Total Downloads Limit** across all
buyers. That screen also reports how many times the product has been
downloaded.

### Access

Which shoppers may buy a product is controlled one of two ways, chosen by the
**Product Access Control** option.

With **Access levels** (the default), the product carries one ordinary
Hubzero access level and the list shows it in the **Access** column.

With **Access groups**, the product's edit form instead offers two group
trees: *User is one of the following* and *User is not one of the
following*. Checking several groups combines them with OR, and the list
column reads "User is: …" and "User is not: …". This is the mode hubs use to
key licences to institutional attributes supplied by an authentication
plugin.

## SKUs

Reach a product's SKUs from the **SKUs (published)** column. The list shows
**Title**, **State**, and **Restrictions**, with the same publish, edit,
delete and new toolbar.

| Field | Notes |
|---|---|
| Title | Required. The SKU name, shown in the cart and on orders. |
| Price | Required. |
| Weight | Shown only where shipping applies. |
| Checkout notes/comments message | A prompt shown to the buyer during checkout. |
| Checkout notes/comments required | Whether the buyer must answer it. |
| Product options | One menu per option group on the parent product. Saving fails if another SKU of the product already has the identical set of options; a product with no option groups can have only one published SKU. |
| Allow multiple | Whether more than one of this SKU may be bought at a time. Saving fails unless the parent product also allows it. |
| Track Inventory, Inventory | Count down stock as SKUs sell. Hidden when serial-number management is on, because serials are then the inventory. |
| Inventory notification threshold | Warn when stock falls to this level. |
| State | Unpublished or Published. |
| Start Publishing, Finish Publishing | Optional visibility window. |
| Restrict by users | Turns on the per-username permit list. |

The information panel gives the SKU's ID, its parent product, its download
count, and a **Direct URL** — a shareable link straight to the product page
with this SKU's options preselected.

For software SKUs, a second block adds a per-SKU **EULA** that overrides the
product's, the **Download file** name (required; the file itself is placed on
the server under the configured download folder, not uploaded here), a
**Total Downloads Limit**, and a **Downloads Limit per Single User**.

### Serial numbers

**Serial Number Management** on a software SKU offers **No management**,
**Single Universal Number** (one key given to every buyer, typed into
**Single Serial Number**), or **Multiple Unique Numbers**. Choose the last,
save, and a **Manage multiple serial numbers** link appears: add numbers one
at a time or upload a CSV. Each number is handed out once and then marked
used, which is why inventory tracking is taken over automatically. Set an
inventory notification threshold so you hear about a shrinking pool.

### Restrictions and whitelist

Setting **Restrict by users** to Yes and saving reveals a **Manage
restrictions** link. There you add hub usernames, one list per SKU, by typing
them or uploading a CSV; only those people may buy that SKU. A username that
does not yet exist on the hub is stored anyway and attached to the account
the first time that person logs in.

**Manage whitelist** is the opposite: an address on it gets the SKU
regardless of every other access control or restriction.

## Putting one product on sale

A lab wants to sell a licensed analysis code as a download, one price, no
options. That is the smallest useful thing the store does, and it takes both
halves of the component.

1. Put the file on the server, in the download folder named in **Options**.
   It is not uploaded through this interface, and a SKU that names a file
   which is not there saves happily and fails at the download.
2. **Components > Storefront > Products**, then **New**. Fill in **Title**,
   **Tagline** and **Description** — all three are required — set **Type**
   to *Software Download*, and press **Save**. Leave **State** at
   *Unpublished* for now.
3. Reopen the product. The image uploader and the type-related settings only
   appear once the record exists. Add the picture, and open **Edit
   type-related options** to set the EULA if the licence needs one.
4. Set **Access Level**, or the group trees if the hub is in access-group
   mode. This decides who can buy it, so get it right before publishing;
   see [Access](#access).
5. Tick the **Collections** the product belongs to. A product in no
   collection is reachable by its URL and by search, but appears on no
   browse page.
6. Save, then follow the **SKUs (published)** link and create one SKU: a
   **Title**, the **Price**, and under the software block the **Download
   file** name. With no option groups on the product, this SKU is the only
   one it can have.
7. Publish the SKU, then publish the product. Then check the store page from
   a test account or a private window rather than from your own session:
   the catalogue is filtered by the viewer's access levels, and yours are
   almost certainly wider than a member's.

> **Warning:** Publishing is the moment the product becomes buyable, and
> money is the one thing on a hub you cannot quietly undo. Do the access
> level, the price and the EULA before step 7, not after. Unpublishing later
> stops new sales but does not touch orders already placed, which live in
> [Cart](06-cart.md) and cannot be reversed from there either.

Adding options later — a platform choice, a licence term — means adding an
option group, ticking it on the product, and giving every combination its own
SKU. Existing orders are unaffected; the catalogue changes underneath them.

## Collections

Collections are the categories on the store's front page. The list shows
**Title**, **Alias**, **Type**, and **Published**. A collection has only a
**Title**, an **Alias** (both required), a published state, and an image.
Every collection is saved with the type `category`.

Products join collections from the product's own edit form, not from here.

## Option groups and options

The **Option Groups** list shows **Title**, **Options (published)**, and
**Published**. A group has a title and a published state; the options count
links to that group's options, where each option likewise has just a title
and a published state.

An unpublished option or option group disappears from the product page, which
can leave a SKU unreachable — the SKU is still there, but no combination of
visible options selects it.

## The store on the site

The store lives at `/storefront`. Its home page lists the top-level
collections; `/storefront/browse/<collection>` lists a collection's products;
`/storefront/product/<alias>` is a product page; `/storefront/search`
searches product titles. Every page carries a link to the cart.

Set **Require Login** to Yes to close the store to guests. A guest is then
sent to `/storefront/overview`, which shows either a plain "log in to see the
store" page or, if you give **Landing page ID** the numeric ID of a content
article, that article with a **Login** button. Set that article's access to
private so it cannot be read directly.

## Options and permissions

**Options** holds the download and image folders, whether login is required,
the landing page ID, the product access control mode, and the quantity label.
Every option is listed in the
[configuration reference](../../reference/configuration/components/storefront.md).

> **Note:** The folder that holds collection images is not among the options.
> The code always reads it as `collectionsImagesFolder`, which no setting
> defines, so collection images are served from `/site/storefront/collections`
> whatever else you configure.

The **Permissions** tab sets who may configure the component (`core.admin`),
reach its administrator screens (`core.manage`), and create, delete, edit,
change the state of, or edit their own entries. `access.xml` also declares
per-product, per-SKU, per-collection, per-option-group and per-option
sections, but the screens all evaluate permissions at the component level.
