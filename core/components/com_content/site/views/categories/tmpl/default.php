<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(PATH_COMPONENT . '/helpers');

?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<header id="content-header">
		<h2>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h2>
	</header>
<?php endif; ?>

<section class="main section">
	<div class="categories-list<?php echo $this->pageclass_sfx; ?>">
		<?php if ($this->params->get('show_base_description')) : ?>
			<?php //If there is a description in the menu parameters use that; ?>
				<?php if ($this->params->get('categories_description')) : ?>
					<?php echo Html::content('prepare', $this->params->get('categories_description'), '', 'com_content.categories'); ?>
				<?php  else: ?>
					<?php //Otherwise get one from the database if it exists. ?>
					<?php if ($this->parent->description) : ?>
						<div class="category-desc">
							<?php echo Html::content('prepare', $this->parent->description, '', 'com_content.categories'); ?>
						</div>
					<?php  endif; ?>
				<?php  endif; ?>
			<?php endif; ?>
		<?php
		echo $this->loadTemplate('items');
		?>
	</div>
</section>

