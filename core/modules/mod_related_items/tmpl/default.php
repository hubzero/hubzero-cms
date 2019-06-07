<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<ul class="relateditems<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item): ?>
		<li>
			<a href="<?php echo $item->route; ?>">
				<?php
				if ($showDate):
					echo '<time datetime="' . $item->created . '">' . Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')) . '</time> - ';
				endif;
				?>
				<?php echo $item->title; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
