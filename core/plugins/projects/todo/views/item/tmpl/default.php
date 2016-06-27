<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('jquery.datepicker.css', 'system')
	 ->css('jquery.timepicker.css', 'system')
	 ->css()
	 ->js('jquery.timepicker', 'system')
     ->js();

$color = $this->row->get('color');
$class = $color ? 'pin_' . $color : 'pin_grey';

$overdue = $this->row->isOverdue();
$oNote = $overdue ? ' ('.Lang::txt('PLG_PROJECTS_TODO_OVERDUE').')' : '';

// Can it be deleted?
$deletable = ($this->model->access('content') && ($this->model->access('manager') or $this->row->get('created_by') == $this->uid)) ? 1 : 0;

// Due?
$due = $this->row->due() ? $this->row->due('date') : Lang::txt('PLG_PROJECTS_TODO_NEVER');

$url = 'index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=todo';

$listName = $this->todo->getListName($this->model->get('id'), $color);

// How long did it take to complete
if ($this->row->isComplete())
{
	$diff = strtotime($this->row->get('closed')) - strtotime($this->row->get('created'));
	$diff = \Components\Projects\Helpers\Html::timeDifference ($diff);
}
$assignee = $this->row->owner('name') ? $this->row->owner('name') : Lang::txt('PLG_PROJECTS_TODO_NOONE');
?>
<div id="plg-header">
	<h3 class="todo"><a href="<?php echo Route::url($url); ?>"><?php echo $this->title; ?></a>
	<?php if ($listName) { ?> &raquo; <a href="<?php echo Route::url($url) . '/?list=' . $color; ?>"><span class="indlist <?php echo 'pin_' . $color; ?>"><?php echo $listName; ?></span></a> <?php } ?>
	<?php if ($this->row->isComplete()) { ?> &raquo; <span class="indlist completedtd"><a href="<?php echo Route::url($url) . '/?state=1'; ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_COMPLETED')); ?></a></span> <?php } ?>
	&raquo; <span class="itemname"><?php echo \Hubzero\Utility\String::truncate($this->row->get('content'), 60); ?></span>
	</h3>
</div>
	<?php if ($this->model->access('content')) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add add btn showinbox"  href="<?php echo Route::url($url . '&action=new'); ?>">
				<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD_TODO'); ?>
			</a>
		</li>
	</ul>
	<?php } ?>

<div class="pinboard">
		<section class="section intropage">
			<div class="grid">
				<div class="col span8">
					<div id="td-item" class="<?php echo $class; ?>">
						<span class="pin">&nbsp;</span>
						<div class="todo-content">
							<?php echo $this->row->get('details') ? stripslashes($this->row->get('details')) :  stripslashes($this->row->get('content')); ?>
						</div>
					</div>
				</div>
				<div class="col span4 omega td-details">
					<p><?php echo Lang::txt('PLG_PROJECTS_TODO_CREATED') . ' ' . $this->row->created('date') .' '.Lang::txt('PLG_PROJECTS_TODO_BY') . ' ' . $this->row->creator('name'); ?></p>
				<?php if (!$this->row->isComplete()) { ?>
					<p><?php echo Lang::txt('PLG_PROJECTS_TODO_ASSIGNED_TO') . ' <strong>' . $assignee . '</strong>'; ?></p>
					<p><?php echo Lang::txt('PLG_PROJECTS_TODO_DUE') . ': <strong>' . $due . '</strong>'; ?></p>
				<?php } else if ($this->row->isComplete()) { ?>
						<p><?php echo Lang::txt('PLG_PROJECTS_TODO_TODO_CHECKED_OFF') . ' ' . $this->row->closed('date') . ' ' . Lang::txt('PLG_PROJECTS_TODO_BY') . ' ' . \Components\Projects\Helpers\Html::shortenName($this->row->closer('name')); ?></p>
						<p><?php echo Lang::txt('PLG_PROJECTS_TODO_TODO_TOOK') . ' ' . $diff . ' ' . Lang::txt('PLG_PROJECTS_TODO_TODO_TO_COMPLETE'); ?></p>
				<?php } ?>
				</div>
			</div>
		</section>
	<p class="td-options">
		<?php if (!$this->row->isComplete() && $this->model->access('content')) { ?>
		<span class="edit"><a href="<?php echo Route::url($url . '&action=edit') . '/?todoid=' . $this->row->get('id'); ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_TODO_EDIT'); ?></a></span>
		<span class="checked"><a href="<?php echo Route::url($url . '&action=changestate') . '/?todoid=' . $this->row->get('id') . '&amp;state=1&amp;' . Session::getFormToken() . '=1'; ?>" class="confirm-checkoff"><?php echo Lang::txt('PLG_PROJECTS_TODO_TODO_CHECK_OFF'); ?></a></span>
		<?php } ?>
		<?php if ($deletable) { ?>
		<span class="trash"><a href="<?php echo Route::url($url . '&action=delete') . '/?todoid=' . $this->row->get('id'); ?>" class="confirm-it" id="deltd"><?php echo Lang::txt('PLG_PROJECTS_TODO_DELETE'); ?></a></span>
		<?php } ?>
	</p>
	<div class="comment-wrap">
		<h4 class="comment-blurb"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_COMMENTS')) . ' (' . $this->row->comments('count') . ')'; ?>:</h4>
		<?php if ($this->row->comments() && $this->row->comments() instanceof \Hubzero\Base\ItemList) { ?>
			<ul id="td-comments">
			<?php foreach ($this->row->comments() as $comment) { ?>
				<li>
					<p><?php echo $comment->content('parsed'); ?></p>
					<p class="todo-assigned"><?php echo $comment->creator('name'); ?> <span class="date"> &middot; <?php echo \Components\Projects\Helpers\Html::timeAgo($comment->get('created')).' '.Lang::txt('PLG_PROJECTS_TODO_AGO'); ?> </span> <?php if ($comment->get('created_by') == $this->uid) { ?><a href="<?php echo Route::url($url . '&action=deletecomment').'/?todoid=' . $this->row->get('id') . '&amp;cid=' . $comment->get('id'); ?>" id="delc-<?php echo $comment->get('id'); ?>" class="confirm-it">[<?php echo Lang::txt('PLG_PROJECTS_TODO_DELETE'); ?>]</a><?php  } ?></p>
				</li>
			<?php } ?>
			</ul>
		<?php } else { ?>
			<p class="noresults"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_TODO_NO_COMMENTS')); ?></p>
		<?php } ?>
		<?php if ($this->model->access('content')) { ?>
		<form action="<?php echo Route::url($url); ?>" method="post" >
			<div class="addcomment td-comment">
				<label><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_NEW_COMMENT')); ?>:
					<textarea name="comment" rows="4" cols="50" class="commentarea" id="td-comment" placeholder="<?php echo Lang::txt('PLG_PROJECTS_TODO_WRITE_COMMENT'); ?>"></textarea>
				</label>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
					<input type="hidden" name="action" value="savecomment" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="active" value="todo" />
					<input type="hidden" name="itemid" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="parent_activity" value="<?php echo $this->row->get('activityid'); ?>" />
					<?php echo Html::input('token'); ?>
					<p class="blog-submit"><input type="submit" class="btn" id="c-submit" value="<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD_COMMENT'); ?>" /></p>
			</div>
		</form>
		<?php } ?>
	</div>
</div>
