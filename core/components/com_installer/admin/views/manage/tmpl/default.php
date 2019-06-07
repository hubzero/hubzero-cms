<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$canDo = Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller));

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller), 'install');
if ($canDo->get('core.edit.state'))
{
	Toolbar::publish('manage.publish', 'JTOOLBAR_ENABLE', true);
	Toolbar::unpublish('manage.unpublish', 'JTOOLBAR_DISABLE', true);
	Toolbar::divider();
}
Toolbar::custom('manage.refresh', 'refresh', 'refresh', 'JTOOLBAR_REFRESH_CACHE', true);
Toolbar::divider();
/*if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'manage.remove', 'JTOOLBAR_UNINSTALL');
	Toolbar::divider();
}*/
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('manage');

Html::behavior('multiselect');
Html::behavior('tooltip');

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
?>
<div id="installer-manage">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=manage');?>" method="post" name="adminForm" id="adminForm">
		<?php echo $this->loadTemplate('filter'); ?>

		<?php if (count($this->rows)) : ?>
		<table class="adminlist">
			<thead>
				<tr>
					<th>
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
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
						<?php echo $this->pagination; ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$i = 0;
			foreach ($this->rows as $item):
				$item->translate();

				$cls = $i%2;
				if ($item->get('status') == 2)
				{
					$cls .= ' protected';
				}
				?>
				<tr class="row<?php echo $cls; ?>">
					<td>
						<?php echo Html::grid('id', $i, $item->get('extension_id')); ?>
					</td>
					<td>
						<span class="bold hasTip" title="<?php echo htmlspecialchars($item->get('name').'::'.$item->get('description')); ?>">
							<?php echo $item->get('name'); ?>
						</span>
					</td>
					<td class="priority-2 center">
						<?php echo $item->get('client'); ?>
					</td>
					<td class="center">
						<?php if (!$item->get('element')) : ?>
						<strong>X</strong>
						<?php else : ?>
							<?php echo Html::manage('state', $item->get('enabled'), $i, $item->get('enabled') == 1, 'cb'); ?>
						<?php endif; ?>
					</td>
					<td class="priority-3 center">
						<?php echo Lang::txt('COM_INSTALLER_TYPE_' . $item->get('type')); ?>
					</td>
					<td class="priority-4 center">
						<?php echo ($item->get('version') != '') ? $item->get('version') : '&#160;'; ?>
						<?php if ($item->get('system_data')) : ?>
							<?php if ($tooltip = $this->createCompatibilityInfo($item->get('system_data'))) : ?>
								<?php echo Html::behavior('tooltip', $tooltip, Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_TITLE')); ?>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td class="priority-5 center">
						<?php echo ($item->get('creationDate') != '') ? $item->get('creationDate') : '&#160;'; ?>
					</td>
					<td class="priority-5 center">
						<span class="editlinktip hasTip" title="<?php echo addslashes(htmlspecialchars(Lang::txt('COM_INSTALLER_AUTHOR_INFORMATION').'::'.$item->get('author_info'))); ?>">
							<?php echo ($item->get('author') != '') ? $item->get('author') : '&#160;'; ?>
						</span>
					</td>
					<td class="priority-4 center">
						<?php echo ($item->get('folder') != '') ? $item->get('folder') : Lang::txt('COM_INSTALLER_TYPE_NONAPPLICABLE'); ?>
					</td>
					<td class="priority-4">
						<?php echo $item->get('extension_id'); ?>
					</td>
				</tr>
				<?php
				$i++;
			endforeach;
			?>
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
