<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="sidebox<?php if (count($this->items) == 0) { echo ' suggestions'; } ?>">
		<h4><a href="<?php echo Route::url($this->model->link('todo')); ?>" class="hlink" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower(Lang::txt('COM_PROJECTS_TAB_TODO')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_TAB_TODO')); ?></a>
<?php if (count($this->items) > 0) { ?>
	<span><a href="<?php echo Route::url($this->model->link('todo')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL')); ?> </a></span>
<?php } ?>
</h4>
<?php if (count($this->items) == 0) { ?>
		<p class="s-todo"><a href="<?php echo Route::url($this->model->link('todo')); ?>"><?php echo Lang::txt('COM_PROJECTS_ADD_TODO'); ?></a></p>
<?php } else { ?>
	<ul>
		<?php foreach ($this->items as $row) {
			$overdue = $row->isOverdue();
			$color = $row->get('color');
			$class = $color ? 'pin_' . $color : 'pin_grey';

			$dueClass = $overdue ? ' urgency' : '';
			if ($row->due('date'))
			{
				$due = Lang::txt('COM_PROJECTS_DUE') . ' ' . $row->due('date');
			}
		?>
	<li>
		 <a href="<?php echo Route::url($this->model->link('todo') . '&action=view&todoid=' . $row->get('id')); ?>" title="<?php echo htmlentities($row->get('content')); ?>">
		<?php echo \Hubzero\Utility\String::truncate($row->get('content'), 35); ?></a>
		 <span class="block faded mini">
			<?php if ($row->creator('id')) { ?>
			<span><?php echo Lang::txt('COM_PROJECTS_BY') . ' ' . \Components\Projects\Helpers\Html::shortenName($row->creator('name')); ?></span> |
			<?php } ?>
			<?php if ($row->due('date')) { ?>
			<span class="duetd<?php echo $dueClass; ?>"><?php echo $due; ?></span> |
			<?php } ?>
			<span><?php echo $row->comments('count') . ' '; echo $row->comments('count') == 1 ? strtolower(Lang::txt('COM_PROJECTS_COMMENT')) : Lang::txt('COM_PROJECTS_COMMENTS'); ?></span>
		</span>
	   </li><?php } ?>
	</ul><?php } ?>
</div>