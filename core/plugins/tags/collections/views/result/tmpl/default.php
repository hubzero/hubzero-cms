<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<li class="collections-entry">
	<p class="title">
		<a href="<?php echo Route::url($this->entry->link()); ?>">
			<?php if ($title = $this->entry->get('title')) { ?>
				<?php echo $this->escape(stripslashes($title)); ?>
			<?php } else { ?>
				<?php echo Lang::txt('PLG_TAGS_COLLECTIONS_POST_NUM', $this->entry->get('id')); ?>
			<?php } ?>
		</a>
	</p>
	<p class="details">
		<?php echo Lang::txt('PLG_TAGS_COLLECTIONS'); ?>
		<span>|</span>
		<?php echo $this->entry->created('date'); ?>
		<span>|</span>
		<?php echo Lang::txt('PLG_TAGS_COLLECTIONS_POSTED_BY', '<cite><a href="' . Route::url('index.php?option=com_members&id=' . $this->entry->get('created_by')) . '">' . $this->escape(stripslashes($this->entry->creator('name'))) . '</a></cite>'); ?>
	</p>
	<?php if ($content = $this->entry->description('clean', 200)) { ?>
		<p><?php echo $content; ?></p>
	<?php } ?>
	<p class="href">
		<?php echo rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->entry->link()), '/'); ?>
	</p>
</li>