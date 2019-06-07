<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER'), 'thememanager');
Toolbar::custom('edit', 'back.png', 'back_f2.png', 'Back', false, false);

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<div class="grid">
		<div class="col span6">
			<h3 class="title">
				<?php echo Lang::txt('COM_TEMPLATES_SITE_PREVIEW'); ?>
			</h3>
		</div>
		<div class="col span6">
			<h3>
				<a href="<?php echo $this->url.'index.php?tp='.$this->tp.'&amp;template='.$this->id; ?>" rel="noopener" target="_blank"><?php echo Lang::txt('JBROWSERTARGET_NEW'); ?></a>
			</h3>
		</div>
	</div>
	<div class="temprev">
		<iframe src="<?php echo $this->url.'index.php?tp='.$this->tp.'&amp;template='.$this->id; ?>" name="previewframe" class="previewframe"></iframe>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="template" value="<?php echo $this->template; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="client" value="<?php echo $this->client->id;?>" />
	<?php echo Html::input('token'); ?>
</form>
