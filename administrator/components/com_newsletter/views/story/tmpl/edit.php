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
$text = ($this->task == 'edit' ? JText::_('COM_NEWSLETTER_EDIT') : JText::_('COM_NEWSLETTER_NEW'));
JToolBarHelper::title(JText::_('COM_NEWSLETTER_STORY_'.strtoupper($this->type)) . ': ' . $text, 'addedit.png');

//add buttons to toolbar
JToolBarHelper::save();
JToolBarHelper::cancel();

//instantiate joomla editor
jimport('joomla.html.editor');
$editor = JEditor::getInstance();
?>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_NEWSLETTER_STORY_'.strtoupper($this->type)); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER'); ?>:</td>
					<td><strong><?php echo $this->newsletter->name; ?></strong></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_NEWSLETTER_STORY_TYPE'); ?>:</td>
					<td>
						<?php echo JText::_('COM_NEWSLETTER_STORY_' . ucfirst($this->type));?>
						<input type="hidden" name="type" value="<?php echo strtolower($this->type) ;?>" />
						<input type="hidden" name="story[id]" value="<?php echo $this->story->id; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE'); ?>:</td>
					<td><input type="text" name="story[title]" value="<?php echo $this->escape($this->story->title); ?>" style="width:100%" /></td>
				</tr>
				<?php if ($this->story->id) : ?>
					<tr>
						<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER'); ?>:</td>
						<td>
							<input type="hidden" name="story[order]" value="<?php echo $this->story->order; ?>" />
							<?php echo $this->story->order; ?>
							<span class="hint"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER_HINT'); ?></span>
						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<td class="key" width="200px">
						<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_STORY'); ?>:
						<br /><br />
						<span class="hint">
							<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_STORY_HINT1'); ?>
						</span>
						<br /><br />
						<span class="hint">
							<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_STORY_HINT2'); ?>
						</span>
					</td>
					<td>
						<?php
							$params = array("full_paths"=>true);
							echo $editor->display("story[story]", $this->escape($this->story->story), '100%', '300px', '50', '10', true, '', '', '', $params);
						?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE'); ?></td>
					<td>
						<input type="text" name="story[readmore_title]" value="<?php echo $this->story->readmore_title; ?>" style="width:30%;margin-right:2%" placeholder="<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_TITLE_PLACEHOLDER'); ?>" />
						<input type="text" name="story[readmore_link]" value="<?php echo $this->story->readmore_link; ?>" style="width:67.5%" placeholder="<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_PLACEHOLDER'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</fielset>
	<input type="hidden" name="story[nid]" value="<?php echo $this->newsletter->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
</form>
