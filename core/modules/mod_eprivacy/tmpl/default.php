<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();
$this->js();
?>
<div class="<?php echo $this->module->module; ?>" id="<?php echo $this->moduleid; ?>">
	<div class="<?php echo $this->module->module; ?>-message">
		<?php echo $this->message; ?>

		<a class="<?php echo $this->module->module; ?>-close" href="<?php echo $this->uri; ?>" data-duration="<?php echo $this->duration; ?>" title="<?php echo Lang::txt('MOD_EPRIVACY_CLOSE_TITLE'); ?>">
			<span><?php echo Lang::txt('MOD_EPRIVACY_CLOSE'); ?></span>
		</a>
	</div>
</div>
