<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Document::addStyleDeclaration('
	.toolbar-box .icon-32-buildprofile:before { content: "\f007"; }
	.toolbar-box .icon-32-buildprofile:after { content: "\f0ad"; right: 0px; bottom: -2px; }
');

$canDo = Components\Members\Helpers\Admin::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS'));
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option);
	Toolbar::spacer();

	//Toolbar::custom('profile', 'buildprofile', '', 'COM_MEMBERS_PROFILE', false);
	Toolbar::getRoot()->appendButton('Link', 'buildprofile', 'COM_MEMBERS_PROFILE', Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=profile'));
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::custom('clearTerms', 'remove', '', 'COM_MEMBERS_CLEAR_TERMS', false);
	Toolbar::publishList('confirm', 'COM_MEMBERS_CONFIRM');
	Toolbar::unpublishList('unconfirm', 'COM_MEMBERS_UNCONFIRM');
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('users');

Html::behavior('tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');

	if (pressbutton == 'clearTerms') {
		var res = confirm('Are you sure? Make sure you know what you\'re doing, as this action cannot be undone!');

		if (!res) {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span4">
				<label for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_FOR'); ?></label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_RESET'); ?></button>
			</div>
			<div class="col span8">
				<select name="activation" id="filter_emailConfirmed" onchange="document.adminForm.submit( );">
					<option value="0"<?php if ($this->filters['activation'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FILTER_EMAIL_CONFIRMED'); ?></option>
					<option value="1"<?php if ($this->filters['activation'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_CONFIRMED'); ?></option>
					<option value="-1"<?php if ($this->filters['activation'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_UNCONFIRMED'); ?></option>
				</select>

				<label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
				<select name="access" id="filter-access" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
				</select>

				<label for="filter-state"><?php echo Lang::txt('COM_MEMBERS_FILTER_STATE'); ?>:</label>
				<select name="state" id="filter-state" onchange="this.form.submit()">
					<option value="*"><?php echo Lang::txt('COM_MEMBERS_FILTER_STATE');?></option>
					<?php echo Html::select('options', Components\Members\Helpers\Admin::getStateOptions(), 'value', 'text', $this->filters['state']); ?>
				</select>

				<label for="filter-approved"><?php echo Lang::txt('COM_MEMBERS_FILTER_APPROVED'); ?>:</label>
				<select name="approved" id="filter-approved" onchange="this.form.submit()">
					<option value="*"><?php echo Lang::txt('COM_MEMBERS_FILTER_APPROVED');?></option>
					<?php echo Html::select('options', Components\Members\Helpers\Admin::getApprovedOptions(), 'value', 'text', $this->filters['approved']); ?>
				</select>

				<label for="filter-group_id"><?php echo Lang::txt('COM_MEMBERS_FILTER_USERGROUP'); ?>:</label>
				<select name="group_id" id="filter-group_id" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_MEMBERS_FILTER_USERGROUP');?></option>
					<?php echo Html::select('options', Components\Members\Helpers\Admin::getAccessGroups(), 'value', 'text', $this->filters['group_id']); ?>
				</select>

				<label for="filter-range"><?php echo Lang::txt('COM_MEMBERS_OPTION_FILTER_DATE'); ?>:</label>
				<select name="range" id="filter-range" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('COM_MEMBERS_OPTION_FILTER_DATE');?></option>
					<?php echo Html::select('options', Components\Members\Helpers\Admin::getRangeOptions(), 'value', 'text', $this->filters['range']); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->count(); ?>);" /></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_MEMBERS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_MEMBERS_COL_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_MEMBERS_COL_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-6"><?php echo Html::grid('sort', 'COM_MEMBERS_COL_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3 nowrap"><?php echo Lang::txt('COM_MEMBERS_COL_GROUPS'); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_ENABLED', 'block', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="nowrap"><?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_APPROVED', 'approved', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3" colspan="2"><?php echo Html::grid('sort', 'COM_MEMBERS_COL_REGISTERED', 'registerDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-6"><?php echo Html::grid('sort', 'COM_MEMBERS_COL_LAST_VISIT', 'lastvisitDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
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
			$canEdit   = $canDo->get('core.edit');
			$canChange = User::authorise('core.edit.state', $this->option);

			// If this group is super admin and this user is not super admin, $canEdit is false
			if ((!User::authorise('core.admin')) && JAccess::check($row->get('id'), 'core.admin'))
			{
				$canEdit   = false;
				$canChange = false;
			}

			if (!$row->get('surname') && !$row->get('givenName'))
			{
				$bits = explode(' ', $row->get('name'));

				$row->set('surname', array_pop($bits));

				if (count($bits) >= 1)
				{
					$row->set('givenName', array_shift($bits));
				}
				if (count($bits) >= 1)
				{
					$row->set('middleName', implode(' ', $bits));
				}
			}
			$row->set('name', $row->get('surname', Lang::txt('COM_MEMBERS_UNDEFINED')) . ', ' . $row->get('givenName', Lang::txt('COM_MEMBERS_UNDEFINED')) . ' ' . $row->get('middleName'));

			switch ($row->get('activation'))
			{
				case '1':
					$task = 'unconfirm';
					$img = 'publish_g.png';
					$alt = Lang::txt('JYES');
					$state = 'publish';
					break;
				case '3':
					$task = 'unconfirm';
					$img = 'publish_g.png';
					$alt = Lang::txt('COM_MEMBERS_DOMAIN_SUPPLIED_EMAIL');
					$state = 'publish';
					break;
				default:
					$task = 'confirm';
					$img = 'publish_x.png';
					$alt = Lang::txt('JNO');
					$state = 'unpublish';
					break;
			}

			$groups = array();
			foreach ($row->accessgroups as $agroup)
			{
				$groups[] = $this->accessgroups->seek($agroup->get('group_id'))->get('title');
			}
			$row->set('group_names', implode('<br />', $groups));
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canEdit) : ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
					<?php endif; ?>
				</td>
				<td class="priority-2">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<div class="fltrt">
						<?php if ($count = $row->notes->count()) : ?>
							<a class="state filter" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=notes&filter_search=uid:' . (int) $row->get('id')); ?>" title="<?php echo Lang::txt('COM_USERS_FILTER_NOTES'); ?>">
								<span><?php echo Lang::txt('COM_USERS_NOTES'); ?></span>
							</a>
							<a class="modal state notes" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=notes&tmpl=component&task=modal&id=' . (int) $row->get('id')); ?>" rel="{handler: 'iframe', size: {x: 800, y: 450}}" title="<?php echo Lang::txts('COM_MEMBERS_N_USER_NOTES', $count); ?>">
								<span><?php echo Lang::txt('COM_USERS_NOTES'); ?></span>
							</a>
						<?php endif; ?>
						<a class="state notes" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=notes&task=add&user_id=' . (int) $row->get('id')); ?>" title="<?php echo Lang::txt('COM_USERS_ADD_NOTE'); ?>">
							<span><?php echo Lang::txt('COM_USERS_NOTES'); ?></span>
						</a>
					</div>
					<?php if ($canEdit) : ?>
						<a class="editlinktip hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>" title="<?php echo $this->escape(stripslashes($row->get('name')));/* ?>::<img border=&quot;1&quot; src=&quot;<?php echo $base . $picture; ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><span class=&quot;glyph org&quot;><?php echo ($row->organization) ? $this->escape(stripslashes($row->organization)) : Lang::txt('COM_MEMBERS_UNKNOWN'); ?></span><br /><span class=&quot;glyph <?php echo ($row->public) ? 'public' : 'private'; ?>&quot;><?php echo ($row->public) ? 'public profile' : 'private profile'; </span>*/?>">
							<?php echo $this->escape($row->get('name')); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($row->get('name')); ?>
					<?php endif; ?>
					<?php if (Config::get('debug')) : ?>
						<a class="permissions button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=debug&id=' . $row->get('id'));?>">
							<?php echo Lang::txt('COM_MEMBERS_DEBUG_USER');?>
						</a>
					<?php endif; ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row->get('username')); ?>
				</td>
				<td class="priority-6">
					<?php echo $this->escape($row->get('email')); ?>
				</td>
				<td class="center priority-3">
					<?php if (substr_count($row->get('group_names'), "\n") > 1) : ?>
						<span class="hasTip" title="<?php echo Lang::txt('COM_MEMBERS_HEADING_GROUPS') . '::' . $row->get('group_names'); ?>"><?php echo Lang::txt('COM_MEMBERS_MULTIPLE_GROUPS'); ?></span>
					<?php else : ?>
						<?php echo $row->get('group_names'); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-4">
					<?php if ($canChange) : ?>
						<?php if (User::get('id') != $row->get('id')) : ?>
							<?php echo Html::grid('boolean', $i, !$row->get('block'), 'unblock', 'block'); ?>
						<?php else : ?>
							<?php echo Html::grid('boolean', $i, !$row->get('block'), 'block', null); ?>
						<?php endif; ?>
					<?php else : ?>
						<span class="state <?php echo Lang::txt($row->get('block') ? 'no' : 'yes'); ?>"><span><?php echo Lang::txt($row->get('block') ? 'JNO' : 'JYES'); ?></span></span>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php if ($canChange) : ?>
						<?php echo Html::grid('boolean', $i, $row->get('approved'), 'approve', null); ?>
					<?php else : ?>
						<?php echo Html::grid('boolean', $i, $row->get('approved'), null, null); ?>
					<?php endif; ?>
				</td>
				<td class="priority-3">
					<?php if ($canChange) : ?>
						<a class="state <?php echo $state; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_SET_TASK', $task); ?>">
							<span class="text"><?php echo $alt; ?></span>
						</a>
					<?php else : ?>
						<span class="state <?php echo $state; ?>">
							<span class="text"><?php echo $alt; ?></span>
						</span>
					<?php endif; ?>
				</td>
				<td class="priority-3">
					<time datetime="<?php echo Date::of($row->get('registerDate'))->format('Y-m-dTh:i:s'); ?>"><?php echo Date::of($row->get('registerDate'))->toLocal('Y-m-d H:i:s'); ?></time>
				</td>
				<td class="priority-6">
					<?php if (!$row->get('lastvisitDate') || $row->get('lastvisitDate') == '0000-00-00 00:00:00') : ?>
						<span class="never" style="color:#bbb;"><?php echo Lang::txt('COM_MEMBERS_NEVER'); ?></span>
					<?php else: ?>
						<time datetime="<?php echo Date::of($row->get('lastvisitDate'))->format('Y-m-dTh:i:s'); ?>"><?php echo Date::of($row->get('lastvisitDate'))->toLocal('Y-m-d H:i:s'); ?></time>
					<?php endif; ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<?php if (User::authorise('core.create', $this->option) && User::authorise('core.edit', $this->option) && User::authorise('core.edit.state', $this->option)) : ?>
		<?php echo $this->loadTemplate('batch'); ?>
	<?php endif;?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo Html::input('token'); ?>
</form>