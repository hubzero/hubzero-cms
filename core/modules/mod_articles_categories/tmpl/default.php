<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;
?>
<ul class="categories-module<?php echo $moduleclass_sfx; ?>">
	<?php require $this->getLayoutPath($params->get('layout', 'default') . '_items'); ?>
</ul>
