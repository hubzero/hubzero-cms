<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;
?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item): ?>
		<li>
			<a href="<?php echo $item->link; ?>">
				<?php echo $item->title; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
