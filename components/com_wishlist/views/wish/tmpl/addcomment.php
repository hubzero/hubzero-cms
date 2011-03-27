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
	$category = ($this->level==0) ? 'wish': 'wishcomment';
	
	$class = ' hide';
	if (is_object($this->addcomment)) {
		$class = ($this->addcomment->referenceid == $this->refid && $this->addcomment->category==$category) ? '' : ' hide';
	}
?>
					<div class="addcomment<?php echo $class; ?>" id="comm_<?php echo $this->refid; ?>">
                    	<h3><?php echo JText::_('ACTION_ADD_COMMENT'); ?></h3>
						<form action="index.php" method="post" id="commentform_<?php echo $this->refid; ?>" enctype="multipart/form-data">
							<fieldset>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="listid" value="<?php echo $this->listid; ?>" />
                                <input type="hidden" name="wishid" value="<?php echo $this->wishid; ?>" />
								<input type="hidden" name="active" value="answers" />
								<input type="hidden" name="task" value="savereply" />
								<input type="hidden" name="referenceid" value="<?php echo $this->refid; ?>" />
								<input type="hidden" name="cat" value="<?php echo $category; ?>" />
								<label>
									<input class="option" type="checkbox" name="anonymous" value="1" /> 
									<?php echo JText::_('POST_COMMENT_ANONYMOUSLY'); ?>
								</label>
								<label>
									<textarea name="comment" rows="4" cols="50" class="commentarea"><?php echo JText::_('COM_WISHLIST_ENTER_COMMENTS'); ?></textarea>
								</label>
							</fieldset>
                            <fieldset>
                             <div>
                            	<label>
                               		 <?php echo JText::_('ACTION_ATTACH_FILE'); ?>
									<input type="file" name="upload"  />								
								</label>
                                <label>
                               		 <?php echo JText::_('ACTION_ATTACH_FILE_DESC'); ?>
									<input type="text" name="description" value="" />								
								</label>
                             </div>
                            </fieldset>
							<p><input type="submit" value="<?php echo JText::_('POST_COMMENT'); ?>" /> 
                            <a href="javascript:void(0);" class="closeform" id="close_<?php echo $this->refid; ?>"><?php echo JText::_('CANCEL'); ?></a></p>
						</form>
					</div>
<?php
}
?>