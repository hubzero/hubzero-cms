<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();
?>


<form action="<?php echo Route::url('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="width-100">
		<h3 class="title fltlft">
			<?php echo Lang::txt('COM_TEMPLATES_SITE_PREVIEW'); ?>
		</h3>
		<h3 class="fltrt">
			<a href="<?php echo $this->url.'index.php?tp='.$this->tp.'&amp;template='.$this->id; ?>" target="_blank"><?php echo Lang::txt('JBROWSERTARGET_NEW'); ?></a>
		</h3>
		<div class="clr"></div>
		<div class="width-100 temprev">
			<iframe src="<?php echo $this->url.'index.php?tp='.$this->tp.'&amp;template='.$this->id; ?>" name="previewframe" class="previewframe"></iframe>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="template" value="<?php echo $this->template; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="client" value="<?php echo $this->client->id;?>" />
	<?php echo Html::input('token'); ?>
</form>
