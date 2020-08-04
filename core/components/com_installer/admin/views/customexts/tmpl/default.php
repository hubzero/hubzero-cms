<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER_' . $this->controller));
Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER_' . $this->controller), 'customexts');

if ($canDo->get('core.create'))
{
	Toolbar::addNew('customexts.edit');
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_INSTALLER_CUSTOMEXTS_DELETE_CONFIRM', 'remove');
}
Toolbar::divider();
if ($canDo->get('core.edit.state'))
{
	Toolbar::publish('customexts.publish', 'JTOOLBAR_ENABLE', true);
	Toolbar::unpublish('customexts.unpublish', 'JTOOLBAR_DISABLE', true);
	Toolbar::divider();
}
Toolbar::custom('customexts.update', 'refresh', '', 'COM_INSTALLER_CUSTOMEXTS_UPDATE_CODE');
Toolbar::divider();

Toolbar::help('customexts');

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
			<a<?php if ($this->controller == 'customexts') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=customexts'); ?>"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_SUBMENU'); ?></a>
		</li>
	</ul>
</nav>

<div id="installer-customexts">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=customexts');?>" method="post" name="adminForm" id="adminForm">

		<fieldset id="filter-bar">

			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" />
				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>

			<div class="filter-select fltrt">

				<select name="filter_location" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_CLIENT_SELECT');?></option>
					<?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::LocationOptions(), 'value', 'text', $this->filters['client_id'], true);?>
				</select>

				<select name="filter_status" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_STATE_SELECT');?></option>
					<?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::StatusOptions(), 'value', 'text', $this->filters['status'], true);?>
				</select>

				<select name="filter_type" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_TYPE_SELECT');?></option>
					<?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::TypeOptions(), 'value', 'text', $this->filters['type']);?>
				</select>

				<select name="filter_group" class="inputbox filter filter-submit" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_FOLDER_SELECT');?></option>
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
						<?php echo Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_NAME', 'name', $listDirn, $listOrder); ?>
					</th>
					<th class="center">
						<?php echo Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_STATUS', 'status', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-2">
						<?php echo Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_LOCATION', 'client_id', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-3">
						<?php echo Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_TYPE', 'type', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-4 center">
						<?php echo Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_FOLDER', 'folder', $listDirn, $listOrder); ?>
					</th>
					<th class="priority-5">
						<?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADING_MODIFIED_ON'); ?>
					</th>
					<th class="priority-5">
						<?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADING_MODIFIED_BY'); ?>
					</th>
					<th class="priority-4">
						<?php echo Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_ID', 'extension_id', $listDirn, $listOrder); ?>
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

				$ordering   = ($listOrder == 'ordering');
				$canChange  = User::authorise('core.edit.state', 'com_installer');

				$cls = $i%2;
				?>
				<tr class="row<?php echo $cls; ?>">
					<td>
						<?php echo Html::grid('id', $i, $item->get('extension_id')); ?>
					</td>
					<td>
						<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $item->get('extension_id')); ?>">
							<?php echo $this->escape(stripslashes($item->get('name'))); ?>
						</a>
						<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($item->get('name'))); ?>
						</span>
						<?php } ?>
					</td>
					<td class="center">
						<?php if (!$item->get('alias')) : ?>
							<strong>X</strong>
						<?php else : ?>
						<?php echo Html::grid('published', $item->enabled, $i, '', $canChange); ?>
						<?php endif; ?>
					</td>
					<td class="priority-2 center">
						<?php echo ($item->get('client_id') == 1) ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE'); ?>
					</td>
					<td class="priority-3 center">
						<?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_TYPE_' . $item->get('type')); ?>
					</td>
					<td class="priority-4 center">
						<?php echo ($item->get('folder') != '') ? $item->get('folder') : '&#160;'; ?>
					</td>
					<td class="priority-5 center">
						<?php echo ($item->get('modified') != '') ? $item->get('modified') : '&#160;'; ?>
					</td>
					<td class="priority-5 center">
						<?php
						$modifier = User::getInstance($item->get('modified_by'));
						echo $this->escape($modifier->get('name', Lang::txt('COM_INSTALLER_CUSTOMEXTS_UNKNOWN')) . ' (' . $item->get('modified_by') . ')');
						?>
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
