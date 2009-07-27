<?php // @version $Id: default_links.php 9781 2007-12-31 11:13:48Z mtk $
defined('_JEXEC') or die('Restricted access');
?>

<h2>
	<?php echo JText::_('More Articles...'); ?>
</h2>

<ul>
	<?php foreach ($this->links as $link) : ?>
	<li>
		<a class="blogsection" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($link->slug, $link->catslug, $link->sectionid)); ?>">
			<?php echo $link->title; ?></a>
	</li>
	<?php endforeach; ?>
</ul>
