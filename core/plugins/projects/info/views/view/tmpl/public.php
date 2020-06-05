<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div id="basic_info">
	<table id="infotbl">
		<tbody>
			<?php
			// This is for the admin-defined project information
			if ($this->info)
			{
				foreach ($this->info as $field)
				{
					?>
					<tr>
						<th class="htd"><?php echo $field->label; ?></th>
						<td><?php echo $field->value; ?></td>
					</tr>
					<?php
				} // end foreach
			} // end if
			?>
		</tbody>
	</table>
</div><!-- / .basic info -->