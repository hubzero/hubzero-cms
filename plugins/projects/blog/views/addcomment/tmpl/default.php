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
?>
<a name="commentform_<?php echo $this->activityid; ?>"></a>
<div class="addcomment" id="commentform_<?php echo $this->activityid; ?>">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.a.'id='.$this->pid).'/?active=feed'; ?>" method="post">
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->pid; ?>" />
			<input type="hidden" name="action" value="savecomment" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="active" value="feed" />
			<input type="hidden" name="itemid" value="<?php echo $this->itemid; ?>" />
			<input type="hidden" name="tbl" value="<?php echo $this->tbl; ?>" />
			<input type="hidden" name="parent_activity" value="<?php echo $this->activityid; ?>" />
			<label>
				<textarea name="comment" rows="4" cols="50" class="commentarea" id="ca_<?php echo $this->activityid; ?>" placeholder="Write your comment"></textarea>
			</label>
			<p class="blog-submit"><input type="submit" class="c-submit" id="cs_<?php echo $this->activityid; ?>" value="<?php echo JText::_('COM_PROJECTS_ADD_COMMENT'); ?>" /></p>
		</fieldset>
	</form>
</div>