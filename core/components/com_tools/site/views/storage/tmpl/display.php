<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('storage.css');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_TOOLS_STORAGE'); ?></h2>

	<div id="content-header-extra">
		<?php echo $this->monitor; ?>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->exceeded) { ?>
		<p class="warning"><?php echo Lang::txt('COM_TOOLS_ERROR_STORAGE_EXCEEDED'); ?></p>
	<?php } ?>

	<?php if ($this->output) { ?>
		<p class="passed"><?php echo $this->output; ?></p>
	<?php } else if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=storage'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p class="help">
				<strong><?php echo Lang::txt('COM_TOOLS_STORAGE_WHAT_DOES_PURGE_DO'); ?></strong><br />
				<?php echo Lang::txt('COM_TOOLS_STORAGE_WHAT_PURGE_DOES'); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_STORAGE_AUTOMATIC'); ?></legend>
			<div class="grid">
				<div class="col span6">
					<label>
						<?php echo Lang::txt('COM_TOOLS_STORAGE_CLEAN_UP_DISCK_SPACE'); ?>
						<select name="degree">
							<option value="default"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_MINIMALLY'); ?></option>
							<option value="olderthan1"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_DAY'); ?></option>
							<option value="olderthan7"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_WEEK'); ?></option>
							<option value="olderthan30"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_MONTH'); ?></option>
							<option value="all"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_ALL'); ?></option>
						</select>
					</label>
				</div>
				<div class="col span6 omega">
					<label>
						<br />
						<input type="submit" class="option" name="action" value="Purge" />
					</label>
				</div>
			</div>
			<p class="hint"><?php echo Lang::txt('COM_TOOLS_STORAGE_AUTOMATIC_HINT'); ?></p>
		</fieldset>
		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_STORAGE_MANUAL'); ?></legend>
			<div class="filebrowser field-wrap">
				<?php echo Lang::txt('COM_TOOLS_STORAGE_BROWSE_STORAGE'); ?>
				<iframe src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component'); ?>" name="filer" id="filer" width="98%" height="500" border="0" frameborder="0"></iframe>
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="purge" />

			<?php echo Html::input('token'); ?>
		</fieldset><div class="clear"></div>
	</form>
</section><!-- / .main section -->
