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

$comment = $this->comment;
$newComment = $this->project->lastvisit && $this->project->lastvisit <= $comment->created
	? true : false;

// Is user allowed to delete item?
$deletable = ($comment->created_by == $this->uid or $this->project->role == 1) ? 1 : 0;

$longComment = stripslashes($comment->comment);

$shorten = (strlen($longComment) > 250) ? 1 : 0;
$shortComment = $shorten
	? \Hubzero\Utility\String::truncate($longComment, 250) : $longComment;

$longComment = ProjectsHtml::replaceUrls($longComment, 'external');
$shortComment = ProjectsHtml::replaceUrls($shortComment, 'external');

// Emotions (new)
$longComment  = ProjectsHtml::replaceEmoIcons($longComment);
$shortComment = ProjectsHtml::replaceEmoIcons($shortComment);

$creator = \Hubzero\User\Profile::getInstance($comment->created_by);

?>
	<li class="quote <?php echo $newComment ? ' newitem' : ''; ?>" id="c_<?php echo $comment->id; ?>">
	<?php if ($deletable) { ?>
		<span class="m_options">
			<span class="delit" id="pu_<?php echo $comment->id; ?>">
			 <a href="<?php echo JRoute::_('index.php?option=' . $this->option
			. '&alias=' . $this->project->alias . '&task=view&active=feed')
			.'/?action=deletecomment' . a . 'cid=' . $comment->id; ?>">x</a>
			</span>
		</span>
		<?php } ?>
		<img class="comment-author" src="<?php echo $creator->getPicture($comment->admin); ?>" alt="" />
		<span class="comment-show">
			<span class="comment-details">
				<span class="actor"><?php echo $comment->admin == 1 ? JText::_('COM_PROJECTS_ADMIN') : $comment->author; ?></span>
				<span class="item-time">&middot; <?php echo ProjectsHtml::showTime($comment->created, true); ?></span>
			</span>
	<?php 	echo '<span class="body">' . $shortComment;
			if ($shorten)
			{
				echo ' <a href="#" class="more-content">' . JText::_('COM_PROJECTS_MORE') . '</a>';
			}
			echo '</span>'; ?>
	<?php 	if ($shorten)
			{
			echo '<span class="fullbody hidden">' . $longComment . '</span>' ;
			}
	?>
		</span>

	</li>
