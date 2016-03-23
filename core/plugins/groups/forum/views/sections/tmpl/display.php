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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

$this->css()
     ->js();

if ($this->config->get('access-manage-section')) {
?>
<ul id="page_options">
	<li>
		<a class="icon-config config btn" href="<?php echo Route::url($base . '/settings'); ?>">
			<?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS'); ?>
		</a>
	</li>
</ul>
<?php } ?>

<section class="main section">
<?php if ($this->sections->total()) { ?>
	<div class="subject">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<form action="<?php echo Route::url($base . '&scope=search'); ?>" method="get">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LABEL'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER'); ?>" />
					<!--
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="search" />
					-->
				</fieldset>
			</div><!-- / .container -->
		</form>

	<?php
	$filters = array('access' => 0);
	if (!User::isGuest())
	{
		$filters['access'] = array(0, 1, 3);
		if ($this->config->get('access-view-section'))
		{
			$filters['access'] = array(0, 1, 3, 4);
		}
	}

	$ct = count($this->sections);

	$ct--;
	foreach ($this->sections as $i => $section)
	{
		if (!$section->exists())
		{
			continue;
		}
		?>
		<div class="container">
			<?php if ($this->config->get('access-edit-section')) { ?>
				<span class="ordering-controls">
					<?php if ($i != 0) { ?>
						<a class="order-up reorder" href="<?php echo Route::url($base . '&section=' . $section->get('alias') . '&action=orderup'); ?>" title="<?php echo Lang::txt('Move up'); ?>"><?php echo Lang::txt('Move up'); ?></a>
					<?php } else { ?>
						<span class="order-up reorder"><?php echo Lang::txt('Move up'); ?></span>
					<?php } ?>

					<?php if ($i < $ct) { ?>
						<a class="order-down reorder" href="<?php echo Route::url($base . '&section=' . $section->get('alias') . '&action=orderdown'); ?>" title="<?php echo Lang::txt('Move down'); ?>"><?php echo Lang::txt('Move down'); ?></a>
					<?php } else { ?>
						<span class="order-down reorder"><?php echo Lang::txt('Move down'); ?></span>
					<?php } ?>
				</span>
			<?php } ?>

			<table class="entries categories">
				<caption>
					<?php if ($this->config->get('access-edit-section') && $this->edit == $section->get('alias')) { ?>
						<form action="<?php echo Route::url($base); ?>" method="post" id="s<?php echo $section->get('id'); ?>">
							<input type="text" name="fields[title]" value="<?php echo $this->escape(stripslashes($section->get('title'))); ?>" />
							<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />

							<input type="hidden" name="fields[id]" value="<?php echo $section->get('id'); ?>" />
							<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
							<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
							<input type="hidden" name="action" value="savesection" />
							<input type="hidden" name="active" value="forum" />
							<?php echo Html::input('token'); ?>
						</form>
					<?php } else { ?>
						<?php echo $this->escape(stripslashes($section->get('title'))); ?>
					<?php } ?>
					<?php if ($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) { ?>
						<?php if ($this->config->get('access-delete-section')) { ?>
							<a class="icon-delete delete" href="<?php echo Route::url($base . '&scope=' . $section->get('alias') . '/delete'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE'); ?>">
								<span><?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE'); ?></span>
							</a>
						<?php } ?>
						<?php if ($this->config->get('access-edit-section') && $this->edit != $section->get('alias')) { ?>
							<a class="icon-edit edit" href="<?php echo Route::url($base . '&scope=' . $section->get('alias') . '/edit#s' . $section->get('id')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?>">
								<span><?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?></span>
							</a>
						<?php } ?>
					<?php } ?>
				</caption>
				<?php if ($this->config->get('access-create-category')) { ?>
					<tfoot>
						<tr>
							<td<?php if ($section->categories()->total() > 0) { echo ' colspan="5"'; } ?>>
								<a class="icon-add add btn" href="<?php echo Route::url($base . '&scope=' . $section->get('alias') . '/new'); ?>">
									<span><?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_CATEGORY'); ?></span>
								</a>
							</td>
						</tr>
					</tfoot>
				<?php } ?>
				<tbody>
					<?php if ($section->categories('list', $filters)->total() > 0) { ?>
						<?php foreach ($section->categories() as $row) { ?>
							<tr<?php if ($row->get('closed')) { echo ' class="closed"'; } ?>>
								<th class="priority-5" scope="row">
									<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
								</th>
								<td>
									<a class="entry-title" href="<?php echo Route::url($row->link()); ?>">
										<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
									</a>
									<span class="entry-details">
										<span class="entry-description">
											<?php echo $this->escape(stripslashes($row->get('description'))); ?>
										</span>
									</span>
								</td>
								<td class="priority-4">
									<span><?php echo $row->count('threads'); ?></span>
									<span class="entry-details">
										<?php echo Lang::txt('PLG_GROUPS_FORUM_DISCUSSIONS'); ?>
									</span>
								</td>
								<td class="priority-4">
									<span><?php echo ($row->count('threads') ? $row->count('posts') : 0); ?></span>
									<span class="entry-details">
										<?php echo Lang::txt('PLG_GROUPS_FORUM_POSTS'); ?>
									</span>
								</td>
							<?php if ($this->config->get('access-edit-category') || $this->config->get('access-delete-category')) { ?>
								<td class="entry-options">
									<?php if ($row->get('created_by') == User::get('id') || $this->config->get('access-edit-category')) { ?>
										<a class="icon-edit edit" href="<?php echo Route::url($row->link('edit')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?>">
											<span><?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?></span>
										</a>
									<?php } ?>
									<?php if ($this->config->get('access-delete-category')) { ?>
										<a class="icon-delete delete tooltips" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE_CATEGORY'); ?>" href="<?php echo Route::url($row->link('delete')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE'); ?>">
											<span><?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE'); ?></span>
										</a>
									<?php } ?>
								</td>
							<?php } ?>
							</tr>
						<?php } ?>
					<?php } else { ?>
							<tr>
								<td><?php echo Lang::txt('PLG_GROUPS_FORUM_NO_CATEGORIES'); ?></td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php
		}
	?>

	<?php if ($this->config->get('access-create-section')) { ?>
		<div class="container">
			<form method="post" action="<?php echo Route::url($base); ?>">
					<table class="entries categories">
						<caption>
							<label for="field-title">
								<?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_SECTION') . ' '; ?>
								<input type="text" name="fields[title]" id="field-title" value="" />
							</label>
							<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />
						</caption>
						<tbody>
							<tr>
								<td><?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_SECTION_EXPLANATION'); ?></td>
							</tr>
						</tbody>
					</table>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
					<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
					<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="savesection" />

					<?php echo Html::input('token'); ?>
				</fieldset>
			</form>
		</div><!-- /.container -->
	<?php } ?>

	</div><!-- /.subject -->
	<aside class="aside">
		<div class="container">
			<h3><?php echo Lang::txt('PLG_GROUPS_FORUM_STATISTICS'); ?></h3>
			<table>
				<tbody>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_FORUM_CATEGORIES'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('categories'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_FORUM_DISCUSSIONS'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('threads'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_FORUM_POSTS'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('posts'); ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="container relative-container">
			<h3><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS'); ?></h3>
			<?php if (Component::params('com_groups')->get('email_comment_processing') && $this->config->get('access-view-section')) : ?>
				<?php if (Component::params('com_groups')->get('enable_forum_email_digest', 0)) : ?>
					<p>
						<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS'); ?>
						<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS_' . $this->recvEmailOptionValue); ?>
						<br />
						<a href="#" class="edit-forum-options">
							<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CHANGE_SETTINGS'); ?>
						</a>
					</p>
					<div class="edit-forum-options-panel">
						<p class="response-message"></p>
						<form method="post" action="<?php echo Route::url($base); ?>" id="forum-options-extended">
							<div>
								<input type="checkbox" class="edit-forum-options-receive-emails" value="1" name="recvpostemail"<?php if ($this->recvEmailOptionValue >= 1) { echo ' checked="checked"'; } ?> />
								<label>
									<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_TOGGLE'); ?>
								</label>
							</div>
							<div class="edit-forum-options-as">
								<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_INTERVAL'); ?>
							</div>
							<div>
								<input type="radio" name="recvpostemail" class="edit-forum-options-immediate" value="1"<?php if ($this->recvEmailOptionValue == 1) { echo ' checked="checked"'; } ?><?php if ($this->recvEmailOptionValue == 0) { echo ' disabled="disabled"'; } ?> />
								<label>
									<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_IMMEDIATELY'); ?>
								</label>
							</div>
							<div>
								<input type="radio" name="recvpostemail" class="edit-forum-options-digest" value="2"<?php if ($this->recvEmailOptionValue >= 2) { echo ' checked="checked"'; } ?><?php if ($this->recvEmailOptionValue == 0) { echo ' disabled="disabled"'; } ?> />
								<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_AS_A'); ?>
								<select name="recvpostemail" class="edit-forum-options-frequency"<?php if ($this->recvEmailOptionValue < 2) { echo ' disabled="disabled"'; } ?>>
									<option value="2"<?php if ($this->recvEmailOptionValue == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DAILY'); ?></option>
									<option value="3"<?php if ($this->recvEmailOptionValue == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_WEEKLY'); ?></option>
									<option value="4"<?php if ($this->recvEmailOptionValue == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_MONTHLY'); ?></option>
								</select>
								<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DIGEST'); ?>
							</div>

							<input type="hidden" name="action" value="savememberoptions" />
							<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
							<?php echo Html::input('token'); ?>

							<div class="edit-forum-options-actions">
								<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />
								<input type="button" class="btn edit-forum-options-cancel" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_CANCEL'); ?>" />
							</div>
						</form>
					</div>
				<?php else : ?>
					<form method="post" action="<?php echo Route::url($base); ?>" id="forum-options">
						<fieldset>
							<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS'); ?></legend>

							<input type="hidden" name="action" value="savememberoptions" />
							<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
							<input type="hidden" name="postsaveredirect" value="<?php echo Route::url($base); ?>" />
							<?php echo Html::input('token'); ?>

							<label class="option" for="recvpostemail">
								<input type="checkbox" class="option" id="recvpostemail" value="1" name="recvpostemail"<?php if ($this->recvEmailOptionValue == 1) { echo ' checked="checked"'; } ?> />
								<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS'); ?>
							</label>
							<input class="option" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />
						</fieldset>
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="container">
			<h3><?php echo Lang::txt('PLG_GROUPS_FORUM_LAST_POST'); ?></h3>
			<p>
			<?php
			if ($this->model->lastActivity()->exists())
			{
				$post = $this->model->lastActivity();

				$lname = Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS');
				if (!$post->get('anonymous'))
				{
					$lname = $this->escape(stripslashes($post->creator('name', $lname)));
					if ($post->creator('public'))
					{
						$lname = '<a href="' . Route::url($post->creator()->getLink()) . '">' . $lname . '</a>';
					}
				}
				foreach ($this->sections as $section)
				{
					if ($section->categories()->total() > 0)
					{
						foreach ($section->categories() as $row)
						{
							if ($row->get('id') == $post->get('category_id'))
							{
								$post->set('category', $row->get('alias'));
								$post->set('section', $section->get('alias'));
								break;
							}
						}
					}
				}
				?>
				<a class="entry-comment" href="<?php echo Route::url($post->link()); ?>">
					<?php echo $post->content('clean', 170); ?>
				</a>
				<span class="entry-author">
					<?php echo $lname; ?>
				</span>
				<span class="entry-date">
					<span class="entry-date-at"><?php echo Lang::txt('PLG_GROUPS_FORUM_AT'); ?></span>
					<span class="icon-time time"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('time'); ?></time></span>
					<span class="entry-date-on"><?php echo Lang::txt('PLG_GROUPS_FORUM_ON'); ?></span>
					<span class="icon-date date"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('date'); ?></time></span>
				</span>
			<?php } else { ?>
				<?php echo Lang::txt('PLG_GROUPS_FORUM_NONE'); ?>
			<?php } ?>
			</p>
		</div>
	</aside><!-- / .aside -->
<?php } else { ?>
	<div class="instructions">
		<?php if ($this->config->get('access-create-section')) { ?>
			<p class="notification"><?php echo Lang::txt('PLG_GROUPS_FORUM_EMPTY_MODERATOR', Route::url($base. '&action=populate')); ?></p>

			<div class="container">
				<form method="post" action="<?php echo Route::url($base); ?>">
					<fieldset class="entry-section">
						<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_SECTION'); ?></legend>

						<span class="input-wrap">
							<label for="field-title"><span><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_TITLE'); ?></span></label>
							<span class="input-cell">
								<input type="text" name="fields[title]" id="field-title" value="" placeholder="<?php echo Lang::txt('PLG_GROUPS_FORUM_ENTER_TITLE'); ?>" />
							</span>
							<span class="input-cell">
								<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_CREATE'); ?>" />
							</span>
						</span>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
						<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />
						<input type="hidden" name="active" value="forum" />
						<input type="hidden" name="action" value="savesection" />

						<input type="hidden" name="fields[id]" value="" />
						<input type="hidden" name="fields[access]" value="0" />

						<?php echo Html::input('token'); ?>
					</fieldset>
				</form>
			</div><!-- / .container -->
		<?php } else { ?>
			<p class="notification"><?php echo Lang::txt('PLG_GROUPS_FORUM_EMPTY_NOT_MODERATOR'); ?></p>
		<?php } ?>
	</div>
<?php } ?>
</section><!-- /.main -->
