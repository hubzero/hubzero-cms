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
$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Newsletter Template') . ': <small><small>[ ' . $text . ' ]</small></small>', 'template.png');

//add toolbar buttons
JToolBarHelper::help('template.html', true);
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_( $text . ' Newsletter Template'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<th><?php echo JText::_('Name:'); ?></th>
					<td><input type="text" name="template[name]" value="<?php echo $this->template->name; ?>" /></td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('Primary Title Color:'); ?><br />
						<span class="hint"><?php echo JText::_('Hex Color Code (ex. #FF0000 -> Red)'); ?></span>
					</th>
					<td><input type="text" name="template[primary_title_color]" value="<?php echo $this->template->primary_title_color; ?>" /></td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('Primary Text Color:'); ?><br />
						<span class="hint"><?php echo JText::_('Hex Color Code (ex. #FF0000 -> Red)'); ?></span>
					</th>
					<td><input type="text" name="template[primary_text_color]" value="<?php echo $this->template->primary_text_color; ?>" /></td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('Secondary Title Color:'); ?><br />
						<span class="hint"><?php echo JText::_('Hex Color Code (ex. #FF0000 -> Red)'); ?></span>
					</th>
					<td><input type="text" name="template[secondary_title_color]" value="<?php echo $this->template->secondary_title_color; ?>" /></td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('Secondary Text Color:'); ?><br />
						<span class="hint"><?php echo JText::_('Hex Color Code (ex. #FF0000 -> Red)'); ?></span>
					</th>
					<td><input type="text" name="template[secondary_text_color]" value="<?php echo $this->template->secondary_text_color; ?>" /></td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('Template:') ?><br /><br />
						<?php if ($this->config->get('template_tips')) : ?>
							<span class="hint">
								<?php echo JText::_('Need Help Building Your Template?') ?><br />
								<a target="_blank" href="<?php echo $this->config->get('template_tips'); ?>"><?php echo JText::_('Tips for Creating Your Template'); ?></a>
							</span>
							<br /><br />
						<?php endif; ?>
						<?php if ($this->config->get('template_templates')) : ?>
							<span class="hint">
								<?php echo JText::_('Need Examples and/or Free Templates'); ?><br />
								<a target="_blank" href="<?php echo $this->config->get('template_templates'); ?>"><?php echo JText::_('Check out these templates'); ?></a>
							</span>
							<br /><br />
						<?php endif; ?>
						<span class="hint"><?php echo JText::_('Placeholders that can be used:'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{LINK}} = Link to HUB'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{ALIAS}} = Newsletter Alias'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{TITLE}} = Newsletter Title'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{ISSUE}} = Newsletter Issue'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{PRIMARY_STORIES}} = Newsletter Primary Stories'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{SECONDARY_STORIES}} = Newsletter Secondary Stories'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{COPYRIGHT}} = Current Year (copyright)'); ?></span><br />
						<span class="hint"><?php echo JText::_('{{UNSUBSCRIBE_LINK}} = 1 Click Unsubscribe Link'); ?></span><br />
					</th>
					<td><textarea name="template[template]" cols="100" rows="30"><?php echo $this->escape( $this->template->template ); ?></textarea>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="template[id]" value="<?php echo $this->template->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
</form>