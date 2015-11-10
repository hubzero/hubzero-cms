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

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_ACL'), 'support.png');
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('acl');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}

/**
* Toggles the check state of a group of boxes
*
* Checkboxes must have an id attribute in the form cb0, cb1...
* @param The number of box to 'check'
* @param An alternative field name
*/
function checkAllOptions()
{
	var f = document.adminForm;
	var c = f.toggleOpt.checked;

	$('.chk').each(function(i, el){
		el.checked = c;
	});
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th> </th>
				<th> </th>
				<th> </th>
				<th> </th>
				<th colspan="3"><?php echo Lang::txt('COM_SUPPORT_COL_TICKETS'); ?></th>
				<th colspan="2"><?php echo Lang::txt('COM_SUPPORT_COL_COMMENTS'); ?></th>
				<th colspan="2"><?php echo Lang::txt('COM_SUPPORT_COL_PRIVATE_COMMENTS'); ?></th>
				<th> </th>
			</tr>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_ID'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_OBJECT'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_MODEL'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_READ'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_UPDATE'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_DELETE'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_CREATE'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_READ'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_CREATE'); ?></th>
				<th><?php echo Lang::txt('COM_SUPPORT_COL_READ'); ?></th>
				<th> </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td> </td>
				<td>
					<input type="hidden" name="aro[id]" id="aro_id" value="" />
				</td>
				<td>
					<label for="aro_foreign_key"><?php echo Lang::txt('COM_SUPPORT_ACL_ALIAS_ID'); ?>:</label>
					<input type="text" name="aro[foreign_key]" id="aro_foreign_key" size="20" value="" />
				</td>
				<td>
					<select name="aro[model]" id="aro_model">
						<option value="user"><?php echo Lang::txt('COM_SUPPORT_ACL_USER'); ?></option>
						<option value="group"><?php echo Lang::txt('COM_SUPPORT_ACL_GROUP'); ?></option>
					</select>
					<input type="checkbox" name="toggleOpt" value="" onclick="checkAllOptions();" /> <abbr title="<?php echo Lang::txt('COM_SUPPORT_CHECK_ALL'); ?>"><?php echo Lang::txt('COM_SUPPORT_COL_ALL'); ?></abbr>
				</td>
				<td>
					<input type="hidden" name="map[tickets][id]" value="0" />
					<input type="hidden" name="map[tickets][aro_id]" value="0" />
					<input type="hidden" name="map[tickets][aco_id]" value="1" />
					<input type="hidden" name="map[tickets][action_create]" value="1" />
					<input type="checkbox" class="chk" name="map[tickets][action_read]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[tickets][action_update]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[tickets][action_delete]" value="1" />
				</td>
				<td>
					<input type="hidden" name="map[comments][id]" value="0" />
					<input type="hidden" name="map[comments][aro_id]" value="0" />
					<input type="hidden" name="map[comments][aco_id]" value="2" />
					<input type="checkbox" class="chk" name="map[comments][action_create]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[comments][action_read]" value="1" />
					<input type="hidden" name="map[comments][action_update]" value="0" />
					<input type="hidden" name="map[comments][action_delete]" value="0" />
				</td>
				<td>
					<input type="hidden" name="map[private_comments][id]" value="0" />
					<input type="hidden" name="map[private_comments][aro_id]" value="0" />
					<input type="hidden" name="map[private_comments][aco_id]" value="3" />
					<input type="checkbox" class="chk" name="map[private_comments][action_create]" value="1" />
				</td>
				<td>
					<input type="checkbox" class="chk" name="map[private_comments][action_read]" value="1" />
					<input type="hidden" name="map[private_comments][action_update]" value="0" />
					<input type="hidden" name="map[private_comments][action_delete]" value="0" />
				</td>
				<td>
					<input type="submit" name="newacl" value="Add" onclick="submitbutton('save');" />
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$sql = "SELECT m.*, r.model AS aro_model, r.foreign_key AS aro_foreign_key, r.alias AS aro_alias, c.model AS aco_model, c.foreign_key AS aco_foreign_key
	FROM #__support_acl_aros_acos AS m
	LEFT JOIN #__support_acl_aros AS r ON m.aro_id=r.id
	LEFT JOIN #__support_acl_acos AS c ON m.aco_id=c.id
	WHERE r.foreign_key=$row->foreign_key AND r.model='$row->model'
	ORDER BY aro_foreign_key, aro_model";
	$this->database->setQuery($sql);
	$lines = $this->database->loadObjectList();

	$data = array();
	$data['tickets']['id'] = 0;
	$data['tickets']['create'] = 0;
	$data['tickets']['read'] = 0;
	$data['tickets']['update'] = 0;
	$data['tickets']['delete'] = 0;

	$data['comments']['id'] = 0;
	$data['comments']['create'] = 0;
	$data['comments']['read'] = 0;
	$data['comments']['update'] = 0;
	$data['comments']['delete'] = 0;

	$data['private_comments']['id'] = 0;
	$data['private_comments']['create'] = 0;
	$data['private_comments']['read'] = 0;
	$data['private_comments']['update'] = 0;
	$data['private_comments']['delete'] = 0;

	foreach ($lines as $line)
	{
		$data[$line->aco_model]['id'] = $line->id;
		$data[$line->aco_model]['create'] = $line->action_create;
		$data[$line->aco_model]['read'] = $line->action_read;
		$data[$line->aco_model]['update'] = $line->action_update;
		$data[$line->aco_model]['delete'] = $line->action_delete;
	}
?>
			<tr>
				<td style="text-align:center;"><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td style="text-align:center;"><?php echo $row->id; ?></td>
				<td><?php echo $row->alias; ?> (<?php echo $row->foreign_key; ?>)</td>
				<td><?php echo $row->model; ?></td>
				<td style="text-align:center;">
					<?php
					$calt = Lang::txt('JNO');
					$cls = 'unpublish';
					if ($data['tickets']['read'])
					{
						$calt = Lang::txt('JYES');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&id=' . $data['tickets']['id'] . '&action=read&value=' . ($data['tickets']['read'] ? '0' : '1') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php
					$calt = Lang::txt('JNO');
					$cls = 'unpublish';
					if ($data['tickets']['update'])
					{
						$calt = Lang::txt('JYES');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&id=' . $data['tickets']['id'] . '&action=update&value=' . ($data['tickets']['update'] ? '0' : '1') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php
					$calt = Lang::txt('JNO');
					$cls = 'unpublish';
					if ($data['tickets']['delete'])
					{
						$calt = Lang::txt('JYES');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&id=' . $data['tickets']['id'] . '&action=delete&value=' . ($data['tickets']['delete'] ? '0' : '1') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>

				<td style="text-align:center;">
					<?php
					$calt = Lang::txt('JNO');
					$cls = 'unpublish';
					if ($data['comments']['create'])
					{
						$calt = Lang::txt('JYES');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&id=' . $data['tickets']['id'] . '&action=create&value=' . ($data['comments']['create'] ? '0' : '1') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php
					$calt = Lang::txt('JNO');
					$cls = 'unpublish';
					if ($data['comments']['read'])
					{
						$calt = Lang::txt('JYES');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&id=' . $data['tickets']['id'] . '&action=read&value=' . ($data['comments']['read'] ? '0' : '1') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>

				<td style="text-align:center;">
					<?php
					$calt = Lang::txt('JNO');
					$cls = 'unpublish';
					if ($data['private_comments']['create'])
					{
						$calt = Lang::txt('JYES');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&id=' . $data['tickets']['id'] . '&action=create&value=' . ($data['private_comments']['create'] ? '0' : '1') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>
				<td style="text-align:center;">
					<?php
					$calt = Lang::txt('JNO');
					$cls = 'unpublish';
					if ($data['private_comments']['read'])
					{
						$calt = Lang::txt('JYES');
						$cls = 'publish';
					}
					?>
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&id=' . $data['tickets']['id'] . '&action=read&value=' . ($data['private_comments']['read'] ? '0' : '1') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>
				<td> </td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
