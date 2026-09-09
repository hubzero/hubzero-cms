<!--
status: generated
source: Event::trigger('wishlist.*') call sites and core/plugins/wishlist/
-->

# Wishlist events

Events in the `wishlist` group. A plugin in `core/plugins/wishlist/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `wishlist.onWishlistAfterDelete`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:261`](../../../core/components/com_wishlist/admin/controllers/lists.php#L261) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterDeleteComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:304`](../../../core/components/com_wishlist/admin/controllers/comments.php#L304) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterDeleteWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:423`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L423) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSave`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:209`](../../../core/components/com_wishlist/admin/controllers/lists.php#L209) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSaveComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:256`](../../../core/components/com_wishlist/admin/controllers/comments.php#L256) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSaveWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:375`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L375) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSave`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:193`](../../../core/components/com_wishlist/admin/controllers/lists.php#L193) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSaveComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:240`](../../../core/components/com_wishlist/admin/controllers/comments.php#L240) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSaveWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:331`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L331) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.
