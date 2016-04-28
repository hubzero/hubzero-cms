<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');

$user      = User::getInstance();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

JText::script('COM_USERS_GROUPS_CONFIRM_DELETE');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'groups.delete')
		{
			var f = document.adminForm;
			var cb='';
<?php foreach ($this->items as $i=>$item):?>
<?php if ($item->user_count > 0):?>
			cb = f['cb'+<?php echo $i;?>];
			if (cb && cb.checked) {
				if (confirm(Joomla.JText._('COM_USERS_GROUPS_CONFIRM_DELETE'))) {
					Joomla.submitform(task);
				}
				return;
			}
<?php endif;?>
<?php endforeach;?>
		}
		Joomla.submitform(task);
	}
</script>
<form action="<?php echo Route::url('index.php?option=com_users&view=groups');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('COM_USERS_SEARCH_GROUPS_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_USERS_SEARCH_IN_GROUPS'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="priority-4">
					<?php echo Lang::txt('JGRID_HEADING_ID'); ?>
				</th>
				<th class="left">
					<?php echo Lang::txt('COM_USERS_HEADING_GROUP_TITLE'); ?>
				</th>
				<th class="priority-3">
					<?php echo Lang::txt('COM_USERS_HEADING_USERS_IN_GROUP'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canCreate = $user->authorise('core.create', 'com_users');
			$canEdit   = $user->authorise('core.edit',   'com_users');
			// If this group is super admin and this user is not super admin, $canEdit is false
			if (!$user->authorise('core.admin') && (JAccess::checkGroup($item->id, 'core.admin')))
			{
				$canEdit = false;
			}
			$canChange = $user->authorise('core.edit.state', 'com_users');
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($canEdit) : ?>
						<?php echo Html::grid('id', $i, $item->id); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-4">
					<?php echo (int) $item->id; ?>
				</td>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level) ?>
					<?php if ($canEdit) : ?>
					<a href="<?php echo Route::url('index.php?option=com_users&task=group.edit&id='.$item->id);?>">
						<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<?php if (Config::get('debug')) : ?>
						<a class="button fltrt" href="<?php echo Route::url('index.php?option=com_users&view=debuggroup&group_id='.(int) $item->id);?>">
							<?php echo Lang::txt('COM_USERS_DEBUG_GROUP');?>
						</a>
					<?php endif; ?>
				</td>
				<td class="center priority-3">
					<?php echo $item->user_count ? $item->user_count : ''; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
