<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
$i = 0;
$n = $list->count();
?>
<ul class="newsflash-horiz<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php foreach ($list as $item) : ?>
		<li>
			<?php require $this->getLayoutPath('_item');

			if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>
				<span class="article-separator">&#160;</span>
			<?php endif; ?>
		</li>
	<?php
		$i++;
	endforeach; ?>
</ul>
