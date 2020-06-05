<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_ACL'), 'support');
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('acl');

Html::behavior('framework');
Html::behavior('formvalidation');

$this->js('edit.js');
?>

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
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_OBJECT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_MODEL'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_READ'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_UPDATE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_DELETE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_CREATE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_READ'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_CREATE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_READ'); ?></th>
				<th scope="col"> </th>
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
					<input type="checkbox" name="toggleOpt" id="toggleOpt" value="" /> <abbr title="<?php echo Lang::txt('COM_SUPPORT_CHECK_ALL'); ?>"><?php echo Lang::txt('COM_SUPPORT_COL_ALL'); ?></abbr>
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
					<input type="submit" name="newacl" id="newacl" value="<?php echo Lang::txt('Add'); ?>" />
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$db = App::get('db');
$k = 0;
$i = 0;
foreach ($this->rows as $row)
{
	$sql = "SELECT m.*, r.model AS aro_model, r.foreign_key AS aro_foreign_key, r.alias AS aro_alias, c.model AS aco_model, c.foreign_key AS aco_foreign_key
		FROM `#__support_acl_aros_acos` AS m
		LEFT JOIN `#__support_acl_aros` AS r ON m.aro_id=r.id
		LEFT JOIN `#__support_acl_acos` AS c ON m.aco_id=c.id
		WHERE r.foreign_key=" . $db->quote($row->foreign_key) . " AND r.model=" . $db->quote($row->model) . "
		ORDER BY aro_foreign_key, aro_model";
	$db->setQuery($sql);
	$lines = $db->loadObjectList();

	$data = array();
	$data['tickets']['id']     = 0;
	$data['tickets']['create'] = 0;
	$data['tickets']['read']   = 0;
	$data['tickets']['update'] = 0;
	$data['tickets']['delete'] = 0;

	$data['comments']['id']     = 0;
	$data['comments']['create'] = 0;
	$data['comments']['read']   = 0;
	$data['comments']['update'] = 0;
	$data['comments']['delete'] = 0;

	$data['private_comments']['id']     = 0;
	$data['private_comments']['create'] = 0;
	$data['private_comments']['read']   = 0;
	$data['private_comments']['update'] = 0;
	$data['private_comments']['delete'] = 0;

	foreach ($lines as $line)
	{
		$data[$line->aco_model]['id']     = $line->id;
		$data[$line->aco_model]['create'] = $line->action_create;
		$data[$line->aco_model]['read']   = $line->action_read;
		$data[$line->aco_model]['update'] = $line->action_update;
		$data[$line->aco_model]['delete'] = $line->action_delete;
	}

	$this->view('_acl_aro_row')
		->set('i', $i)
		->set('data', $data)
		->set('row', $row)
		->display();

	$i++;
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
