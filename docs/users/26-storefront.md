<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/users/storefront
-->
# Storefront

The storefront is the hub's store. Hubs use it to distribute licensed
software, to sell course seats and memberships, and occasionally to ship
physical goods. It is at `/storefront`, and the cart and your order history
are at `/cart`.

> **Note:** Most hubs do not have one. The storefront ships switched off, and
> an administrator has to turn it on and stock it before there is anything to
> buy. If `/storefront` gives you a page-not-found, your hub does not sell
> anything, and nothing on this page applies to you. Everything else on a hub
> — tools, publications, groups, courses — is free to members whether or not
> a store exists.

Where a hub does run one, it is usually for the things it cannot simply give
away: a solver whose licence is counted per seat, a training course with a
registration fee, a data subscription. A department buying five seats of a
licensed code for a semester goes through the storefront and gets the serial
numbers back on the confirmation page.

Many hubs require you to log in before you can see the store at all. Where
that is the case, `/storefront` shows a welcome page with a **Login** button
instead of the catalogue.

## Finding something

The store's home page lists the collections the hub has set up — usually
categories of software. Click one to see its products, or type words into the
search box and press **Search** to match product titles. Searching from
inside a collection searches only that collection.

A product page shows its picture, its price or price range, a tagline,
a description, and often a features list. Its address,
`https://<your hub>/storefront/product/<name>`, is stable and shareable. Hub
staff can also give you a link that arrives with the options already chosen.

You will not see every product the hub has. Products are limited by access
level or by the groups your account belongs to, and individual variants can
be restricted to a named list of people. What you cannot buy simply is not
listed.

## Adding an item to the cart

1. Choose an option under each heading on the product page — a platform, a
   licence term, whatever the hub has defined. As you choose, the price
   updates to the exact variant you have selected.
2. Set the quantity if the product allows more than one. Some hubs rename
   this field; some products can only ever be bought one at a time.
3. Press **Add to cart**. The hub takes you to `/cart`.

If the product is out of stock, has been withdrawn, or you already own it,
the page says so in place of the **Add to cart** button.

## The cart

`/cart` lists what you have collected, with a quantity box and a **delete**
link for each line. Change a quantity and press **Update cart** to apply it;
setting a quantity to zero removes the line. If the hub issues discount
codes, type one into the coupon box and press **Apply**.

Press **Checkout** to start. You must be logged in — the hub asks you to sign
in first if you are not, and your cart survives the login.

## Checking out

Checkout is a series of steps, and the hub shows only the ones your order
needs.

- **Checkout: user agreement.** If anything in the order carries an end-user
  licence, its text appears here. Tick the box to accept it and press
  **Next**. You cannot go on without accepting.
- **Checkout: Notes/Comments.** Some items ask a question — a purchase order
  number, a department, an intended use. Answer it and press **Next**. Where
  the question is marked required, an answer is required.
- **Checkout: shipping information.** For physical goods, enter the name and
  address to ship to, or choose one you saved earlier, and press **Next**.
  Tick **Save this address for future use** to keep it.
- **Review your order.** The full order, with subtotal, shipping, discounts
  and total, plus the notes you entered. Press **Place order** to confirm.

An order with a balance to pay hands you to the hub's payment provider at
this point and returns you when the payment clears.

When it is done you land on a **Thank you!** page with an order summary, and
the hub emails a confirmation to the address on your account.

## Getting what you bought

Nothing is posted to you unless you bought a physical thing. Software,
course seats and memberships are delivered on the hub, straight away.

The summary on the confirmation page carries the action for each item.

- A software download has a **Download** link. Where the hub issues licence
  keys, your serial number or numbers are printed beside it.
- A course seat has a link to the course page; you are already registered.
- A membership or subscription is applied to your account straight away.

## Your orders

Press **Your Orders** in the cart, or go to `/cart/orders`, for every order
you have placed, newest first, each with its number, date, items and total.
Download links stay live here, so you can fetch a file again later.

> **Note:** Downloads can be capped. A hub may limit how many times one
> person may download a file, or how many times it may be downloaded in
> total. Once a cap is reached the link stops working, and you have to ask
> the hub's support staff.

You have to be logged in to see your orders. See
[Support](12-support.md) if an order or a download does not behave as it should.
