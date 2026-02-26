<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

?>

<?php
$wishlistUrl = Route::url(
    'index.php?option=com_resources&id=' . $this->resource->id . '&active=wishlist'
);
$addWishUrl = Route::url(
    'index.php?option=com_wishlist&id=' . $this->wishlistid . '&task=add'
);
?>
<p class="wishlist">
    <a href="<?php echo $wishlistUrl; ?>">
        <?php echo Lang::txt('PLG_RESOURCES_WISHLIST_NUM_WISHES', $this->items); ?>
    </a>
    (<a href="<?php echo $addWishUrl; ?>"><?php echo Lang::txt('PLG_RESOURCES_WISHLIST_ADD_NEW_WISH'); ?></a>)
</p>