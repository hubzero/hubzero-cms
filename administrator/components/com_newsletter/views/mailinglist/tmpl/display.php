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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set title
JToolBarHelper::title(JText::_('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS'), 'list.png');

//add buttons to toolbar
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList('COM_NEWSLETTER_MAILINGLIST_DELETE_CHECK', 'delete');
JToolBarHelper::spacer();
JToolBarHelper::custom('manage', 'user', '', 'COM_NEWSLETTER_TOOLBAR_MANAGE');
JToolBarHelper::custom('export', 'export', '', 'COM_NEWSLETTER_TOOLBAR_EXPORT');
JToolBarHelper::spacer();
JToolBarHelper::preferences($this->option, '550');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->lists); ?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_NAME'); ?></th>
				<th scope="col"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_PRIVACY'); ?></th>
				<th scope="col"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_ACTIVE_SUBSCRIBERS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_NEWSLETTER_MAILINGLIST_TOTAL_SUBSCRIBERS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->lists) > 0) : ?>
				<?php foreach ($this->lists as $k => $list) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $list->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<?php echo $this->escape($list->name); ?>
						</td>
						<td>
							<span class="access <?php echo ($list->private) ? 'private' : 'public'; ?>">
								<?php echo ($list->private) ? JText::_('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PRIVATE') : JText::_('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PUBLIC'); ?>
							</span>
						</td>
						<td>
							<?php echo $list->active_count; ?>
						</td>
						<td>
							<?php echo $list->total_count; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="5">
						<?php echo JText::sprintf('COM_NEWSLETTER_MAILINGLIST_NO_LISTS', "javascript:submitbutton('add');"); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="task" value="add" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>