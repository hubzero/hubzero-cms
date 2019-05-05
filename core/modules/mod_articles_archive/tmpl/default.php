<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

if (!empty($list)) :?>
	<ul class="archive-module<?php echo $moduleclass_sfx; ?>">
		<?php foreach ($list as $item) : ?>
			<li>
				<a href="<?php echo $item->link; ?>">
					<?php echo $item->text; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif;