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

//include modal
JHTML::_('behavior.modal');                              

//set title
JToolBarHelper::title('<a href="index.php?option='.$this->option.'">' . JText::_( 'Newsletters' ) . '</a>', 'newsletter.png');

//add buttons to toolbar
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::custom('duplicate', 'copy', '', 'Copy');
JToolBarHelper::deleteList('Are you sure you want to delete the selected Newsletter(s)?', 'delete');
JToolBarHelper::spacer();
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::custom('preview', 'preview', '', 'Preview');
JToolBarHelper::custom('sendtest', 'sendtest', '', 'Test Send');
JToolBarHelper::custom('sendnewsletter', 'send', '', 'Send');
JToolBarHelper::spacer();
JToolBarHelper::preferences($this->option, '550');
?>



<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	if (pressbutton == 'preview')
	{
		var id = '',
			ids = document.getElementsByName('id[]');
		for (var i=0; i< ids.length;i++)
		{
			if (id == '' && ids[i].type == 'checkbox' && ids[i].checked)
			{
				id = parseInt(ids[i].value);
			}
		}
		
		HUB.Administrator.Newsletter.newsletterPreview( id );
		return;
	}
	
	// do field validation
	submitform( pressbutton );
}
</script>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->newsletters); ?>);" /></th>
				<th><?php echo JText::_('Newsletter Name'); ?></th>
				<th><?php echo JText::_('Format'); ?></th>
				<th><?php echo JText::_('Template'); ?></th>
				<th><?php echo JText::_('Public'); ?></th>
				<th><?php echo JText::_('Sent'); ?></th>
				<th><?php echo JText::_('Tracking'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->newsletters) > 0) : ?>
				<?php foreach($this->newsletters as $k => $newsletter) : ?>
					<tr>
						<td width="30px">
							<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $newsletter->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td><?php echo $newsletter->name; ?></td>
						<td><?php echo ($newsletter->type == 'html') ? 'HTML' : 'Plain Text'; ?></td>
						<td>
							<?php
								$activeTemplate = '';
								if ($newsletter->template == '-1')
								{
									$activeTemplate = 'No Template (Content override with template)';
								}
								else
								{
									foreach ($this->templates as $template)
									{
										if ($template->id == $newsletter->template)
										{
											$activeTemplate  = $template->name;
											//$activeTemplate .= ' (<a href="'.JRoute::_('index.php?option=com_newsletter&controller=template&task=edit&id='.$template->id).'">edit</a>)';
										}
									}
								}
								
								echo ($activeTemplate) ? $activeTemplate : '<em>No Template Found</em>';
							?>
						</td>
						<td width="50px">
							<?php if ($newsletter->published) : ?>
								<font color="green"><?php echo JText::_('Yes'); ?></font>
							<?php else : ?>
								<font color="red"><?php echo JText::_('No'); ?></font>
							<?php endif; ?>	
						</td>
						<td width="50px">
							<?php if ($newsletter->sent) : ?>
								<font color="green"><?php echo JText::_('Yes'); ?></font>
							<?php else : ?>
								<font color="red"><?php echo JText::_('No'); ?></font>
							<?php endif; ?>	
						</td>
						<td width="50px">
							<?php if ($newsletter->tracking) : ?>
								<strong><?php echo JText::_('Yes'); ?></strong>
							<?php else : ?>
								<em><font color="red"><?php echo JText::_('No'); ?></font></em>
							<?php endif; ?>	
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7">
						<?php echo JText::_('Currently there are no newsletters.'); ?>
						<a onclick="javascript:submitbutton('add');" href="#"><?php echo JText::_('Click here to create a new one!'); ?></a>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="add" />
	<input type="hidden" name="boxchecked" value="0" />
</form>	