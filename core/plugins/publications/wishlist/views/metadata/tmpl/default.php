<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<p class="wishlist">
	<a href="<?php echo Route::url($this->publication->link('wishlist')); ?>">
		<?php echo Lang::txt('NUM_WISHES', $this->items); ?>
	</a>
	(<a href="<?php echo Route::url('index.php?option=com_wishlist&id=' . $this->wishlistid . '&task=add'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WISHLIST_ADD_NEW_WISH'); ?></a>)
</p>