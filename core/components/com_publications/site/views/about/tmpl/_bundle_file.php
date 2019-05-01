<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$file = $this->file;
?>
<li>
	<span class="item-icon">
		<span class="item-extension _<?php echo $this->escape(strtolower(Filesystem::extension($file['name']))); ?>"></span>
	</span>
	<span class="item-title"><?php echo $this->escape($file['name']); ?></span>
	<span class="item-details"><?php echo trim(Hubzero\Utility\Number::formatBytes($file['size'])); ?></span>
</li>
