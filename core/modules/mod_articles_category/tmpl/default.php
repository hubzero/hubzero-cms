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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;
?>
<ul class="category-module<?php echo $moduleclass_sfx; ?>">
<?php if ($grouped) : ?>
	<?php foreach ($list as $group_name => $group) : ?>
	<li>
		<h<?php echo $item_heading; ?>><?php echo $group_name; ?></h<?php echo $item_heading; ?>>
		<ul>
			<?php foreach ($group as $item) : ?>
				<li>
					<h<?php echo $item_heading+1; ?>>
						<?php if ($params->get('link_titles') == 1) : ?>
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
							<?php echo $item->title; ?>
							<?php if ($item->displayHits) :?>
								<span class="mod-articles-category-hits">(<?php echo $item->displayHits; ?>)</span>
							<?php endif; ?></a>
						<?php else : ?>
							<?php echo $item->title; ?>
							<?php if ($item->displayHits) :?>
								<span class="mod-articles-category-hits">(<?php echo $item->displayHits; ?>)</span>
							<?php endif; ?></a>
						<?php endif; ?>
					</h<?php echo $item_heading+1; ?>>

					<?php if ($params->get('show_author')) :?>
						<span class="mod-articles-category-writtenby">
							<?php echo $item->displayAuthorName; ?>
						</span>
					<?php endif;?>

					<?php if ($item->displayCategoryTitle) :?>
						<span class="mod-articles-category-category">
						(<?php echo $item->displayCategoryTitle; ?>)
						</span>
					<?php endif; ?>
					<?php if ($item->displayDate) : ?>
						<span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
					<?php endif; ?>
					<?php if ($params->get('show_introtext')) :?>
						<p class="mod-articles-category-introtext">
							<?php echo $item->displayIntrotext; ?>
						</p>
					<?php endif; ?>

					<?php if ($params->get('show_readmore')) :?>
						<p class="mod-articles-category-readmore">
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
							<?php if ($item->params->get('access-view')== FALSE) :
									echo Lang::txt('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE');
								elseif ($readmore = $item->alternative_readmore) :
									echo $readmore;
									echo \Hubzero\Utility\String::truncate($item->title, $params->get('readmore_limit'));
									if ($params->get('show_readmore_title', 0) != 0) :
										echo \Hubzero\Utility\String::truncate($this->item->title, $params->get('readmore_limit'));
									endif;
								elseif ($params->get('show_readmore_title', 0) == 0) :
									echo Lang::txt('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE');
								else :

									echo Lang::txt('MOD_ARTICLES_CATEGORY_READ_MORE');
									echo \Hubzero\Utility\String::truncate($item->title, $params->get('readmore_limit'));
								endif; ?>
							</a>
						</p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</li>
	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($list as $item) : ?>
	<li>
		<h<?php echo $item_heading; ?>>
		<?php if ($params->get('link_titles') == 1) : ?>
			<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
				<?php echo $item->title; ?>
				<?php if ($item->displayHits) :?>
					<span class="mod-articles-category-hits">(<?php echo $item->displayHits; ?>)</span>
				<?php endif; ?>
			</a>
		<?php else : ?>
			<?php echo $item->title; ?>
			<?php if ($item->displayHits) :?>
				<span class="mod-articles-category-hits">(<?php echo $item->displayHits; ?>)</span>
			<?php endif; ?>
		<?php endif; ?>
		</h<?php echo $item_heading; ?>>

		<?php if ($params->get('show_author')) :?>
			<span class="mod-articles-category-writtenby">
				<?php echo $item->displayAuthorName; ?>
			</span>
		<?php endif;?>
		<?php if ($item->displayCategoryTitle) :?>
			<span class="mod-articles-category-category">
				(<?php echo $item->displayCategoryTitle; ?>)
			</span>
		<?php endif; ?>
		<?php if ($item->displayDate) : ?>
			<span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
		<?php endif; ?>
		<?php if ($params->get('show_introtext')) :?>
			<p class="mod-articles-category-introtext">
				<?php echo $item->displayIntrotext; ?>
			</p>
		<?php endif; ?>

		<?php if ($params->get('show_readmore')) :?>
			<p class="mod-articles-category-readmore">
				<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
					<?php if ($item->params->get('access-view')== FALSE) :
						echo Lang::txt('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE');
					elseif ($readmore = $item->alternative_readmore) :
						echo $readmore;
						echo \Hubzero\Utility\String::truncate($item->title, $params->get('readmore_limit'));
					elseif ($params->get('show_readmore_title', 0) == 0) :
						echo Lang::txt('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE');
					else :
						echo Lang::txt('MOD_ARTICLES_CATEGORY_READ_MORE');
						echo \Hubzero\Utility\String::truncate($item->title, $params->get('readmore_limit'));
					endif; ?>
				</a>
			</p>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
<?php endif; ?>
</ul>
