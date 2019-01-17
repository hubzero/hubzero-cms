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
?>

<?php if (count($this->children[$this->category->id]) > 0 && $this->maxLevel != 0) : ?>
	<ul>
		<?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
			<?php
			if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
				if (!isset($this->children[$this->category->id][$id + 1])) :
					$class = ' class="last"';
				endif;
				?>
				<li<?php echo $class; ?>>
					<?php $class = ''; ?>
					<span class="item-title">
						<a href="<?php echo Route::url(Components\Content\Site\Helpers\Route::getCategoryRoute($child->id)); ?>">
							<?php echo $this->escape($child->title); ?>
						</a>
					</span>

					<?php if ($this->params->get('show_subcat_desc') == 1) :?>
						<?php if ($child->description) : ?>
							<div class="category-desc">
								<?php echo Html::content('prepare', $child->description, '', 'com_content.category'); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
						<dl>
							<dt>
								<?php echo Lang::txt('COM_CONTENT_NUM_ITEMS'); ?>
							</dt>
							<dd>
								<?php echo $child->getNumItems(true); ?>
							</dd>
						</dl>
					<?php endif; ?>

					<?php if (count($child->getChildren()) > 0):
						$this->children[$child->id] = $child->getChildren();
						$this->category = $child;
						$this->maxLevel--;
						if ($this->maxLevel != 0) :
							echo $this->loadTemplate('children');
						endif;
						$this->category = $child->getParent();
						$this->maxLevel++;
					endif; ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php endif;
