<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

if ($params->get('backgroundimage'))
{
	$this->css('
	.custom' . $moduleclass_sfx . ' {
		background-image: url(' . $params->get('backgroundimage') . ');
	}
	');
}
?>
<div class="custom<?php echo $moduleclass_sfx ?>">
	<?php echo $module->content; ?>
</div>
