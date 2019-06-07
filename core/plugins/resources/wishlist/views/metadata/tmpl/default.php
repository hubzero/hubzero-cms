<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<p class="wishlist">
	<a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->resource->id . '&active=wishlist'); ?>">
		<?php echo Lang::txt('PLG_RESOURCES_WISHLIST_NUM_WISHES', $this->items); ?>
	</a>
	(<a href="<?php echo Route::url('index.php?option=com_wishlist&id=' . $this->wishlistid . '&task=add'); ?>"><?php echo Lang::txt('PLG_RESOURCES_WISHLIST_ADD_NEW_WISH'); ?></a>)
</p>