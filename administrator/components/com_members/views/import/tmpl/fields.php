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

$this->css('import')
     ->js('import');

JRequest::setVar('hidemainmenu', 1);

$canDo = MembersHelper::getActions('component');

// set title
$title  = ($this->import->get('id')) ? JText::_('COM_MEMBERS_IMPORT_TITLE_EDIT') : JText::_('COM_MEMBERS_IMPORT_TITLE_ADD');

JToolBarHelper::title(JText::_('COM_MEMBERS') . ': ' . $title, 'import.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
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

<?php foreach ($this->getErrors() as $error) : ?>
	<p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>

<form action="<?php echo JRoute::_('index.php?option=com_members&controller=import&task=save'); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="col width-60 fltlft">

		<p class="warning"><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELDSET_MAPPING_REQUIRED'); ?></p>

		<?php
		$this->view('_fieldmap')
			->set('import', $this->import)
			->display();
		?>

	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_ID'); ?></th>
					<td><?php echo $this->import->get('id'); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDBY'); ?></th>
					<td>
						<?php
							if ($created_by = Hubzero\User\Profile::getInstance($this->import->get('created_by')))
							{
								echo $created_by->get('name');
							}
						?>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDON'); ?></th>
					<td>
						<?php
							echo JHTML::_('date', $this->import->get('created_at'), 'm/d/Y @ g:i a');
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="import[id]" value="<?php echo $this->import->get('id'); ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>