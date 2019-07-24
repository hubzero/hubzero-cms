<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<h3><?php echo Lang::txt('COM_CONTENT_MORE_ARTICLES'); ?></h3>

<ol>
	<?php foreach ($this->link_items as &$item) : ?>
		<li>
			<a href="<?php echo Route::url(Components\Content\Site\Helpers\Route::getArticleRoute($item->slug, $item->catslug, $item->language)); ?>">
				<?php echo $item->title; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ol>
