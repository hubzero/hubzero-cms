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
	<?php
	// This is for the admin-defined project information
	if ($this->info)
	{
		?>
		<dl id="infotbl">
			<?php
			foreach ($this->info as $field)
			{
				?>
				<dt><?php echo $field->label; ?></dt>
				<dd><?php echo $field->value; ?></dd>
				<?php
			} // end foreach
			?>
		</dl>
		<?php
	} // end if
	?>
</div><!-- / .basic info -->