<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');
Html::behavior('modal');

$canDo = UsersHelper::getActions();
$user = User::getInstance();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$loggeduser = User::getInstance();
?>

<form action="<?php echo Route::url('index.php?option=com_users&view=users');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('COM_USERS_SEARCH_USERS'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_USERS_SEARCH_USERS'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_RESET'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<label for="filter_state">
				<?php echo Lang::txt('COM_USERS_FILTER_LABEL'); ?>
			</label>

			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo Lang::txt('COM_USERS_FILTER_STATE');?></option>
				<?php echo Html::select('options', UsersHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>

			<select name="filter_approved" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo Lang::txt('COM_USERS_FILTER_APPROVED');?></option>
				<?php echo Html::select('options', UsersHelper::getApprovedOptions(), 'value', 'text', $this->state->get('filter.approved'));?>
			</select>

			<select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_USERS_FILTER_USERGROUP');?></option>
				<?php echo Html::select('options', UsersHelper::getGroups(), 'value', 'text', $this->state->get('filter.group_id'));?>
			</select>

			<select name="filter_range" id="filter_range" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_USERS_OPTION_FILTER_DATE');?></option>
				<?php echo Html::select('options', Usershelper::getRangeOptions(), 'value', 'text', $this->state->get('filter.range'));?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-3">
					<?php echo Html::grid('sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-2">
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_ENABLED', 'a.block', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_APPROVED', 'a.approved', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-3">
					<?php echo Lang::txt('COM_USERS_HEADING_GROUPS'); ?>
				</th>
				<th class="nowrap priority-5">
					<?php echo Html::grid('sort', 'JGLOBAL_EMAIL', 'a.email', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-6">
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_LAST_VISIT_DATE', 'a.lastvisitDate', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-6">
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_REGISTRATION_DATE', 'a.registerDate', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-5">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canEdit   = $canDo->get('core.edit');
			$canChange = $loggeduser->authorise('core.edit.state', 'com_users');
			// If this group is super admin and this user is not super admin, $canEdit is false
			if ((!$loggeduser->authorise('core.admin')) && JAccess::check($item->id, 'core.admin'))
			{
				$canEdit   = false;
				$canChange = false;
			}
		?>
			<tr class="row<?php echo $i % 2; if (!$canChange) { echo ' disabled'; } ?>">
				<td class="center">
					<?php if ($canEdit) : ?>
						<?php echo Html::grid('id', $i, $item->id); ?>
					<?php endif; ?>
				</td>
				<td>
					<div class="fltrt">
						<?php echo Html::users('filterNotes', $item->note_count, $item->id); ?>
						<?php echo Html::users('notes', $item->note_count, $item->id); ?>
						<?php echo Html::users('addNote', $item->id); ?>
					</div>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=com_users&task=user.edit&id='.(int) $item->id); ?>" title="<?php echo Lang::txt('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
							<?php echo $this->escape($item->name); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($item->name); ?>
					<?php endif; ?>
					<?php if (Config::get('debug')) : ?>
						<a class="permissions button" href="<?php echo Route::url('index.php?option=com_users&view=debuguser&user_id='.(int) $item->id);?>">
							<?php echo Lang::txt('COM_USERS_DEBUG_USER');?>
						</a>
					<?php endif; ?>
				</td>
				<td class="center priority-3">
					<?php echo $this->escape($item->username); ?>
				</td>
				<td class="center priority-2">
					<?php if ($canChange) : ?>
						<?php if ($loggeduser->id != $item->id) : ?>
							<?php echo Html::grid('boolean', $i, !$item->block, 'users.unblock', 'users.block'); ?>
						<?php else : ?>
							<?php echo Html::grid('boolean', $i, !$item->block, 'users.block', null); ?>
						<?php endif; ?>
					<?php else : ?>
						<span class="state <?php echo Lang::txt($item->block ? 'no' : 'yes'); ?>"><span><?php echo Lang::txt($item->block ? 'JNO' : 'JYES'); ?></span></span>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php if ($canChange) : ?>
						<?php echo Html::grid('boolean', $i, $item->approved, 'users.approve', null); ?>
					<?php else : ?>
						<?php echo Html::grid('boolean', $i, $item->approved, null, null); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-3">
					<?php if (substr_count($item->group_names, "\n") > 1) : ?>
						<span class="hasTip" title="<?php echo Lang::txt('COM_USERS_HEADING_GROUPS').'::'.nl2br($item->group_names); ?>"><?php echo Lang::txt('COM_USERS_USERS_MULTIPLE_GROUPS'); ?></span>
					<?php else : ?>
						<?php echo nl2br($item->group_names); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-5">
					<?php echo $this->escape($item->email); ?>
				</td>
				<td class="center priority-6">
					<?php if ($item->lastvisitDate!='0000-00-00 00:00:00'):?>
						<?php echo Date::of($item->lastvisitDate)->toLocal('Y-m-d H:i:s'); ?>
					<?php else:?>
						<?php echo Lang::txt('JNEVER'); ?>
					<?php endif;?>
				</td>
				<td class="center priority-6">
					<?php echo Date::of($item->registerDate)->toLocal('Y-m-d H:i:s'); ?>
				</td>
				<td class="center priority-5">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php //Load the batch processing form. ?>
	<?php if ($user->authorise('core.create', 'com_users') && $user->authorise('core.edit', 'com_users') && $user->authorise('core.edit.state', 'com_users')) : ?>
		<?php echo $this->loadTemplate('batch'); ?>
	<?php endif;?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
