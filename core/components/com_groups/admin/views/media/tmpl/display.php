<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->css('media.css')
	->js('media.js');
?>

<div id="attachments">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=component&controller=' . $this->controller . '&gidNumber=' . $this->group->get('gidNumber') . '&task=upload'); ?>" id="adminForm" method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="grid">
				<div class="col span4">
					<div class="input-wrap">
						<input type="file" name="upload" id="upload" />
					</div>
				</div>
				<div class="col span4">
					<div class="input-wrap">
						<input type="text" name="foldername" id="foldername" placeholder="<?php echo Lang::txt('COM_GROUPS_MEDIA_CREATE_DIRECTORY'); ?>" />
					</div>
				</div>
				<div class="col span4">
					<div class="input-wrap">
						<input type="submit" value="<?php echo Lang::txt('COM_GROUPS_MEDIA_ACTION_UPLOAD'); ?>" />
					</div>
				</div>
			</div>

			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="gidNumber" value="<?php echo $this->escape($this->group->get('gidNumber')); ?>" />
			<input type="hidden" name="dir" id="currentdir" value="<?php echo $this->escape(urlencode($this->dir)); ?>" />
			<input type="hidden" name="tmpl" value="component" />

			<?php echo Html::input('token'); ?>
		</fieldset>

		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<div id="themanager" class="manager">
			<div class="input-wrap">
				<label for="dir">
					<?php echo Lang::txt('COM_GROUPS_MEDIA_DIRECTORY'); ?>
					<?php echo $this->dirPath; ?>
				</label>
			</div>

			<iframe src="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=component&controller=' . $this->controller . '&gidNumber=' . $this->group->get('gidNumber') . '&task=list' . ($this->dir ? '&dir=' . $this->dir : '') . '&t=' . Date::toUnix()); ?>" name="filer" id="filer" width="98%" height="400"></iframe>
		</div>
	</form>
</div>
