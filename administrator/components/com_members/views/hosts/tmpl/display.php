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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div id="hosts">
	<form action="index.php" method="post">
		<table>
			<tbody>
				<tr>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
						<input type="hidden" name="task" value="add" />

						<input type="text" name="host" value="" />
						<input type="submit" value="<?php echo JText::_('COM_MEMBERS_HOSTS_ADD'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<br />

		<table class="paramlist admintable">
			<tbody>
				<?php
				if (count($this->rows) > 0)
				{
					foreach ($this->rows as $row)
					{
						?>
						<tr>
							<td class="paramlist_key"><?php echo $row; ?></td>
							<td class="paramlist_value"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=remove&host=' . $row . '&id=' . $this->id . '&' . JUtility::getToken() . '=1'); ?>"><?php echo JText::_('JACTION_DELETE'); ?></a></td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>

		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>