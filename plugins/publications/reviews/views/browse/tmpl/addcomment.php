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
	$category = ($this->level==0) ? 'review': 'reviewcomment';
	
	$class = ' hide';
	if (is_object($this->addcomment)) {
		$class = ($this->addcomment->referenceid == $this->row->id && $this->addcomment->category==$category) ? '' : ' hide';
	}
?>
					<div class="addcomment<?php echo $class; ?>">
						<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->publication->id.'&active=reviews#c'.$this->row->id); ?>" method="post" id="commentform_<?php echo $this->row->id; ?>">
							<fieldset>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="rid" value="<?php echo $this->publication->id; ?>" />
								<input type="hidden" name="active" value="reviews" />
								<input type="hidden" name="action" value="savereply" />
								<input type="hidden" name="referenceid" value="<?php echo $this->row->id; ?>" />
								<input type="hidden" name="category" value="<?php echo $category; ?>" />
								<label>
									<textarea name="comment" rows="4" cols="50" class="commentarea"><?php echo JText::_('PLG_PUBLICATION_REVIEWS_ENTER_COMMENTS'); ?></textarea>
								</label>
								<label class="reply-anonymous-label">
									<input class="option" type="checkbox" name="anonymous" value="1" /> 
									<?php echo JText::_('PLG_PUBLICATION_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
								</label>
								<p class="submit">
									<input type="submit" value="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_POST_COMMENT'); ?>" /> 
									<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->publication->id.'&active=reviews#c'.$this->row->id); ?>" class="closeform cancelreply"><?php echo JText::_('PLG_PUBLICATION_REVIEWS_CANCEL'); ?></a>
								</p>
							</fieldset>
						</form>
					</div>
<?php
}
?>