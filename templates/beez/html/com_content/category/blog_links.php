<?php // @version $Id: blog_links.php 10381 2008-06-01 03:35:53Z pasamio $
defined('_JEXEC') or die('Restricted access');
?>

<h2>
	<?php echo JText::_('More Articles...'); ?>
</h2>

<ul>
	<?php foreach ($this->links as $link) : ?>
	<li>
		<a class="blogsection" href="<?php echo JRoute::_('index.php?view=article&id='.$link->slug); ?>">
			<?php echo $link->title; ?></a>
	</li>
	<?php endforeach; ?>
</ul>
