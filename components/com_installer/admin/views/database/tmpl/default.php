<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller));

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller), 'install.png');
Toolbar::custom('database.fix', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_DATABASE_FIX', false, false);
Toolbar::divider();
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_DATABASE');
?>
<div id="installer-database">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=warnings');?>" method="post" name="adminForm" id="adminForm">
		<?php if ($this->errorCount === 0) : ?>
			<p class="nowarning"><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_OK'); ?></p>
			<?php echo Html::sliders('start', 'database-sliders', array('useCookie'=>1)); ?>

		<?php else : ?>
			<p class="warning"><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_ERRORS'); ?></p>
			<?php echo Html::sliders('start', 'database-sliders', array('useCookie'=>1)); ?>

			<?php $panelName = Lang::txts('COM_INSTALLER_MSG_N_DATABASE_ERROR_PANEL', $this->errorCount); ?>
			<?php echo Html::sliders('panel', $panelName, 'error-panel'); ?>
			<fieldset class="panelform">
				<ul>
					<?php if (!$this->filterParams) : ?>
						<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_FILTER_ERROR'); ?>
					<?php endif; ?>

					<?php if ($this->schemaVersion != $this->changeSet->getSchema()) : ?>
						<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_SCHEMA_ERROR', $this->schemaVersion, $this->changeSet->getSchema()); ?></li>
					<?php endif; ?>

					<?php if (version_compare($this->updateVersion, JVERSION) != 0) : ?>
						<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_UPDATEVERSION_ERROR', $this->updateVersion, JVERSION); ?></li>
					<?php endif; ?>

					<?php foreach ($this->errors as $line => $error) : ?>
						<?php $key = 'COM_INSTALLER_MSG_DATABASE_' . $error->queryType;
						$msgs = $error->msgElements;
						$file = basename($error->file);
						$msg0 = (isset($msgs[0])) ? $msgs[0] : ' ';
						$msg1 = (isset($msgs[1])) ? $msgs[1] : ' ';
						$msg2 = (isset($msgs[2])) ? $msgs[2] : ' ';
						$message = Lang::txt($key, $file, $msg0, $msg1, $msg2); ?>
						<li><?php echo $message; ?></li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php endif; ?>

		<?php echo Html::sliders('panel', Lang::txt('COM_INSTALLER_MSG_DATABASE_INFO'), 'furtherinfo-pane'); ?>
			<fieldset class="panelform">
			<ul>
				<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_SCHEMA_VERSION', $this->schemaVersion); ?></li>
				<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_UPDATE_VERSION', $this->updateVersion); ?></li>
				<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_DRIVER', JFactory::getDbo()->name); ?></li>
				<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_CHECKED_OK', count($this->results['ok'])); ?></li>
				<li><?php echo Lang::txt('COM_INSTALLER_MSG_DATABASE_SKIPPED', count($this->results['skipped'])); ?></li>
			</ul>
			</fieldset>
		<?php echo Html::sliders('end'); ?>

		<div class="clr"> </div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo Html::input('token'); ?>
	</form>
</div>
