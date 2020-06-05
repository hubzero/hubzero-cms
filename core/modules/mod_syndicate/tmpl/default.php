<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<a href="<?php echo $link ?>" class="syndicate-module<?php echo $moduleclass_sfx; ?>">
	<?php echo Html::asset('icon', 'feed'); ?>
	<?php if ($params->get('display_text', 1)) : ?>
		<span>
		<?php if (str_replace(' ', '', $text) != '') : ?>
			<?php echo $text; ?>
		<?php else : ?>
			<?php echo Lang::txt('MOD_SYNDICATE_DEFAULT_FEED_ENTRIES'); ?>
		<?php endif; ?>
		</span>
	<?php endif; ?>
</a>
