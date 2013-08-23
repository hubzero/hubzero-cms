<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<h3 class="section-header">
	<a name="questions"></a>
	<?php echo JText::_('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?>
</h3>
<div class="section">
	<?php foreach ($this->getErrors() as $error) { ?>
	<p class="error"><?php echo $error; ?></p>
	<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=questions'); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend><?php echo JText::_('COM_ANSWERS_YOUR_QUESTION'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
			<input type="hidden" name="active" value="questions" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="funds" value="<?php echo $this->funds; ?>" />

			<input type="hidden" name="tag" value="<?php echo $this->tag; ?>" />
			<input type="hidden" name="question[id]" value="<?php echo $this->row->id; ?>" />

			<label for="field-anonymous">
				<input class="option" type="checkbox" name="question[anonymous]" id="field-anonymous" value="1" /> 
				<?php echo JText::_('COM_ANSWERS_POST_QUESTION_ANON'); ?>
			</label>
			<label>
				<?php echo JText::_('COM_ANSWERS_TAGS'); ?>: <span class="required"><?php echo JText::_('COM_ANSWERS_REQUIRED'); ?></span><br />
				<?php
				JPluginHelper::importPlugin( 'hubzero' );
				$dispatcher =& JDispatcher::getInstance();
				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', '')) );

				if (count($tf) > 0) {
								echo $tf[0];
				} else { ?>
				<textarea name="tags" id="tags-men" rows="6" cols="35"></textarea>
				<?php } ?>
			</label>
			<label for="field-subject">
				<?php echo JText::_('COM_ANSWERS_ASK_ONE_LINER'); ?>: <span class="required"><?php echo JText::_('COM_ANSWERS_REQUIRED'); ?></span><br />
				<input type="text" name="question[subject]" id="field-subject" value="<?php echo $this->escape($this->row->subject); ?>" />
			</label>
			<label for="field-question">
				<?php echo JText::_('COM_ANSWERS_ASK_DETAILS'); ?>:<br />
				<?php
				ximport('Hubzero_Wiki_Editor');
				$editor =& Hubzero_Wiki_Editor::getInstance();
				echo $editor->display('question[question]', 'field-question', $this->escape(stripslashes($this->row->question)), '', '50', '10');
				?>
				<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
			</label>
			</label>
		<?php if ($this->banking) { ?>
			<label for="field-reward">
				<?php echo JText::_('COM_ANSWERS_ASSIGN_REWARD'); ?>:<br />
				<input type="text" name="question[reward]" id="field-reward" value="" size="5" <?php if ($this->funds <= 0) { echo 'disabled="disabled" '; } ?>/> 
				<?php echo JText::_('COM_ANSWERS_YOU_HAVE'); ?> <strong><?php echo $this->funds; ?></strong> <?php echo JText::_('COM_ANSWERS_POINTS_TO_SPEND'); ?>
			</label>
		<?php } else { ?>
			<input type="hidden" name="question[reward'" value="0" />
		<?php } ?>
			<input class="option" type="hidden" name="question[email]" value="1" checked="checked" />
		</fieldset>

		<p class="submit"><input type="submit" value="<?php echo JText::_('COM_ANSWERS_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .section -->
