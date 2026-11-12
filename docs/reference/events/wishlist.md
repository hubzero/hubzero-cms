<!--
status: generated
source: Event::trigger('wishlist.*') call sites and core/plugins/wishlist/
-->

# Wishlist events

Events in the `wishlist` group. A plugin in `core/plugins/wishlist/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `wishlist.onWishlistAfterDelete`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:258`](../../../core/components/com_wishlist/admin/controllers/lists.php#L258) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterDeleteComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:296`](../../../core/components/com_wishlist/admin/controllers/comments.php#L296) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterDeleteWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:420`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L420) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSave`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:208`](../../../core/components/com_wishlist/admin/controllers/lists.php#L208) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSaveComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:251`](../../../core/components/com_wishlist/admin/controllers/comments.php#L251) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSaveWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:376`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L376) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSave`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:190`](../../../core/components/com_wishlist/admin/controllers/lists.php#L190) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSaveComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:233`](../../../core/components/com_wishlist/admin/controllers/comments.php#L233) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSaveWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:327`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L327) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.
