<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<li class="blog-entry">
	<p class="title">
		<a href="<?php echo Route::url($this->entry->link()); ?>"><?php echo $this->escape(stripslashes($this->entry->get('title'))); ?></a>
	</p>
	<p class="details">
		<?php echo $this->entry->published('date'); ?>
		<span>|</span>
		<?php echo Lang::txt('PLG_TAGS_BLOGS_POSTED_BY', '<cite><a href="' . Route::url('index.php?option=com_members&id=' . $this->entry->get('created_by')) . '">' . $this->escape(stripslashes($this->entry->creator->get('name'))) . '</a></cite>'); ?>
	</p>
	<?php if ($content = \Hubzero\Utility\Str::truncate(strip_tags($this->entry->content()), 200)) { ?>
		<p><?php echo $content; ?></p>
	<?php } ?>
	<p class="href">
		<?php echo rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->entry->link()), '/'); ?>
	</p>
</li>
