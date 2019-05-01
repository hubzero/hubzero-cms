<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
// no direct access
defined('_HZEXEC_') or die();
?>
<div class="handlertype-<?php echo $this->handler->get('_name'); ?>">
	<h3><?php echo $this->configs->label; ?></h3>
	<p><?php echo $this->configs->about; ?></p>
</div>