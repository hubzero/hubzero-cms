<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(JPATH_COMPONENT.'/helpers');

?>
<?php if ($this->params->get('show_page_heading') or $this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
	<header id="content-header">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h2>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h2>
		<?php endif; ?>

		<?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
			<h2>
				<?php echo $this->escape($this->params->get('page_subheading')); ?>
				<?php if ($this->params->get('show_category_title')) : ?>
					<span class="subheading-category"><?php echo $this->category->title; ?></span>
				<?php endif; ?>
			</h2>
		<?php endif; ?>
	</header>
<?php endif; ?>

<section class="main section">
	<div class="section-inner category-list<?php echo $this->pageclass_sfx;?>">

		<?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
			<div class="subject">
		<?php endif; ?>

		<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
			<div class="category-desc">
				<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
					<img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="" />
				<?php endif; ?>
				<?php if ($this->params->get('show_description') && $this->category->description) : ?>
					<?php echo Html::content('prepare', $this->category->description, '', 'com_content.category'); ?>
				<?php endif; ?>
				<div class="clr"></div>
			</div>
		<?php endif; ?>

		<div class="cat-items">
			<?php echo $this->loadTemplate('articles'); ?>
		</div>

		<?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
			</div>
			<aside class="aside">
				<div class="cat-children">
					<?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
						<h3>
							<?php echo Lang::txt('JGLOBAL_SUBCATEGORIES'); ?>
						</h3>
					<?php endif; ?>
					<?php echo $this->loadTemplate('children'); ?>
				</div>
			</aside>
		<?php endif; ?>
	</div>
</section>
