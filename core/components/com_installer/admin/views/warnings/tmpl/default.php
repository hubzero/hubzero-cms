<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->getName()), 'install');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('warnings');

Document::setTitle(Lang::txt('COM_INSTALLER_TITLE_' . $this->getName()));
?>
<div id="installer-warnings">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=warnings'); ?>" method="post" name="adminForm" id="item-form">
		<?php
		if (!count($this->messages))
		{
			echo '<p class="nowarning">' . Lang::txt('COM_INSTALLER_MSG_WARNINGS_NONE') . '</p>';
		}
		else
		{
			echo Html::sliders('start', 'warning-sliders', array('useCookie' => 1));
			foreach ($this->messages as $message)
			{
				echo Html::sliders('panel', $message['message'], str_replace(' ', '', $message['message']));
				echo '<div class="warning">' . $message['description'] . '</div>';
			}
			echo Html::sliders('panel', Lang::txt('COM_INSTALLER_MSG_WARNINGFURTHERINFO'), 'furtherinfo-pane');
			echo '<div class="warning">'. Lang::txt('COM_INSTALLER_MSG_WARNINGFURTHERINFODESC') .'</div>';
			echo Html::sliders('end');
		}
		?>
		<div class="clr"></div>

		<input type="hidden" name="boxchecked" value="0" />
		<?php echo Html::input('token'); ?>
	</form>
</div>
