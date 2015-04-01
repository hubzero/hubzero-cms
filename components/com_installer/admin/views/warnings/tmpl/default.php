<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

// no direct access
defined('_JEXEC') or die;
?>
<div id="installer-warnings">
	<form action="<?php echo Route::url('index.php?option=com_installer&view=warnings'); ?>" method="post" name="adminForm" id="adminForm">
		<?php
		if (!count($this->messages))
		{
			echo '<p class="nowarning">' . Lang::txt('COM_INSTALLER_MSG_WARNINGS_NONE') . '</p>';
		}
		else
		{
			echo JHtml::_('sliders.start', 'warning-sliders', array('useCookie' => 1));
			foreach ($this->messages as $message)
			{
				echo JHtml::_('sliders.panel', $message['message'], str_replace(' ', '', $message['message']));
				echo '<div style="padding: 5px;">' . $message['description'] . '</div>';
			}
			echo JHtml::_('sliders.panel', Lang::txt('COM_INSTALLER_MSG_WARNINGFURTHERINFO'), 'furtherinfo-pane');
			echo '<div style="padding: 5px;" >'. Lang::txt('COM_INSTALLER_MSG_WARNINGFURTHERINFODESC') .'</div>';
			echo JHtml::_('sliders.end');
		}
		?>
		<div class="clr"></div>

		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
