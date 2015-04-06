<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>


<div class="items-more">

<h3><?php echo Lang::txt('COM_CONTENT_MORE_ARTICLES'); ?></h3>
<ol>
<?php
	foreach ($this->link_items as &$item) :
?>
	<li>
		<a href="<?php echo Route::url(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
			<?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ol>
</div>
