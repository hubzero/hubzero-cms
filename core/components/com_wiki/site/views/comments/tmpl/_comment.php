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

defined('_HZEXEC_') or die();

$cls = isset($this->cls) ? $this->cls : 'odd';

if ($this->page->get('created_by') == $this->comment->get('created_by'))
{
	$cls .= ' author';
}
$cls .= ($this->comment->isReported()) ? ' abusive' : '';

$name = Lang::txt('COM_WIKI_ANONYMOUS');
if (!$this->comment->get('anonymous'))
{
	$name = $this->escape(stripslashes($this->comment->creator()->get('name', $name)));
	if ($this->comment->creator()->get('public'))
	{
		$name = '<a href="' . Route::url($this->comment->creator()->getLink()) . '">' . $name . '</a>';
	}
}

if ($this->comment->isReported())
{
	$comment = '<p class="warning">' . Lang::txt('COM_WIKI_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
}
else
{
	$comment = $this->comment->content('parsed');
}

$this->comment->set('category', 'answercomment');
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator()->getPicture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<?php
			if ($this->comment->get('rating'))
			{
				switch ($this->comment->get('rating'))
				{
					case 0:   $rcls = ' no-stars';        break;
					case 0.5: $rcls = ' half-stars';      break;
					case 1:   $rcls = ' one-stars';       break;
					case 1.5: $rcls = ' onehalf-stars';   break;
					case 2:   $rcls = ' two-stars';       break;
					case 2.5: $rcls = ' twohalf-stars';   break;
					case 3:   $rcls = ' three-stars';     break;
					case 3.5: $rcls = ' threehalf-stars'; break;
					case 4:   $rcls = ' four-stars';      break;
					case 4.5: $rcls = ' fourhalf-stars';  break;
					case 5:   $rcls = ' five-stars';      break;
					default:  $rcls = ' no-stars';        break;
				}
				?>
				<p><span class="avgrating<?php echo $rcls; ?>"><span><?php echo Lang::txt('COM_WIKI_COMMENT_RATING', $this->comment->get('rating')); ?></span></span></p>
				<?php
			}
			?>

			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->page->link('comments') . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('COM_WIKI_PERMALINK'); ?>">
					<span class="comment-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('COM_WIKI_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>

			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
				<?php if ($this->page->access('delete', 'comment')) { ?>
					<a class="icon-delete delete" href="<?php echo Route::url($this->comment->link('delete')); ?>"><!--
						--><?php echo Lang::txt('COM_WIKI_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->page->access('edit', 'comment')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->comment->link('edit')); ?>"><!--
						--><?php echo Lang::txt('COM_WIKI_EDIT'); ?><!--
					--></a>
				<?php } ?>

				<?php if (!$this->comment->isReported()) { ?>
					<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
						<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
						<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('COM_WIKI_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WIKI_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('COM_WIKI_CANCEL'); ?><!--
					--></a>
						<?php } else { ?>
						<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('COM_WIKI_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WIKI_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('COM_WIKI_REPLY'); ?><!--
					--></a>
						<?php } ?>
					<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('COM_WIKI_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
						--><?php echo Lang::txt('COM_WIKI_REPORT_ABUSE'); ?><!--
					--></a>
				<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if (User::isGuest()) { ?>
				<p class="warning">
					<?php echo Lang::txt('COM_WIKI_WARNING_LOGIN_REQUIRED', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->page->link('comments'), false, true))) . '">' . Lang::txt('COM_WIKI_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->page->link('comments')); ?>" method="post" enctype="multipart/form-data">
					<a name="commentform<?php echo $this->comment->get('id'); ?>"></a>
					<fieldset>
						<legend><span><?php echo Lang::txt('COM_WIKI_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('COM_WIKI_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
						<input type="hidden" name="comment[page_id]" value="<?php echo $this->page->get('id'); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
						<input type="hidden" name="comment[version]" value="<?php echo $this->page->version->get('version'); ?>" />
						<input type="hidden" name="comment[status]" value="1" />

						<input type="hidden" name="pagename" value="<?php echo $this->page->pagename; ?>" />

						<?php foreach ($this->page->adapter()->routing('savecomment') as $name => $val) { ?>
							<input type="hidden" name="<?php echo $this->escape($name); ?>" value="<?php echo $this->escape($val); ?>" />
						<?php } ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('COM_WIKI_ENTER_COMMENTS'); ?></span>
							<?php
							echo \Components\Wiki\Helpers\Editor::getInstance()->display('comment[ctext]', 'comment_' . $this->comment->get('id') . '_content', '', 'minimal no-footer', '35', '4');
							?>
						</label>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo Lang::txt('COM_WIKI_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<?php echo Html::input('token'); ?>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_WIKI_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$model = $this->comment->replies()
				->whereIn('state', array(
					\Components\Wiki\Models\Comment::STATE_PUBLISHED,
					\Components\Wiki\Models\Commen::STATE_FLAGGED
				));
			if ($this->version)
			{
				$model->whereEquals('version', $this->version);
			}
			$comments = $model
				->ordered()
				->rows();

			$this->view('_list', 'comments')
				//->setBasePath(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'site')
				->set('parent', $this->comment->get('id'))
				->set('page', $this->page)
				->set('option', $this->option)
				->set('comments', $comments)
				->set('config', $this->config)
				->set('depth', $this->depth)
				->set('version', $this->version)
				->set('cls', $cls)
				->display();
		}
		?>
	</li>