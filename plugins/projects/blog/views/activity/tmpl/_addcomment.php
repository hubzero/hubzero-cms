<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
defined('_JEXEC') or die('Restricted access');

$a = $this->activity;

// Add Comment
if ($a->commentable) { ?>
<div class="addcomment <?php if (count($this->comments) == 0) { echo 'hidden'; } ?>" id="commentform_<?php echo $a->id; ?>">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=feed'); ?>" method="post">
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" name="action" value="savecomment" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="active" value="feed" />
			<input type="hidden" name="itemid" value="<?php echo $this->eid; ?>" />
			<input type="hidden" name="tbl" value="<?php echo $this->etbl; ?>" />
			<input type="hidden" name="parent_activity" value="<?php echo $a->id; ?>" />
			<img class="comment-author" src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($this->uid); ?>" alt="" />
			<label class="comment-show">
				<textarea name="comment" rows="4" cols="50" class="commentarea" placeholder="<?php echo JText::_('PLG_PROJECTS_BLOG_ENTER_COMMENT'); ?>" id="ca_<?php echo $a->id; ?>"></textarea>
			</label>
			<p class="blog-submit"><input type="submit" class="btn c-submit nojs" id="cs_<?php echo $a->id; ?>" value="<?php echo JText::_('COM_PROJECTS_COMMENT'); ?>" /></p>
		</fieldset>
	</form>
</div>
<?php } ?>