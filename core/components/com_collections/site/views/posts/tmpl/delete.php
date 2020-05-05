<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&task=delete&entry='.$this->entry->id); ?>" method="post" id="hubForm">
		<div class="explaination">
<?php if ($this->authorized) { ?>
			<p><a class="add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&task=new'); ?>"><?php echo Lang::txt('New entry'); ?></a></p>
<?php } ?>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_HEADER'); ?></legend>

	 		<p class="warning"><?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_WARNING', $this->entry->title); ?></p>

			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" />
				<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="task" value="delete" />
		<input type="hidden" name="entry" value="<?php echo $this->entry->id; ?>" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?>" />
			<a href="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&scope='.Date::of($this->entry->publish_up)->toLocal('Y').'/'.Date::of($this->entry->publish_up)->toLocal('m').'/'.$this->entry->alias); ?>"><?php echo Lang::txt('Cancel'); ?></a>
		</p>
	</form>
