<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');

$field     = Request::getCmd('field');
$function  = 'jSelectUser_'.$field;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<h2 class="modal-title"><?php echo Lang::txt('Users'); ?></h2>
<form action="<?php echo Route::url('index.php?option=com_users&view=users&layout=modal&tmpl=component&groups='.Request::getVar('groups', '', 'default', 'BASE64').'&excluded='.Request::getVar('excluded', '', 'default', 'BASE64'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="filter clearfix">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="40" placeholder="<?php echo Lang::txt('COM_USERS_SEARCH_IN_NAME'); ?>" />

			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			<button type="button" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('', '<?php echo Lang::txt('JLIB_FORM_SELECT_USER') ?>');"><?php echo Lang::txt('JOPTION_NO_USER')?></button>
		</div>
		<div class="col width-50 fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo Lang::txt('COM_USERS_FILTER_STATE');?></option>
				<?php echo Html::select('options', UsersHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>

			<select name="filter_approved" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo Lang::txt('COM_USERS_FILTER_APPROVED');?></option>
				<?php echo Html::select('options', UsersHelper::getApprovedOptions(), 'value', 'text', $this->state->get('filter.approved'));?>
			</select>

			<label for="filter_group_id"><?php echo Lang::txt('COM_USERS_FILTER_USER_GROUP'); ?></label>
			<?php echo Html::access('usergroup', 'filter_group_id', $this->state->get('filter.group_id'), 'onchange="this.form.submit()"'); ?>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_GROUPS', 'group_names', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$i = 0;
			foreach ($this->items as $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');">
						<?php echo $item->name; ?></a>
				</td>
				<td align="center">
					<?php echo $item->username; ?>
				</td>
				<td align="left">
					<?php echo nl2br($item->group_names); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
