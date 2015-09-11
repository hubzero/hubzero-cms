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

// build array of positions
$positions = array();
foreach ($this->modules as $module)
{
	if (!in_array($module->get('position'), $positions) && $module->get('position') != '')
	{
		$positions[] = $module->get('position');
	}
}
?>
<ul class="toolbar toolbar-modules">
	<li class="new">
		<a class="btn icon-add" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=add'); ?>">
			<?php echo Lang::txt('COM_GROUPS_PAGES_NEW_MODULE'); ?>
		</a>
	</li>
	<li class="filter">
		<select>
			<option value=""><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_FILTER'); ?></option>
			<?php foreach ($positions as $position) : ?>
				<option value="<?php echo $position; ?>"><?php echo $position; ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li class="filter-search-divider"><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_OR'); ?></li>
	<li class="search">
		<input type="text" placeholder="<?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_SEARCH'); ?>" />
	</li>
</ul>

<ul class="item-list modules">
	<?php if ($this->modules->count() > 0) : ?>
		<?php foreach ($this->modules as $module) : ?>
			<?php
			$class = 'position-' . $module->get('position');
			if ($module->get('approved') == 0)
			{
				$class .= ' not-approved';
			}
			?>
			<li>
				<div class="item-container <?php echo $class; ?>">
					<div class="item-title">
						<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=edit&moduleid='.$module->get('id')); ?>">
							<?php echo $module->get('title'); ?>
						</a>

						<?php
							$pages = array();
							$menus = $module->menu('list');
							foreach ($menus as $menu)
							{
								$pages[] = $menu->getPageTitle();
							}
						?>
					</div>

					<div class="item-sub">
						<?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_INCLUDED_ON', implode(', ', $pages)); ?>
					</div>

					<div class="item-position">
						<span><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_POSITION'); ?>:</span>
						<?php echo $module->get('position'); ?>
					</div>

					<?php if ($module->get('approved') == 0) : ?>
						<div class="item-approved">
							<?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_PENDING_APPROVAL'); ?>
						</div>
					<?php endif; ?>

					<div class="item-state">
						<?php if ($module->get('state') == 0) : ?>
							<a class="unpublished tooltips" title="<?php echo Lang::txt('COM_GROUPS_PAGES_PUBLISH_MODULE'); ?>" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=publish&moduleid='.$module->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_PUBLISH_MODULE'); ?></a>
						<?php else : ?>
							<a class="published tooltips" title="<?php echo Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_MODULE'); ?>" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=unpublish&moduleid='.$module->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_MODULE'); ?></a>
						<?php endif; ?>
					</div>

					<div class="item-controls btn-group dropdown">
						<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=edit&moduleid='.$module->get('id')); ?>" class="btn">
							<?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_MODULE'); ?>
						</a>
						<span class="btn dropdown-toggle"></span>
						<ul class="dropdown-menu">
							<li><a class="icon-edit" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=edit&moduleid='.$module->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_EDIT_MODULE'); ?></a></li>
							<li class="divider"></li>
							<?php if ($module->get('state') == 0) : ?>
								<li><a class="icon-ban-circle" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=publish&moduleid='.$module->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_PUBLISH_MODULE'); ?></a></li>
							<?php else : ?>
								<li><a class="icon-success" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=unpublish&moduleid='.$module->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_MODULE'); ?></a></li>
							<?php endif; ?>
							<li class="divider"></li>
							<li><a class="icon-delete" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=delete&moduleid='.$module->get('id')); ?>"> <?php echo Lang::txt('COM_GROUPS_PAGES_DELETE_MODULE'); ?></a></li>
						</ul>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<li class="no-results">
			<p><?php echo Lang::txt('COM_GROUPS_PAGES_NO_MODULES'); ?></p>
		</li>
	<?php endif; ?>
</ul>