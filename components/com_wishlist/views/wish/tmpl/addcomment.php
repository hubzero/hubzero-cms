<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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