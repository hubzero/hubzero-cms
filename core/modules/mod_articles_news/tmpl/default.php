<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<div class="newsflash<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) :?>
		<?php require $this->getLayoutPath('_item'); ?>
	<?php endforeach; ?>
</div>
