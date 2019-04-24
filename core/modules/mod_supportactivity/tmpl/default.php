<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<div class="mod_<?php echo $this->module->name; ?>">
	<?php if ($this->results) { ?>
		<ul id="activity-list<?php echo $this->module->id; ?>" data-url="<?php echo Request::base(true) . '?task=module&amp;no_html=1&amp;module=' . $this->module->name . '&amp;feedactivity=1&amp;start='; ?>">
			<?php
			foreach ($this->results as $result)
			{
				require $this->getLayoutPath('default_item');
			}
			?>
		</ul>
	<?php } else { ?>
		<p class="no-results"><?php echo Lang::txt('MOD_SUPPORTACTIVITY_NO_RESULTS'); ?></p>
	<?php } ?>
</div>