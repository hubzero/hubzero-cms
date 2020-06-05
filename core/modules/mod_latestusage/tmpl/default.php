<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<table <?php echo ($this->cls) ? 'class="' . $this->cls . '" ' : ''; ?>>
	<caption><?php echo Lang::txt('MOD_LATESTUSAGE_CAPTION'); ?></caption>
	<tfoot>
		<tr>
			<td><a href="<?php echo Route::url('index.php?option=com_usage&task=maps&type=online'); ?>"><?php echo Lang::txt('MOD_LATESTUSAGE_WHOSONLONE'); ?></a></td>
			<td class="more"><a href="<?php echo Route::url('index.php?option=com_usage'); ?>"><?php echo Lang::txt('MOD_LATESTUSAGE_MORE'); ?></a></td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_USERS'); ?></th>
			<td class="numerical-data"><?php echo $this->users; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_RESOURCES'); ?></th>
			<td class="numerical-data"><?php echo $this->resources; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_TOOLS'); ?></th>
			<td class="numerical-data"><?php echo $this->tools; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_SIMULATIONS'); ?></th>
			<td class="numerical-data"><?php echo $this->sims; ?></td>
		</tr>
	</tbody>
</table>
