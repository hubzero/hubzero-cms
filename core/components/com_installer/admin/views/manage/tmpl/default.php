<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

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
$canOrder  = User::authorise('core.edit.state', 'com_plugins');
$saveOrder = $listOrder == 'ordering';

?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'manage') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=manage'); ?>"><?php echo Lang::txt('COM_INSTALLER_SUBMENU_CORE'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'migrations') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=migrations'); ?>"><?php echo Lang::txt('COM_INSTALLER_SUBMENU_MIGRATIONS'); ?></a>
		</li>
	</ul>
</nav>

<div id="installer-manage">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=manage');?>" method="post" name="adminForm" id="adminForm">

		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" />
				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>

			<div class="filter-select fltrt">

				<select name="filter_location" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_VALUE_CLIENT_SELECT');?></option>
					<?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::LocationOptions(), 'value', 'text', $this->filters['client_id'], true);?>
				</select>

				<select name="filter_status" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_VALUE_STATE_SELECT');?></option>
					<?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::StatusOptions(), 'value', 'text', $this->filters['status'], true);?>
				</select>

				<select name="filter_type" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_VALUE_TYPE_SELECT');?></option>
					<?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::TypeOptions(), 'value', 'text', $this->filters['type']);?>
				</select>

				<select name="filter_group" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_VALUE_FOLDER_SELECT');?></option>
					<?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::GroupOptions(), 'value', 'text', $this->filters['group']);?>
				</select>

			</div>
		</fieldset>

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

				$ordering   = ($listOrder == 'ordering');
				$canCheckin = User::authorise('core.manage', 'com_checkin') || $item->checked_out==User::get('id') || $item->checked_out==0;
				$canChange  = User::authorise('core.edit.state', 'com_installer') && $canCheckin;

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
							<?php echo Html::grid('published', $item->enabled, $i, '', $canChange); ?>
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
