<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>

<thead>
	<tr>
		<th scope="col" class="priority-5" width="5%">
			<?php echo Html::grid('sort', Lang::txt('COM_SEARCH_COL_ID'), 'id', $sortDirection, $sortCriteria); ?>
		</th>

		<th scope="col">
			<?php echo Html::grid('sort', Lang::txt('COM_SEARCH_COL_TYPE'), 'id', $sortDirection, $sortCriteria); ?>
		</th>

		<th scope="col">
			<?php echo Html::grid('sort', Lang::txt('COM_SEARCH_COL_STRENGTH'), 'id', $sortDirection, $sortCriteria); ?>
		</th>
	</tr>
</thead>
