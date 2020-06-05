<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<?php if (!empty($names)): ?>
	<ul class="latestusers<?php echo $moduleclass_sfx ?>">
		<?php foreach ($names as $name): ?>
			<li>
				<?php echo $name->username; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif;
