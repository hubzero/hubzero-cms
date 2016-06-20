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
if ($canDo->get('core.edit.state'))
{
	Toolbar::publish('manage.publish', 'JTOOLBAR_ENABLE', true);
	Toolbar::unpublish('manage.unpublish', 'JTOOLBAR_DISABLE', true);
	Toolbar::divider();
}
Toolbar::custom('manage.refresh', 'refresh', 'refresh', 'JTOOLBAR_REFRESH_CACHE', true);
Toolbar::divider();
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'manage.remove', 'JTOOLBAR_UNINSTALL');
	Toolbar::divider();
}
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('manage');

Html::behavior('multiselect');
Html::behavior('tooltip');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div id="installer-manage">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=manage');?>" method="post" name="adminForm" id="adminForm">
		<?php if ($this->showMessage) : ?>
			<?php echo $this->loadTemplate('message'); ?>
		<?php endif; ?>

		<?php if ($this->ftp) : ?>
			<?php echo $this->loadTemplate('ftp'); ?>
		<?php endif; ?>

		<?php echo $this->loadTemplate('filter'); ?>

		<?php if (count($this->items)) : ?>
		<table class="adminlist">
			<thead>
				<tr>
					<th>
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="nowrap">
						<?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_NAME', 'name', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-2">
						<?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_LOCATION', 'client_id', $listDirn, $listOrder); ?>
					</th>
					<th class="center">
						<?php echo Html::grid('sort', 'JSTATUS', 'status', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-3">
						<?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_TYPE', 'type', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-4 center">
						<?php echo Lang::txt('JVERSION'); ?>
					</th>
					<th class="priority-5">
						<?php echo Lang::txt('JDATE'); ?>
					</th>
					<th class="priority-5">
						<?php echo Lang::txt('JAUTHOR'); ?>
					</th>
					<th class="priority-4">
						<?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_FOLDER', 'folder', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-4">
						<?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_ID', 'extension_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="11">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item): ?>
				<tr class="row<?php echo $i%2; if ($item->status == 2) echo ' protected';?>">
					<td>
						<?php echo Html::grid('id', $i, $item->extension_id); ?>
					</td>
					<td>
						<span class="bold hasTip" title="<?php echo htmlspecialchars($item->name.'::'.$item->description); ?>">
							<?php echo $item->name; ?>
						</span>
					</td>
					<td class="priority-2 center">
						<?php echo $item->client; ?>
					</td>
					<td class="center">
						<?php if (!$item->element) : ?>
						<strong>X</strong>
						<?php else : ?>
							<?php echo Html::manage('state', $item->enabled, $i, $item->enabled == 1, 'cb'); ?>
						<?php endif; ?>
					</td>
					<td class="priority-3 center">
						<?php echo Lang::txt('COM_INSTALLER_TYPE_' . $item->type); ?>
					</td>
					<td class="priority-4 center">
						<?php echo @$item->version != '' ? $item->version : '&#160;'; ?>
						<?php if ($item->system_data) : ?>
							<?php if ($tooltip = $this->createCompatibilityInfo($item->system_data)) : ?>
								<?php echo Html::behavior('tooltip', $tooltip, Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_TITLE')); ?>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td class="priority-5 center">
						<?php echo @$item->creationDate != '' ? $item->creationDate : '&#160;'; ?>
					</td>
					<td class="priority-5 center">
						<span class="editlinktip hasTip" title="<?php echo addslashes(htmlspecialchars(Lang::txt('COM_INSTALLER_AUTHOR_INFORMATION').'::'.$item->author_info)); ?>">
							<?php echo @$item->author != '' ? $item->author : '&#160;'; ?>
						</span>
					</td>
					<td class="priority-4 center">
						<?php echo @$item->folder != '' ? $item->folder : Lang::txt('COM_INSTALLER_TYPE_NONAPPLICABLE'); ?>
					</td>
					<td class="priority-4">
						<?php echo $item->extension_id ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo Html::input('token'); ?>
	</form>
</div>
