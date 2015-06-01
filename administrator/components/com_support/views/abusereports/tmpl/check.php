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
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('COM_SUPPORT_TICKETS') . ': ' . JText::_('COM_SUPPORT_ABUSE_CHECK'), 'support.png');
JToolBarHelper::custom('check', 'purge', '', 'COM_SUPPORT_CHECK', false);

JHTML::_('behavior.framework');

$this->view('_submenu')->display();
?>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check'); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-sample"><?php echo JText::_('COM_SUPPORT_ABUSE_SAMPLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<textarea name="sample" id="field-sample" cols="35" rows="20"><?php echo $this->escape($this->sample); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<?php if ($this->results) { ?>
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('COM_SUPPORT_ABUSE_SPAM_REPORT'); ?></span></legend>
				<table>
					<tbody>
						<?php
						foreach ($this->results as $result)
						{
							?>
							<tr>
								<th><?php echo $result['service']; ?></th>
								<td><?php echo ($result['is_spam'] ? '<span style="color:red">spam</span>' : '<span style="color:green">ham</span>'); ?></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</fieldset>
		<?php } else { ?>
			<p class="info"><?php echo JText::_('COM_SUPPORT_ABUSE_CHECK_ABOUT'); ?></p>
		<?php } ?>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="check" />

	<?php echo JHTML::_('form.token'); ?>
</form>
