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

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS'), 'user.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();

	Toolbar::custom('clearTerms', 'remove', '', 'COM_MEMBERS_CLEAR_TERMS', false);
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
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
	//Toolbar::deleteList();
}

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
			<div class="col span5">
				<label for="filter_search_field"><?php echo Lang::txt('COM_MEMBERS_SEARCH'); ?></label>
				<select name="search_field" id="filter_search_field">
					<option value="uidNumber"<?php if ($this->filters['search_field'] == 'uidNumber') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?></option>
					<option value="email"<?php if ($this->filters['search_field'] == 'email') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL'); ?></option>
					<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME'); ?></option>
					<option value="surname"<?php if ($this->filters['search_field'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_LAST_NAME'); ?></option>
					<option value="givenName"<?php if ($this->filters['search_field'] == 'givenName') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_FIRST_NAME'); ?></option>
					<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_FULL_NAME'); ?></option>
				</select>

				<label for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_FOR'); ?></label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_GO'); ?>" />
			</div>
			<div class="col span7">
				<select name="emailConfirmed" id="filter_emailConfirmed" onchange="document.adminForm.submit( );">
					<option value="0"<?php if ($this->filters['emailConfirmed'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FILTER_EMAIL_CONFIRMED'); ?></option>
					<option value="1"<?php if ($this->filters['emailConfirmed'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_CONFIRMED'); ?></option>
					<option value="-1"<?php if ($this->filters['emailConfirmed'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_UNCONFIRMED'); ?></option>
				</select>
				<select name="public" id="filter_public" onchange="document.adminForm.submit( );">
					<option value="-1"<?php if ($this->filters['public'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FILTER_PROFILE_ACCESS'); ?></option>
					<option value="1"<?php if ($this->filters['public'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_PROFILE_ACCESS_PUBLIC'); ?></option>
					<option value="0"<?php if ($this->filters['public'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_FIELD_PROFILE_ACCESS_PRIVATE'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_ID', 'uidNumber', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_NAME', 'lname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_ORGANIZATION', 'org', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3" colspan="2"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_REGISTERED', 'registerDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_CONTRIBUTIONS', 'rcount', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_MEMBERS_COL_LAST_VISIT', 'lastvisitDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php
					// Initiate paging
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$default = '/core/components/com_members/site/assets/img/profile.gif';//DS . trim($this->config->get('defaultpic'), DS);
$default = \Hubzero\User\Profile\Helper::thumbit($default);

$base = str_replace('/administrator', '', Request::base(true));

$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$picture = $default;

	$row = &$this->rows[$i];

	if (!$row->surname && !$row->givenName)
	{
		$bits = explode(' ', $row->name);
		$row->surname = array_pop($bits);
		if (count($bits) >= 1)
		{
			$row->givenName = array_shift($bits);
		}
		if (count($bits) >= 1)
		{
			$row->middleName = implode(' ', $bits);
		}
	}
	$row->surname   = (trim($row->surname))   ? trim($row->surname)   : Lang::txt('COM_MEMBERS_UNDEFINED');
	$row->givenName = (trim($row->givenName)) ? trim($row->givenName) : Lang::txt('COM_MEMBERS_UNDEFINED');

	switch ($row->emailConfirmed)
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

	if (!$row->lastvisitDate || $row->lastvisitDate == "0000-00-00 00:00:00")
	{
		$lvisit = '<span class="never" style="color:#bbb;">' . Lang::txt('COM_MEMBERS_NEVER') . '</span>';
	}
	else
	{
		$lvisit = '<time datetime="' . $row->lastvisitDate . '">' . Date::of($row->lastvisitDate)->toLocal('Y-m-d') . '</time>';
	}

	if ($row->picture)
	{
		$thumb  = substr(PATH_APP, strlen(PATH_ROOT)) . DS . trim($this->config->get('webpath'), DS);
		$thumb .= DS . \Hubzero\User\Profile\Helper::niceidformat($row->uidNumber);
		$thumb .= DS . ltrim($row->picture, DS);
		$thumb = \Hubzero\User\Profile\Helper::thumbit($thumb);

		if (file_exists(PATH_ROOT . $thumb))
		{
			$picture = $thumb;
		}
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->uidNumber; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="priority-2">
					<?php echo $row->uidNumber; ?>
				</td>
				<td>
					<a class="editlinktip hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->uidNumber); ?>" title="<?php echo $this->escape(stripslashes($row->name)); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $base . $picture; ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><span class=&quot;glyph org&quot;><?php echo ($row->organization) ? $this->escape(stripslashes($row->organization)) : Lang::txt('COM_MEMBERS_UNKNOWN'); ?></span><br /><span class=&quot;glyph <?php echo ($row->public) ? 'public' : 'private'; ?>&quot;><?php echo ($row->public) ? 'public profile' : 'private profile'; ?></span>">
						<?php echo $this->escape(stripslashes($row->surname)) . ', ' . $this->escape(stripslashes($row->givenName)) . ' ' . $this->escape(stripslashes($row->middleName)); ?>
					</a>
				</td>
				<td class="priority-2">
					<?php echo $this->escape($row->username); ?>
				</td>
				<td class="priority-3">
					<?php if (trim($row->email)) { ?>
						<a href="mailto:<?php echo $row->email; ?>"><?php echo $this->escape($row->email); ?></a>
					<?php } ?>
				</td>
				<td class="priority-3">
					<a class="state <?php echo $state; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->uidNumber . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_SET_TASK', $task); ?>">
						<span class="text"><?php echo $alt; ?></span>
					</a>
				</td>
				<td class="priority-3">
					<time datetime="<?php echo $row->registerDate; ?>"><?php echo Date::of($row->registerDate)->toLocal('Y-m-d'); ?></time>
				</td>
				<td class="priority-4">
					<?php echo $lvisit; ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo Html::input('token'); ?>
</form>