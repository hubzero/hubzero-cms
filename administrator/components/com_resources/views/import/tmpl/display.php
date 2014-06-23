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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_RESOURCES_IMPORT_TITLE_IMPORTS'), 'import.png');

JToolBarHelper::help('import.html', true);
JToolBarHelper::spacer();
JToolBarHelper::custom('run', 'script', 'script', 'COM_RESOURCES_RUN');
JToolBarHelper::custom('runtest', 'script', 'script', 'COM_RESOURCES_TEST_RUN');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php?option=com_resources&amp;controller=import" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col" width="20px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->imports->count(); ?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_FIELD_NAME'); ?></th>
				<th scope="col"><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_FIELD_NUMRECORDS'); ?></th>
				<th scope="col" width="200px"><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_FIELD_CREATED'); ?></th>
				<th scope="col" width="200px"><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_FIELD_LASTRUN'); ?></th>
				<th scope="col" width="30px"><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_FIELD_RUNCOUNT'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ($this->imports->count() > 0) : ?>
				<?php foreach ($this->imports as $i => $import) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $import->get('id'); ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<?php echo $this->escape($import->get('name')); ?> <br />
							<span class="hint">
								<?php echo nl2br($this->escape($import->get('notes'))); ?>
							</span>
						</td>
						<td>
							<?php
								if ($import->get('count'))
								{
									echo number_format($this->escape($import->get('count')));
								}
							?>
						</td>
						<td>
							<strong><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_ON'); ?></strong>
							<?php
								$created_on = JHTML::_('date', $import->get('created_at'), 'm/d/Y @ g:i a');
								echo $created_on . '<br />';
							?>
							<strong><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_BY'); ?></strong>
							<?php
								if ($created_by = Hubzero\User\Profile::getInstance($import->get('created_by')))
								{
									echo $created_by->get('name');
								}
							?>
						</td>
						<td>
							<?php
								$lastRun = $import->runs('list', array(
									'import' => $import->get('id'),
									'dry_run' => 0,
									''
								))->first();
							?>
							<?php if ($lastRun) : ?>
								<strong><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_ON'); ?></strong>
								<?php
									$created_on = JHTML::_('date', $lastRun->get('ran_at'), 'm/d/Y @ g:i a');
									echo $created_on . '<br />';
								?>
								<strong><?php echo JText::_('COM_RESOURCES_IMPORT_DISPLAY_BY'); ?></strong>
								<?php
									if ($created_by = Hubzero\User\Profile::getInstance($lastRun->get('ran_by')))
									{
										echo $created_by->get('name');
									}
								?>
							<?php else: ?>
								n/a
							<?php endif; ?>
						</td>
						<td>
							<?php
								$runs = $import->runs('list', array(
									'import' => $import->get('id'),
									'dry_run' => 0
								));
								echo $runs->count();
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6"><?php echo JText::_('COM_RESOURCES_IMPORT_NONE'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>