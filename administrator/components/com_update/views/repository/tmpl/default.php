<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('CMS Updater: repository'));
//JToolBarHelper::custom('rollback', 'back', '', 'Rollback repository', false);
//JToolBarHelper::spacer();
JToolBarHelper::custom('update', 'purge', '', 'Update repository', false);

$this->css();
?>

<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="updateRepositoryForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('Search'); ?>: </label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" placeholder="search" />

		<label for="status"><?php echo JText::_('Status'); ?>:</label>
		<select name="status" id="status">
			<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('[ all ]'); ?></option>
			<option value="upcoming"<?php echo ($this->filters['status'] == 'upcoming') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Upcoming'); ?></option>
			<option value="installed"<?php echo ($this->filters['status'] == 'installed') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Installed'); ?></option>
		</select>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>

	<div class="clr"></div>

	<table id="repository-list" class="adminlist">
		<thead>
			<tr>
				<th>Author</th>
				<th>Date</th>
				<th>Status</th>
				<th>Subject</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($this->rows) > 0) : ?>
				<?php foreach ($this->rows as $hash => $data) : ?>
					<tr class="<?php echo (substr($hash, 0, 1) == '*') ? 'upcoming' : 'installed'; ?>">
						<td><?php echo $data->name; ?></td>
						<td><?php echo $data->date; ?></td>
						<td><?php echo (substr($hash, 0, 1) == '*') ? 'upcoming' : 'installed'; ?></td>
						<td><?php echo $data->subject; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php elseif (count($this->rows) == 0 && $this->filters['status'] == 'upcoming' && empty($this->filters['search'])) : ?>
				<tr>
					<td colspan="4" class="no-rows">The repository is up-to-date.</td>
				</tr>
			<?php else : ?>
				<tr>
					<td colspan="4" class="no-rows">No matching records.</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_('form.token'); ?>
</form>
