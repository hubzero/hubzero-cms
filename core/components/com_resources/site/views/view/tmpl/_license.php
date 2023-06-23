<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$name = $this->license->name;
if (empty($name))
{
}
else if (substr($name, 0, 6) != 'custom')
{
	?>
		<?php if ($this->license->url) { ?>
			<a target='_blank' rel="noopener noreferrer license" href="<?php echo $this->license->url; ?>" title="<?php echo $this->escape($this->license->title); ?>"><p class="<?php echo $name; ?> license"></p></a>
		<?php } else { ?>
			<p class="<?php echo $name; ?> license"></p>
		<?php } ?>
	<?php
} else {
	?>
		<a target='_blank' rel="noopener noreferrer license" class="popup" href="<?php echo Route::url('index.php?option=com_resources&task=license&resource=' . substr($name, 6) . '&no_html=1'); ?>"><p class="<?php echo $name; ?> license"></p></a>
	<?php
}
