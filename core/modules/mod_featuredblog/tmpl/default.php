<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError())
{
	?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDBLOG_MISSING_CLASS'); ?></p>
	<?php
}
elseif ($this->row)
{
	$base = rtrim(Request::base(true), '/');
	?>
	<div class="<?php echo $this->cls; ?>">
		<p class="featured-img">
			<a href="<?php echo Route::url($row->link()); ?>">
				<img width="50" height="50" src="<?php echo $base; ?>/core/modules/mod_featuredblog/assets/img/blog_thumb.gif" alt="<?php echo $this->escape(stripslashes($row->get('title'))); ?>" />
			</a>
		</p>
		<p>
			<a href="<?php echo Route::url($row->link()); ?>">
				<?php echo $this->escape(stripslashes($row->get('title'))); ?>
			</a>:
			<?php if ($row->get('content')) { ?>
				<?php echo \Hubzero\Utility\Str::truncate(strip_tags($row->content()), $this->txt_length); ?>
			<?php } ?>
		</p>
	</div>
	<?php
}
