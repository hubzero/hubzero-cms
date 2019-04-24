<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
// no direct access
defined('_HZEXEC_') or die;
?>
<ul class="mostread<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
	<li>
		<a href="<?php echo $item->link; ?>">
			<?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ul>
