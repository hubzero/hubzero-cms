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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!$this->juser->get('guest')) 
{
	$class = ' hide';
	if (is_object($this->addcomment)) 
	{
		$class = ($this->addcomment->parent == $this->row->id) ? '' : ' hide';
	}
	?>
	<div class="addcomment comment-add<?php echo $class; ?>" id="commentform_<?php echo $this->row->id; ?>">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=reviews#c'.$this->row->id); ?>" method="post" id="cform<?php echo $this->row->id; ?>">
			<fieldset>
				<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
				<input type="hidden" name="rid" value="<?php echo $this->escape($this->resource->id); ?>" />
				<input type="hidden" name="active" value="reviews" />
				<input type="hidden" name="action" value="savereply" />
				<input type="hidden" name="parent" value="<?php echo ($this->level ? $this->escape($this->row->id) : 0); ?>" />
				<input type="hidden" name="item_id" value="<?php echo $this->escape($this->resource->id); ?>" />
				<input type="hidden" name="item_type" value="review" />

				<label for="field-content-<?php echo $this->row->id; ?>">
					<textarea name="content" id="field-content-<?php echo $this->row->id; ?>" rows="4" cols="50" class="commentarea"><?php echo JText::_('PLG_RESOURCES_REVIEWS_ENTER_COMMENTS'); ?></textarea>
				</label>

				<label class="reply-anonymous-label" for="field-anonymous-<?php echo $this->row->id; ?>">
					<input class="option" type="checkbox" name="anonymous" id="field-anonymous-<?php echo $this->row->id; ?>" value="1" /> 
					<?php echo JText::_('PLG_RESOURCES_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_RESOURCES_REVIEWS_POST_COMMENT'); ?>" /> 
				</p>
			</fieldset>
		</form>
	</div>
	<?php
}
?>