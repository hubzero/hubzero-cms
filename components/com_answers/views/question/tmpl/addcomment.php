<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!$this->juser->get('guest')) {
	$category = ($this->level==0) ? 'answer': 'answercomment';
	
	$class = ' hide';
	if (is_object($this->addcomment)) {
		$class = ($this->addcomment->referenceid == $this->row->id && $this->addcomment->category==$category) ? '' : ' hide';
	}
?>
					<div class="addcomment<?php echo $class; ?>">
						<form action="index.php" method="post" id="commentform_<?php echo $this->row->id; ?>">
							<fieldset>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="rid" value="<?php echo $this->question->id; ?>" />
								<input type="hidden" name="active" value="answers" />
								<input type="hidden" name="task" value="savereply" />
								<input type="hidden" name="referenceid" value="<?php echo $this->row->id; ?>" />
								<input type="hidden" name="category" value="<?php echo $category; ?>" />
								
								<label>
									<?php echo JText::_('COM_ANSWERS_ENTER_COMMENTS'); ?>
									<textarea name="comment" rows="4" cols="50" class="commentarea" placeholder="<?php echo JText::_('COM_ANSWERS_ENTER_COMMENTS'); ?>"></textarea>
								</label>
								
								<label class="reply-anonymous-label">
									<input class="option" type="checkbox" name="anonymous" value="1" /> 
									<?php echo JText::_('COM_ANSWERS_POST_COMMENT_ANONYMOUSLY'); ?>
								</label>
								
								<p class="submit">
									<input type="submit" value="<?php echo JText::_('COM_ANSWERS_POST_COMMENT'); ?>" /> 
									<a class="closeform cancelreply" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id.'#c'.$this->row->id); ?>"><?php echo JText::_('COM_ANSWERS_CANCEL'); ?></a>
								</p>
							</fieldset>
						</form>
					</div><!-- / .addcomment -->
<?php
}
?>
