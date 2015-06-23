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

// No direct access
defined('_HZEXEC_') or die();

$comment = $this->comment;
$newComment = $this->model->member()->lastvisit && $this->model->member()->lastvisit <= $comment->created
	? true : false;

// Is user allowed to delete item?
$deletable = ($comment->created_by == $this->uid or $this->model->member()->role == 1) ? 1 : 0;

$longComment = stripslashes($comment->comment);
$longComment = str_replace('<!-- {FORMAT:HTML} -->', '', $longComment);

$shorten = (strlen($longComment) > 250) ? 1 : 0;
$shortComment = $shorten
	? \Hubzero\Utility\String::truncate($longComment, 250, array('html' => true)) : $longComment;

$longComment = \Components\Projects\Helpers\Html::replaceUrls($longComment, 'external');
$shortComment = \Components\Projects\Helpers\Html::replaceUrls($shortComment, 'external');

// Emotions (new)
$longComment  = \Components\Projects\Helpers\Html::replaceEmoIcons($longComment);
$shortComment = \Components\Projects\Helpers\Html::replaceEmoIcons($shortComment);

$creator = \Hubzero\User\Profile::getInstance($comment->created_by);

?>
	<li class="quote <?php echo $newComment ? ' newitem' : ''; ?>" id="c_<?php echo $comment->id; ?>">
	<?php if ($this->edit && $deletable && $this->model->access('content')) { ?>
		<span class="m_options">
			<span class="delit" id="pu_<?php echo $comment->id; ?>">
			 <a href="<?php echo Route::url($this->model->link('feed') .'&action=deletecomment&amp;cid=' . $comment->id); ?>">x</a>
			</span>
		</span>
		<?php } ?>
		<img class="comment-author" src="<?php echo $creator->getPicture($comment->admin); ?>" alt="" />
		<div class="comment-show">
			<span class="comment-details">
				<span class="actor"><?php echo $comment->admin == 1 ? Lang::txt('COM_PROJECTS_ADMIN') : $comment->author; ?></span>
				<span class="item-time">&middot; <?php echo \Components\Projects\Helpers\Html::showTime($comment->created, true); ?></span>
			</span>
	<?php 	echo '<div class="body">' . $shortComment;
			if ($shorten)
			{
				echo ' <a href="#" class="more-content">' . Lang::txt('COM_PROJECTS_MORE') . '</a>';
			}
			echo '</div>'; ?>
	<?php 	if ($shorten)
			{
			echo '<div class="fullbody hidden">' . $longComment . '</div>' ;
			}
	?>
		</div>
	</li>
