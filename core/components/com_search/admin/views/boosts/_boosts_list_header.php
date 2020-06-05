<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$sortField = $this->sortField;
$sortDirection = $this->sortDirection;
?>

<thead>
	<tr>
		<th scope="col" class="priority-5" width="5%">
			<?php echo Html::grid(
				'sort', Lang::txt('COM_SEARCH_COL_ID'), 'id', $sortDirection, $sortField
			); ?>
		</th>

		<th scope="col">
			<?php echo Html::grid(
				'sort', Lang::txt('COM_SEARCH_COL_TYPE'), 'field_value', $sortDirection, $sortField
			); ?>
		</th>

		<th scope="col">
			<?php echo Html::grid(
				'sort', Lang::txt('COM_SEARCH_COL_STRENGTH'), 'strength', $sortDirection, $sortField
			); ?>
		</th>
	</tr>
</thead>
