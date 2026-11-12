<!--
status: generated
source: Event::trigger('wishlist.*') call sites and core/plugins/wishlist/
-->

# Wishlist events

Events in the `wishlist` group. A plugin in `core/plugins/wishlist/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `wishlist.onWishlistAfterDelete`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:260`](../../../core/components/com_wishlist/admin/controllers/lists.php#L260) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterDeleteComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:298`](../../../core/components/com_wishlist/admin/controllers/comments.php#L298) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterDeleteWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:422`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L422) with `[$id]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSave`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:210`](../../../core/components/com_wishlist/admin/controllers/lists.php#L210) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSaveComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:253`](../../../core/components/com_wishlist/admin/controllers/comments.php#L253) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistAfterSaveWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:378`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L378) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSave`

Fired from:

- [`core/components/com_wishlist/admin/controllers/lists.php:192`](../../../core/components/com_wishlist/admin/controllers/lists.php#L192) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSaveComment`

Fired from:

- [`core/components/com_wishlist/admin/controllers/comments.php:235`](../../../core/components/com_wishlist/admin/controllers/comments.php#L235) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wishlist.onWishlistBeforeSaveWish`

Fired from:

- [`core/components/com_wishlist/admin/controllers/wishes.php:329`](../../../core/components/com_wishlist/admin/controllers/wishes.php#L329) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.
