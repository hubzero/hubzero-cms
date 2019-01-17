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
$params = &$this->params;
?>

<ul id="archive-items">
	<?php foreach ($this->items as $i => $item) : ?>
		<li class="row<?php echo $i % 2; ?>">
			<h3>
				<?php if ($params->get('link_titles')): ?>
					<a href="<?php echo Route::url(Components\Content\Site\Helpers\Route::getArticleRoute($item->slug, $item->catslug, $item->language)); ?>">
						<?php echo $this->escape($item->title); ?>
					</a>
				<?php else: ?>
					<?php echo $this->escape($item->title); ?>
				<?php endif; ?>
			</h3>

			<?php if ($params->get('show_author') or $params->get('show_parent_category') or $params->get('show_category') or $params->get('show_create_date') or $params->get('show_modify_date') or $params->get('show_publish_date') or $params->get('show_hits')) : ?>
				<dl class="article-info">
					<dt class="article-info-term"><?php echo Lang::txt('COM_CONTENT_ARTICLE_INFO'); ?></dt>
			<?php endif; ?>
				<?php if ($params->get('show_parent_category')): ?>
					<dd class="parent-category-name">
						<?php
						$title = $this->escape($item->parent_title);
						$url = '<a href="' . Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($item->parent_slug)) . '">' . $title . '</a>';
						?>
						<?php if ($params->get('link_parent_category') && $item->parent_slug) : ?>
							<?php echo Lang::txt('COM_CONTENT_PARENT', $url); ?>
						<?php else: ?>
							<?php echo Lang::txt('COM_CONTENT_PARENT', $title); ?>
						<?php endif; ?>
					</dd>
				<?php endif; ?>
				<?php if ($params->get('show_category')): ?>
					<dd class="category-name">
						<?php
						$title = $this->escape($item->category_title);
						$url = '<a href="' . Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($item->catslug)) . '">' . $title . '</a>';
						?>
						<?php if ($params->get('link_category') && $item->catslug): ?>
							<?php echo Lang::txt('COM_CONTENT_CATEGORY', $url); ?>
						<?php else: ?>
							<?php echo Lang::txt('COM_CONTENT_CATEGORY', $title); ?>
						<?php endif; ?>
					</dd>
				<?php endif; ?>
				<?php if ($params->get('show_create_date')): ?>
					<dd class="create">
						<?php echo Lang::txt('COM_CONTENT_CREATED_DATE_ON', Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
					</dd>
				<?php endif; ?>
				<?php if ($params->get('show_modify_date')): ?>
					<dd class="modified">
						<?php echo Lang::txt('COM_CONTENT_LAST_UPDATED', Date::of($item->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
					</dd>
				<?php endif; ?>
				<?php if ($params->get('show_publish_date')): ?>
					<dd class="published">
						<?php echo Lang::txt('COM_CONTENT_PUBLISHED_DATE_ON', Date::of($item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
					</dd>
				<?php endif; ?>
				<?php if ($params->get('show_author') && !empty($item->author )): ?>
					<dd class="createdby">
						<?php $author = $item->author; ?>
						<?php $author = ($item->created_by_alias ? $item->created_by_alias : $author); ?>

						<?php if (!empty($item->contactid ) &&  $params->get('link_author') == true): ?>
							<?php echo Lang::txt('COM_CONTENT_WRITTEN_BY', '<a href="' . Route::url('index.php?option=com_contact&view=contact&id=' . $item->contactid) . '">' . $author . '</a>'); ?>
						<?php else: ?>
							<?php echo Lang::txt('COM_CONTENT_WRITTEN_BY', $author); ?>
						<?php endif; ?>
					</dd>
				<?php endif; ?>
				<?php if ($params->get('show_hits')): ?>
					<dd class="hits">
						<?php echo Lang::txt('COM_CONTENT_ARTICLE_HITS', $item->hits); ?>
					</dd>
				<?php endif; ?>
			<?php if ($params->get('show_author') or $params->get('show_category') or $params->get('show_create_date') or $params->get('show_modify_date') or $params->get('show_publish_date') or $params->get('show_hits')): ?>
				</dl>
			<?php endif; ?>

			<?php if ($params->get('show_intro')): ?>
				<div class="intro">
					<?php echo Hubzero\Utility\Str::truncate($item->introtext, $params->get('introtext_limit')); ?>
				</div>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>

<div class="pagination">
	<?php echo $this->pagination->render(); ?>
</div>
