<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;
?>
<ul class="categories-module<?php echo $moduleclass_sfx; ?>">
	<?php require $this->getLayoutPath($params->get('layout', 'default') . '_items'); ?>
</ul>
