<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div class="items-more">
	<h3><?php echo Lang::txt('COM_CONTENT_MORE_ARTICLES'); ?></h3>
	<ol>
		<?php foreach ($this->link_items as &$item) : ?>
			<li>
				<a href="<?php echo Route::url(Components\Content\Site\Helpers\Route::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
					<?php echo $item->title; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</div>
