{{--
  Wishlist metadata — displays wish count + "add new wish" link.

  Variables (from plugin):
    $resource   — object: resource model
    $wishlistid — int: wishlist ID
    $items      — int: number of wishes

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $wishlistUrl = Route::url('index.php?option=com_resources&id=' . $resource->id . '&active=wishlist');
  $addWishUrl = Route::url('index.php?option=com_wishlist&id=' . $wishlistid . '&task=add');
@endphp

<p class="wishlist">
  <a href="{{ $wishlistUrl }}">{{ Lang::txt('PLG_RESOURCES_WISHLIST_NUM_WISHES', $items) }}</a>
  (<a href="{{ $addWishUrl }}">{{ Lang::txt('PLG_RESOURCES_WISHLIST_ADD_NEW_WISH') }}</a>)
</p>
