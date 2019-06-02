<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<ul class="toolbar toolbar-categories">
	<li class="new">
		<a class="btn icon-add" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=add'); ?>">
			<?php echo Lang::txt('COM_GROUPS_PAGES_NEW_CATEGORY'); ?>
		</a>
	</li>
</ul>

<ul class="item-list categories">
	<?php if ($this->categories->count() > 0) : ?>
		<?php foreach ($this->categories as $category) : ?>
			<li>
				<div class="item-container">
					<div class="item-title">
						<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=edit&categoryid='.$category->get('id')); ?>">
							<?php echo $category->get('title'); ?>
						</a>
					</div>

					<div class="item-sub">
						<?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_X_PAGES', $category->getPages('count')); ?>
					</div>

					<?php $this->css('.item-list .category-' . $category->get('id') . ' { background-color: #' . $category->get('color') . '; }'); ?>
					<div class="item-color category-<?php echo $category->get('id'); ?>"></div>

					<div class="item-controls btn-group dropdown">
						<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=edit&categoryid='.$category->get('id')); ?>" class="btn">
							<?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_CATEGORY'); ?>
						</a>
						<span class="btn dropdown-toggle"></span>
						<ul class="dropdown-menu">
							<li><a class="icon-edit" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=edit&categoryid='.$category->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY'); ?></a></li>
							<li class="divider"></li>
							<li><a class="icon-delete" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=categories&task=delete&categoryid='.$category->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_DELETE_CATEGORY'); ?></a></li>
						</ul>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<li class="no-results">
			<p><?php echo Lang::txt('COM_GROUPS_PAGES_NO_CATEGORIES'); ?></p>
		</li>
	<?php endif; ?>
</ul>