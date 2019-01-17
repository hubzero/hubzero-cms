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

$class = ' class="first"';

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0): ?>
	<ul>
		<?php foreach ($this->items[$this->parent->id] as $id => $item): ?>
			<?php if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())): ?>
				<?php
				if (!isset($this->items[$this->parent->id][$id + 1]))
				{
					$class = ' class="last"';
				}
				?>
				<li<?php echo $class; ?>>
					<?php $class = ''; ?>
					<span class="item-title">
						<a href="<?php echo Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($item->id)); ?>">
							<?php echo $this->escape($item->title); ?>
						</a>
					</span>

					<?php if ($this->params->get('show_subcat_desc_cat') == 1): ?>
						<?php if ($item->description): ?>
							<div class="category-desc">
								<?php echo Html::content('prepare', $item->description, '', 'com_content.categories'); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ($this->params->get('show_cat_num_articles_cat') == 1): ?>
						<dl>
							<dt><?php echo Lang::txt('COM_CONTENT_NUM_ITEMS'); ?></dt>
							<dd><?php echo $item->numitems; ?></dd>
						</dl>
					<?php endif; ?>

					<?php
					if (count($item->getChildren()) > 0):
						$this->items[$item->id] = $item->getChildren();
						$this->parent = $item;
						$this->maxLevelcat--;

						echo $this->loadTemplate('items');

						$this->parent = $item->getParent();
						$this->maxLevelcat++;
					endif;
					?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php endif;
