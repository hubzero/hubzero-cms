<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->data) { ?>
	<h3><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_HEADER'); ?></h3>
	<div class="aside">
		<p><?php echo Lang::txt('PLG_RESOURCES_SPONSORS_EXPLANATION'); ?></p>
	</div>
	<div class="subject" id="sponsors-subject">
		<?php echo $this->data; ?>
	</div>
<?php }