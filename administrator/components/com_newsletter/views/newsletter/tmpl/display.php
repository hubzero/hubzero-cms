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
JToolBarHelper::title(JText::_('COM_NEWSLETTER'), 'newsletter.png');

//add buttons to toolbar
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::custom('duplicate', 'copy', '', 'COM_NEWSLETTER_TOOLBAR_COPY');
JToolBarHelper::deleteList('COM_NEWSLETTER_DELETE_CHECK', 'delete');
JToolBarHelper::spacer();
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::custom('preview', 'preview', '', 'COM_NEWSLETTER_TOOLBAR_PREVIEW');
JToolBarHelper::custom('sendtest', 'sendtest', '', 'COM_NEWSLETTER_TOOLBAR_SEND_TEST');
JToolBarHelper::custom('sendnewsletter', 'send', '', 'COM_NEWSLETTER_TOOLBAR_SEND');
JToolBarHelper::spacer();
JToolBarHelper::preferences($this->option, '550');

// add js
$this->js();
?>

<script type="text/javascript">

Joomla.submitbutton = function(pressbutton)
{
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
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_NAME'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_FORMAT'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEMPLATE'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_PUBLIC'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SENT'); ?></th>
				<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TRACKING'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->newsletters) > 0) : ?>
				<?php foreach ($this->newsletters as $k => $newsletter) : ?>
					<tr>
						<td width="30px">
							<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $newsletter->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td><?php echo $newsletter->name; ?></td>
						<td><?php echo ($newsletter->type == 'html') ? JText::_('COM_NEWSLETTER_FORMAT_HTML') : JText::_('COM_NEWSLETTER_FORMAT_PLAIN'); ?></td>
						<td>
							<?php
								$activeTemplate = '';
								if ($newsletter->template == '-1')
								{
									$activeTemplate = JText::_('COM_NEWSLETTER_NO_TEMPLATE');
								}
								else
								{
									foreach ($this->templates as $template)
									{
										if ($template->id == $newsletter->template)
										{
											$activeTemplate  = $template->name;
										}
									}
								}

								echo ($activeTemplate) ? $activeTemplate : JText::_('COM_NEWSLETTER_NO_TEMPLATE_FOUND');
							?>
						</td>
						<td width="50px">
							<?php if ($newsletter->published) : ?>
								<font color="green"><?php echo JText::_('JYES'); ?></font>
							<?php else : ?>
								<font color="red"><?php echo JText::_('JNO'); ?></font>
							<?php endif; ?>
						</td>
						<td width="50px">
							<?php if ($newsletter->sent) : ?>
								<font color="green"><?php echo JText::_('JYES'); ?></font>
							<?php else : ?>
								<font color="red"><?php echo JText::_('JNO'); ?></font>
							<?php endif; ?>
						</td>
						<td width="50px">
							<?php if ($newsletter->tracking) : ?>
								<strong><?php echo JText::_('JYES'); ?></strong>
							<?php else : ?>
								<em><font color="red"><?php echo JText::_('JNO'); ?></font></em>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7">
						<?php echo JText::_('COM_NEWSLETTER_NO_NEWSLETTER'); ?>
						<a onclick="javascript:submitbutton('add');" href="#"><?php echo JText::_('COM_NEWSLETTER_CREATE_NEWSLETTER'); ?></a>
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