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

					<div class="item-color" style="background-color: #<?php echo $category->get('color'); ?>"></div>

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