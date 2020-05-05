<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$i = $this->i;
$data = $this->data;
$row = $this->row;
$acos = [
	'tickets' => [
		'read', 'update', 'delete'
	],
	'comments' => [
		'create', 'read'
	],
	'private_comments' => [
		'create', 'read'
	]
];
?>

<tr>
	<td class="align-center">
		<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" class="checkbox-toggle" />
	</td>
	<td class="align-center">
		<?php echo $row->id; ?>
	</td>
	<td>
		<?php echo $row->alias; ?> (<?php echo $row->foreign_key; ?>)
	</td>
	<td>
		<?php echo $row->model; ?>
	</td>

	<?php
		foreach ($acos as $aco => $aroList):
			foreach ($aroList as $action):
				$this->view('_acl_aro_row_toggle_field')
					->set('id', $data[$aco]['id'])
					->set('isEnabled', $data[$aco][$action])
					->set('action', $action)
					->display();
			endforeach;
		endforeach;
	?>
</tr>

