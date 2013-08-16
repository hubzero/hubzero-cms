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

$canDo = CitationsHelper::getActions('sponsor');

JToolBarHelper::title(JText::_('Citation Sponsors'), 'citation.png');
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = $('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm">
	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th width="5%"><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('Sponsor'); ?></th>
				<th><?php echo JText::_('Link'); ?></th>
				<th><?php echo JText::_('Image'); ?></th>
				<th><?php echo JText::_('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->sponsors) > 0) : ?>
				<?php foreach ($this->sponsors as $sponsor) : ?>
					<tr>
						<td><?php echo $sponsor['id']; ?></td>
						<td><?php echo $sponsor['sponsor']; ?></td>
						<td><?php echo $sponsor['link']; ?></td>
						<td><?php echo $sponsor['image']; ?></td>
						<td>
<?php if ($canDo->get('core.edit')) { ?>
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $sponsor['id']); ?>">Edit</a> | 
<?php } ?>
<?php if ($canDo->get('core.delete')) { ?>
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=remove&id[]=' . $sponsor['id']); ?>">Delete</a>
<?php } ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="5">There are currently no citation sponsors</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_('form.token'); ?>
</form>