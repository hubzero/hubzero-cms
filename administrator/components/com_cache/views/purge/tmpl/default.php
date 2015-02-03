<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

JToolBarHelper::title(JText::_('COM_CACHE_PURGE_EXPIRED_CACHE'), 'purge.png');
JToolBarHelper::custom('purge', 'delete.png', 'delete_f2.png', 'COM_CACHE_PURGE_EXPIRED', false);
JToolBarHelper::divider();
if (JFactory::getUser()->authorise('core.admin', 'com_cache'))
{
	JToolBarHelper::preferences('com_cache');
	JToolBarHelper::divider();
}
JToolBarHelper::help('purge_expired');
?>

<form action="<?php echo JRoute::_('index.php?option=com_cache'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('COM_CACHE_PURGE_EXPIRED_ITEMS'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<p class="mod-purge-instruct"><?php echo JText::_('COM_CACHE_PURGE_INSTRUCTIONS'); ?></p>
					<p class="warning"><?php echo JText::_('COM_CACHE_RESOURCE_INTENSIVE_WARNING'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />

	<?php echo JHtml::_('form.token'); ?>
</form>
