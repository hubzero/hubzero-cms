<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('CITATION') . ': ' . Lang::txt('CITATION_STATS'), 'citation.png');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('YEAR'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('AFFILIATED'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('NONAFFILIATED'); ?></th>
				<th scope="col"><?php echo Lang::txt('TOTAL'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->stats as $year => $amt) { ?>
			<tr>
				<th><?php echo $year; ?></th>
				<td class="priority-2"><?php echo $amt['affiliate']; ?></td>
				<td class="priority-2"><?php echo $amt['non-affiliate']; ?></td>
				<td><?php echo (intval($amt['affiliate']) + intval($amt['non-affiliate'])); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</form>