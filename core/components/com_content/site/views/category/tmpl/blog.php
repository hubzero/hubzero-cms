<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(PATH_COMPONENT . '/helpers');

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
					<span class="subheading-category"><?php echo $this->category->title;?></span>
				<?php endif; ?>
			</h2>
		<?php endif; ?>
	</header>
<?php endif; ?>

<section class="main section">
	<div class="blog<?php echo $this->pageclass_sfx; ?>">

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

		<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
			<?php if ($this->params->get('show_no_articles', 1)) : ?>
				<p><?php echo Lang::txt('COM_CONTENT_NO_ARTICLES'); ?></p>
			<?php endif; ?>
		<?php endif; ?>

		<?php $leadingcount = 0; ?>
		<?php if (!empty($this->lead_items)) : ?>
			<div class="items-leading">
				<?php foreach ($this->lead_items as &$item) : ?>
					<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
						<?php
						$this->item = &$item;
						echo $this->loadTemplate('item');
						?>
					</div>
					<?php
					$leadingcount++;
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php
		$introcount = (count($this->intro_items));
		$counter = 0;
		?>
		<?php if (!empty($this->intro_items)) : ?>
			<?php foreach ($this->intro_items as $key => &$item) : ?>
				<?php
				$key = ($key-$leadingcount)+1;
				$rowcount=( ((int)$key-1) %	(int) $this->columns) +1;
				$row = $counter / $this->columns ;

				if ($rowcount==1) : ?>
					<div class="items-row cols-<?php echo (int) $this->columns;?> <?php echo 'row-'.$row ; ?>">
				<?php endif; ?>
				<div class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
					<?php
						$this->item = &$item;
						echo $this->loadTemplate('item');
					?>
				</div>
				<?php $counter++; ?>
				<?php if (($rowcount == $this->columns) or ($counter ==$introcount)): ?>
						<span class="row-separator"></span>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if (!empty($this->link_items)) : ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php if (!empty($this->children[$this->category->id])&& $this->maxLevel != 0) : ?>
			<div class="cat-children">
				<?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
					<h3>
						<?php echo Lang::txt('JGLOBAL_SUBCATEGORIES'); ?>
					</h3>
				<?php endif; ?>
				<?php echo $this->loadTemplate('children'); ?>
			</div>
		<?php endif; ?>

		<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
			<div class="pagination">
				<?php /*if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="counter">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif;*/ ?>
				<?php echo $this->pagination->render(); //getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
