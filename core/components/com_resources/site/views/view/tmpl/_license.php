<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$name = $this->license->name;

if (substr($name, 0, 6) != 'custom')
{
	?>
	<p class="<?php echo $name; ?> license">
		<?php if ($this->license->url) { ?>
			Licensed according to <a rel="license" href="<?php echo $this->license->url; ?>" title="<?php echo $this->escape($this->license->title); ?>">this deed</a>.
		<?php } else { ?>
			Licensed under <?php echo $this->license->title; ?>
		<?php } ?>
	</p>
	<?php
} else {
	?>
	<p class="<?php echo $name; ?> license">
		Licensed according to <a rel="license" class="popup" href="<?php echo Route::url('index.php?option=com_resources&task=license&resource=' . substr($name, 6) . '&no_html=1'); ?>">this deed</a>.
	</p>
	<?php
}
