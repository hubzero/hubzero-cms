<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
