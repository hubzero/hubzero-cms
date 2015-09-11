<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller));

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller), 'install.png');
Toolbar::custom('update.update', 'upload', 'upload', 'COM_INSTALLER_TOOLBAR_UPDATE', true, false);
Toolbar::custom('update.find', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_FIND_UPDATES', false, false);
Toolbar::custom('update.purge', 'purge', 'purge', 'JTOOLBAR_PURGE_CACHE', false, false);
Toolbar::divider();
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('update');

Html::behavior('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div id="installer-update">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=update');?>" method="post" name="adminForm" id="adminForm">
		<?php if ($this->showMessage) : ?>
			<?php echo $this->loadTemplate('message'); ?>
		<?php endif; ?>

		<?php if ($this->ftp) : ?>
			<?php echo $this->loadTemplate('ftp'); ?>
		<?php endif; ?>

		<?php if (count($this->items)) : ?>
		<table class="adminlist">
			<thead>
				<tr>
					<th><input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
					<th class="nowrap"><?php echo $this->grid('sort', 'COM_INSTALLER_HEADING_NAME', 'name', $listDirn, $listOrder); ?></th>
					<th class="nowrap"><?php echo $this->grid('sort', 'COM_INSTALLER_HEADING_INSTALLTYPE', 'extension_id', $listDirn, $listOrder); ?></th>
					<th><?php echo $this->grid('sort', 'COM_INSTALLER_HEADING_TYPE', 'type', $listDirn, $listOrder); ?></th>
					<th class="center"><?php echo Lang::txt('JVERSION'); ?></th>
					<th><?php echo $this->grid('sort', 'COM_INSTALLER_HEADING_FOLDER', 'folder', $listDirn, $listOrder); ?></th>
					<th><?php echo $this->grid('sort', 'COM_INSTALLER_HEADING_CLIENT', 'client_id', $listDirn, $listOrder); ?></th>
					<th><?php echo Lang::txt('COM_INSTALLER_HEADING_DETAILSURL'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item):
				$client = $item->client_id ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE');
			?>
				<tr class="row<?php echo $i%2; ?>">
					<td><?php echo Html::grid('id', $i, $item->update_id); ?></td>
					<td>
						<span class="editlinktip hasTip" title="<?php echo Lang::txt('JGLOBAL_DESCRIPTION');?>::<?php echo $item->description ? $this->escape($item->description) : Lang::txt('COM_INSTALLER_MSG_UPDATE_NODESC'); ?>">
						<?php echo $this->escape($item->name); ?>
						</span>
					</td>
					<td class="center">
						<?php echo $item->extension_id ? Lang::txt('COM_INSTALLER_MSG_UPDATE_UPDATE') : Lang::txt('COM_INSTALLER_NEW_INSTALL') ?>
					</td>
					<td><?php echo Lang::txt('COM_INSTALLER_TYPE_' . $item->type) ?></td>
					<td class="center"><?php echo $item->version ?></td>
					<td class="center"><?php echo @$item->folder != '' ? $item->folder : Lang::txt('COM_INSTALLER_TYPE_NONAPPLICABLE'); ?></td>
					<td class="center"><?php echo $client; ?></td>
					<td><?php echo $item->detailsurl ?>
						<?php if (isset($item->infourl)) : ?>
						<br /><a href="<?php echo $item->infourl; ?>"><?php echo $this->escape($item->infourl);?></a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
		<?php else : ?>
			<p class="nowarning"><?php echo Lang::txt('COM_INSTALLER_MSG_UPDATE_NOUPDATES'); ?></p>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo Html::input('token'); ?>
	</form>
</div>