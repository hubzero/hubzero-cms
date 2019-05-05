<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<div class="random-image<?php echo $moduleclass_sfx; ?>">
	<?php if ($link) : ?>
		<a href="<?php echo $link; ?>">
	<?php endif; ?>
			<?php echo Html::asset('image', $image->folder . '/' . $image->name, $image->name, array('width' => $image->width, 'height' => $image->height)); ?>
	<?php if ($link) : ?>
		</a>
	<?php endif; ?>
</div>
