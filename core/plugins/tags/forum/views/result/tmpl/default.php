<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$content = \Hubzero\Utility\Str::truncate(strip_tags($this->post->get('comment')), 200);
?>
<li class="forum-entry">
	<p class="title">
		<a href="<?php echo Route::url($this->post->link()); ?>"><?php echo $this->escape(stripslashes($this->post->get('title'))); ?></a>
	</p>
	<p class="details">
		<?php echo Lang::txt('PLG_TAGS_FORUM') . ' &rsaquo; ' . $this->escape(stripslashes($this->post->get('section'))) . ' &rsaquo; ' . $this->escape(stripslashes($this->post->get('category'))); ?>
	</p>
	<?php if ($content) { ?>
		<p><?php echo $content; ?></p>
	<?php } ?>
	<p class="href">
		<?php echo rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->post->link()), '/'); ?>
	</p>
</li>
