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

$name = Lang::txt('PLG_MEMBERS_CAREERPLAN_ANONYMOUS');
if (!$this->comment->log->get('anonymous'))
{
	$name = $this->escape(stripslashes($this->comment->log->creator->get('name', $name)));
	if (in_array($this->comment->log->creator->get('access'), User::getAuthorisedViewLevels()))
	{
		$name = '<a href="' . Route::url($this->comment->log->creator->link()) . '">' . $name . '</a>';
	}
}
?>
	<li class="comment <?php echo $cls; ?>" id="comment<?php echo $this->comment->log->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->log->creator->picture($this->comment->log->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<span class="time"><time datetime="<?php echo Date::of($this->comment->get('created'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php
				$dt = Date::of($this->comment->get('created'));
				$ct = Date::of('now');

				$lapsed = $ct->toUnix() - $dt->toUnix();

				if ($lapsed < 30)
				{
					echo Lang::txt('PLG_MEMBERS_MENTORS_JUST_NOW');
				}
				elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y'))
				{
					echo $dt->toLocal('M j, Y');
				}
				elseif ($lapsed > 86400)
				{
					echo $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
				}
				else
				{
					echo $dt->relative();
				}
			?></time></span>
			</p>

		<?php if (Request::getWord('action') == 'editcomment'
				&& Request::getInt('comment') == $this->comment->get('id')
				&& User::get('id') == $this->comment->get('created_by')) { ?>
			<form id="cform<?php echo $this->comment->get('id'); ?>" class="comment-edit" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><span><?php echo Lang::txt('COM_BLOG_COMMENT_EDIT'); ?></span></legend>

					<input type="hidden" name="comment[id]" value="<?php echo $this->comment->log->get('id'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="active" value="careerplan" />
					<input type="hidden" name="action" value="post" />

					<?php echo Html::input('token'); ?>

					<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
						<span class="label-text"><?php echo Lang::txt('COM_BLOG_FIELD_COMMENTS'); ?></span>
						<?php
						echo $this->editor('comment[description]', $this->comment->log->get('description'), 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
						?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo Lang::txt('COM_BLOG_SUBMIT'); ?>" />
					</p>
				</fieldset>
			</form>
	<?php } else { ?>

			<div class="comment-body">
				<?php echo $this->comment->log->get('description'); ?>
			</div>

			<p class="comment-options">
				<?php if ($this->config->get('access-delete-comment')) { ?>
					<a class="icon-delete delete" data-confirm="<?php echo Lang::txt('COM_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->base . '&action=deletecomment&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('JACTION_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-comment') || User::get('id') == $this->comment->get('created_by')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->base . '&action=editcomment&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('JACTION_EDIT'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 1)) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
						<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_BLOG_REPLY'); ?>" href="<?php echo Route::url($this->base); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('JCANCEL'); ?><!--
					--></a>
					<?php } else { ?>
						<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_BLOG_REPLY'); ?>" href="<?php echo Route::url($this->base . '&reply=' . $this->comment->get('id')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('COM_BLOG_REPLY'); ?><!--
					--></a>
					<?php } ?>
				<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 1)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->log->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->log->get('id'); ?>">
				<form id="cform<?php echo $this->comment->log->get('id'); ?>" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('PLG_MEMBERS_CAREERPLAN_REPLYING_TO', (!$this->comment->log->get('anonymous') ? $name : Lang::txt('PLG_MEMBERS_CAREERPLAN_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[scope]" value="<?php echo $this->comment->log->get('scope'); ?>" />
						<input type="hidden" name="comment[scope_id]" value="<?php echo $this->comment->log->get('scope_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->log->get('id'); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
						<input type="hidden" name="task" value="view" />
						<input type="hidden" name="active" value="careerplan" />
						<input type="hidden" name="action" value="post" />

						<?php echo Html::input('token'); ?>

						<label for="comment_<?php echo $this->comment->log->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('COM_BLOG_FIELD_COMMENTS'); ?></span>
							<?php
							echo $this->editor('comment[description]', '', 35, 4, 'comment_' . $this->comment->log->get('id') . '_content', array('class' => 'minimal no-footer'));
							?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('JSUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
			</div><!-- / .addcomment -->
		<?php } ?>
	<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 1))
		{
			$r = Hubzero\Activity\Recipient::all()->getTableName();
			$l = Hubzero\Activity\Log::blank()->getTableName();

			$replies = Hubzero\Activity\Recipient::all()
				->select($r . '.*')
				->including('log')
				->join($l, $l . '.id', $r . '.log_id')
				->whereEquals($r . '.scope', $this->comment->log->get('scope'))
				->whereEquals($r . '.scope_id', $this->comment->log->get('scope_id'))
				->whereEquals($l . '.parent', $this->comment->get('id'))
				->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
				->ordered()
				->rows();

			$this->view('_list')
				->set('parent', $this->comment->get('id'))
				->set('option', $this->option)
				->set('comments', $replies)
				->set('config', $this->config)
				->set('depth', $this->depth)
				->set('cls', $cls)
				->set('base', $this->base)
				->set('field', $this->field)
				->set('member', $this->member)
				->display();
		}
		?>
	</li>