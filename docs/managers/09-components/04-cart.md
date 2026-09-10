<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/components/cart
source-id: 3374
modified: 2016-07-12
imported: 2026-09-09
-->
# Cart

The Cart component is the transaction half of the hub's online store. It holds
the shopper's basket, the checkout, and the order record; the catalogue of
products and SKUs lives in the [Storefront](19-storefront.md) component. In the
administrator interface Cart is a set of reports: what has been ordered, and
what has been downloaded.

Go to **Components → Shopping Cart**. Two submenu links sit at the top:

- **Software Downloads**
- **Orders**

Every screen needs `core.manage` on `com_cart`.

## Software Downloads

Cart records a row every time someone downloads a purchased software SKU. Two
reports read those rows.

### All downloads

The default screen. Its columns are **Product**, **Downloaded by**, **User
Info**, **EULA**, **Downloaded**, **IP**, and **Status**. Product, Downloaded
by, Downloaded and Status are sortable.

Above the table are a **Search** box, an **All SKUs** drop-down, and **From**
and **To** date fields with an **Update** button. The date window defaults to
the last month. Clicking a user's name, a product, or a SKU in the table adds
that as a filter; a **Clear** button in the table header removes it.

**Download CSV** in the toolbar exports the current date range and sort as
`cart-downloads-<date>(<from>-<to>).csv`, with the columns Downloaded,
Product, SKU, User, Username, User ID, User Details, EULA, IP, and Status.
The export is written in chunks of 5000 rows, so a large report streams
rather than exhausting memory.

### Downloads by SKU

A roll-up: one row per SKU, with **Product**, **SKU**, and **Downloaded** —
the number of download records in the date window. It has its own search and
date filters and its own **Download CSV** button.

### Marking a download inactive

Click the icon in a row's **Status** column to flip that record between
*Active* and *Inactive*.

> **Warning:** The **Publish** and **Unpublish** buttons in the toolbar do
> not work. They post the tasks `publish` and `unpublish`, which the
> downloads controller does not implement — it has `active` and `inactive`
> instead — so an unknown task falls through to the default and the list
> simply redraws. Use the per-row **Status** link, one record at a time.

This matters when a SKU carries download limits. Cart counts only *active*
download records against a limit, so marking an old record inactive gives the
user another download. There are three limits, all set on the SKU or product
in Storefront under **Software**:

| Setting | Where | Effect |
|---|---|---|
| **Downloads Limit per Single User** | SKU | How many active downloads one account may have of this SKU |
| **Total Downloads Limit** | SKU | How many active downloads exist of this SKU across all users |
| **Total Downloads Limit** | Product | The same, counted across every SKU of the product |

A limit of `0` or empty means no limit.

> **Note:** A download record keeps the status it had when it was written.
> Changing a status now does not rewrite the history shown for earlier
> downloads.

## Orders

### All orders

One row per completed transaction: **Order ID**, **Order total**, **Items
ordered**, **Order placed**, **Purchased by**, and **Payment method**. Order
ID, Order placed, Purchased by and Payment method are sortable.

The filters are a **Search** box, an **Order notes** drop-down (*All notes* or
*Only nonempty notes*), and the **From**/**To** date window. **Download CSV**
in the toolbar exports the list.

Click an order ID to open it.

### Viewing an order

The order view shows **Order Details**, **Shipping info**, **Payment info**,
**Items Ordered**, **Notes/Comments**, and a **Changelog** listing every
administrative edit with who made it and when.

**Edit** on the order view opens a form that can change:

- the payment details string,
- the price and quantity of each item ordered,
- the checkout notes on each item, and
- the order's own notes.

Saving records the difference in the order's changelog. Nothing else about a
completed order can be changed from here — there is no order status field and
no way to add or remove items.

### Items ordered

A line-item report across all orders: **SKU ID**, **Product**, **QTY**,
**Price**, **Order ID**, **Order placed**, and **Purchased by**. It has the
same search and date filters, and its own **Download CSV** button.

## Options

Press **Options** in the toolbar of any Cart screen. The full list is in the
[generated parameter reference](../../reference/configuration/components/cart.md);
the ones that matter day to day are:

- **Send notifications to** — a comma-separated list of addresses that get an
  `ORDER NOTIFICATION` mail on every new order. Leave it empty and no
  notification is sent.
- **Send order info from** — the address the shopper's order confirmation
  appears to come from. Must be a valid address or the site's `mailfrom` is
  used instead.
- **Store administrator ID** — the numeric user ID that receives payment
  processing error mail.
- **Transaction TTL** — minutes a pending transaction holds its inventory
  before the items are released back to stock. Default 120.
- The **Payment** fieldset holds the payment provider name, environment, site
  ID, and validation key. Two providers ship with the component: `DUMMY AUTO
  PAYMENT` and `UPAY`.

## A note on permissions

Reaching any Cart screen needs `core.manage` on `com_cart`. Beyond that, the
component's permission handling is thin:

- The toolbar buttons are hidden by checks against the asset names
  `com_cart.download`, `com_cart.orders`, and `com_cart.order`. Only
  `download` is declared in `config/access.xml`, and none of the three has a
  permissions form of its own, so in practice all three inherit the
  component-level rules on the **Options → Permissions** tab.
- The controllers themselves do not repeat those checks. A user who can reach
  the component can activate a download or save an order edit by posting
  directly, whatever the toolbar shows.

Grant `core.manage` on `com_cart` only to people you would also let edit
orders.
