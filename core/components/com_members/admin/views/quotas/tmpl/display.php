<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS_QUOTAS'), 'user');
if ($canDo->get('core.edit'))
{
	Toolbar::custom('syncQuotasToSystem', 'refresh', 'refresh', 'Sync Selected User Quotas');
	Toolbar::spacer();
}

if ($canDo->get('core.create'))
{
	Toolbar::addNew();
	Toolbar::spacer();
}

Toolbar::help('quotas');


$this->css('quotas.css')
	->js('quotas.js');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search_field"><?php echo Lang::txt('COM_MEMBERS_SEARCH'); ?></label>
				<select name="search_field" id="filter_search_field" class="filter">
					<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_QUOTA_USERNAME'); ?></option>
					<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_QUOTA_NAME'); ?></option>
				</select>

				<label for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_FOR'); ?></label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_GO'); ?>" />
			</div>
			<div class="col span7">
				<select name="class_alias" id="filter_class_alias" class="filter filter-submit">
					<option value=""<?php if ($this->filters['class_alias'] == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FILTER_QUOTA_CLASS'); ?></option>
					<?php foreach ($this->classes as $class) : ?>
						<option value="<?php echo $class->get('alias'); ?>"<?php if ($this->filters['class_alias'] == $class->get('alias')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($class->get('alias')); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<!-- <th class="priority-6"><?php echo Html::grid('sort', 'COM_MEMBERS_QUOTA_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th class="priority-5"><?php echo Html::grid('sort', 'COM_MEMBERS_QUOTA_USER_ID', 'user_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th class="priority-4"><?php echo Html::grid('sort', 'COM_MEMBERS_QUOTA_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo Html::grid('sort', 'COM_MEMBERS_QUOTA_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th class="priority-3"><?php echo Html::grid('sort', 'COM_MEMBERS_QUOTA_CLASS', 'class_alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_DISK_USAGE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					// Initiate paging
					echo $this->rows->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			?>
			<tr class="<?php echo "row$k quota-row"; ?>" data-quota="<?php echo Route::url('index.php?option=com_members&controller=quotas&task=getQuotaUsage&id=' . $row->get('id'), false); ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('user_id'); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->get('user_id'); ?></label>
				</td>
				<td class="priority-5">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('user_id')); ?>">
						<?php echo $this->escape($row->get('user_id')); ?>
					</a>
				</td>
				<td class="priority-4">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape($row->get('username')); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->get('name')); ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->get('class_alias', 'custom')); ?>
				</td>
				<td>
					<div class="usage-calculating"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CALCULATING'); ?></div>
					<div class="usage-outer">
						<div class="usage-inner"></div>
					</div>
					<div class="usage-unavailable"><?php echo Lang::txt('COM_MEMBERS_QUOTA_UNAVAILABLE'); ?></div>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
	<?php echo Html::input('token'); ?>
</form>