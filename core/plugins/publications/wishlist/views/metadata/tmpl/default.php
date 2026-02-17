<?php

// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>

<p class="wishlist">
    <a href="<?php echo Route::url($this->publication->link('wishlist')); ?>">
        <?php echo Lang::txt('NUM_WISHES', $this->items); ?>
    </a>
    (<a href="<?php echo Route::url('index.php?option=com_wishlist&id=' . $this->wishlistid . '&task=add'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WISHLIST_ADD_NEW_WISH'); ?></a>)
</p>