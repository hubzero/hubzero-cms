<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

$longComment  = \Components\Projects\Helpers\Html::replaceUrls($longComment, 'external');
$shortComment = \Components\Projects\Helpers\Html::replaceUrls($shortComment, 'external');

// Emotions (new)
$longComment  = \Components\Projects\Helpers\Html::replaceEmoIcons($longComment);
$shortComment = \Components\Projects\Helpers\Html::replaceEmoIcons($shortComment);

$creator = User::getInstance($comment->created_by);
?>
	<li class="quote <?php echo $newComment ? ' newitem' : ''; ?>" id="c_<?php echo $comment->id; ?>">
		<?php if ($this->edit && $deletable && $this->model->access('content')) { ?>
			<span class="m_options">
				<span class="delit" id="pu_<?php echo $comment->id; ?>">
					<a href="<?php echo Route::url($this->model->link('feed') .'&action=deletecomment&cid=' . $comment->id); ?>">x</a>
				</span>
			</span>
		<?php } ?>
		<img class="comment-author" src="<?php echo $creator->picture($comment->admin); ?>" alt="" />
		<div class="comment-show">
			<span class="comment-details">
				<span class="actor"><?php echo $comment->admin == 1 ? Lang::txt('COM_PROJECTS_ADMIN') : $comment->author; ?></span>
				<span class="item-time">&middot; <?php echo \Components\Projects\Helpers\Html::showTime($comment->created, true); ?></span>
			</span>
			<?php
			echo '<div class="body">' . $shortComment;
			if ($shorten)
			{
				echo ' <a href="#" class="more-content">' . Lang::txt('COM_PROJECTS_MORE') . '</a>';
			}
			echo '</div>';
			if ($shorten)
			{
				echo '<div class="fullbody hidden">' . $longComment . '</div>' ;
			}
			?>
		</div>
	</li>
