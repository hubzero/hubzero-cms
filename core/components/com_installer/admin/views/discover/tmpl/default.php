<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller));

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller), 'install.png');

Toolbar::custom('discover.install', 'upload', 'upload', 'JTOOLBAR_INSTALL', true, false);
Toolbar::custom('discover.refresh', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_DISCOVER', false, false);
Toolbar::custom('discover.purge', 'purge', 'purge', 'JTOOLBAR_PURGE_CACHE', false, false);
Toolbar::divider();
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('discover');

Html::behavior('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div id="installer-discover">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=discover');?>" method="post" name="adminForm" id="adminForm">
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
					<th class="nowrap"><?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_NAME', 'name', $listDirn, $listOrder); ?></th>
					<th class="center"><?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_TYPE', 'type', $listDirn, $listOrder); ?></th>
					<th class="priority-4 center"><?php echo Lang::txt('JVERSION'); ?></th>
					<th class="priority-5 center"><?php echo Lang::txt('JDATE'); ?></th>
					<th class="priority-3"><?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_FOLDER', 'folder', $listDirn, $listOrder); ?></th>
					<th><?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_CLIENT', 'client_id', $listDirn, $listOrder); ?></th>
					<th class="priority-5 center"><?php echo Lang::txt('JAUTHOR'); ?></th>
					<th class="priority-4"><?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_ID', 'extension_id', $listDirn, $listOrder); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item): ?>
				<tr class="row<?php echo $i%2;?>">
					<td><?php echo Html::grid('id', $i, $item->extension_id); ?></td>
					<td><span class="bold hasTip" title="<?php echo htmlspecialchars($item->name.'::'.$item->description); ?>"><?php echo $item->name; ?></span></td>
					<td class="center"><?php echo Lang::txt('COM_INSTALLER_TYPE_' . $item->type); ?></td>
					<td class="priority-4 center"><?php echo @$item->version != '' ? $item->version : '&#160;'; ?></td>
					<td class="priority-5 center"><?php echo @$item->creationDate != '' ? $item->creationDate : '&#160;'; ?></td>
					<td class="priority-3 center"><?php echo @$item->folder != '' ? $item->folder : Lang::txt('COM_INSTALLER_TYPE_NONAPPLICABLE'); ?></td>
					<td class="center"><?php echo $item->client; ?></td>
					<td class="priority-5 center">
						<span class="editlinktip hasTip" title="<?php echo addslashes(htmlspecialchars(Lang::txt('COM_INSTALLER_AUTHOR_INFORMATION').'::'.$item->author_info)); ?>">
							<?php echo @$item->author != '' ? $item->author : '&#160;'; ?>
						</span>
					</td>
					<td class="priority-4"><?php echo $item->extension_id ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p><?php echo Lang::txt('COM_INSTALLER_MSG_DISCOVER_DESCRIPTION'); ?></p>
		<?php else : ?>
			<p>
				<?php echo Lang::txt('COM_INSTALLER_MSG_DISCOVER_DESCRIPTION'); ?>
			</p>
			<p>
				<?php echo Lang::txt('COM_INSTALLER_MSG_DISCOVER_NOEXTENSION'); ?>
			</p>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo Html::input('token'); ?>
	</form>
</div>
