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
<?php if ($this->sections->count()) { ?>
	<div class="subject">
		<form action="<?php echo Route::url($base . '&scope=search'); ?>" method="get">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LABEL'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER'); ?>" />
				</fieldset>
			</div><!-- / .container -->
		</form>

		<?php
		$ct = $this->sections->count();
		$ct--;
		$i = 0;
		foreach ($this->sections as $section)
		{
			$categories = $section
				->categories()
				->whereEquals('state', $this->filters['state'])
				->whereIn('access', $this->filters['access'])
				->order('title', 'asc')
				->rows();
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
								<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->forum->get('scope')); ?>" />
								<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->forum->get('scope_id')); ?>" />
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
						<?php if ($categories->count() > 0) { ?>
							<?php foreach ($categories as $row) { ?>
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
										<span><?php echo $row->threads()
												->whereEquals('state', $this->filters['state'])
												->whereIn('access', $this->filters['access'])
												->total(); ?></span>
										<span class="entry-details">
											<?php echo Lang::txt('PLG_GROUPS_FORUM_DISCUSSIONS'); ?>
										</span>
									</td>
									<td class="priority-4">
										<span><?php echo $row->posts()
												->whereEquals('state', $this->filters['state'])
												->whereIn('access', $this->filters['access'])
												->total(); ?></span>
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
			$i++;
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
						<input type="hidden" name="fields[id]" value="" />
						<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->forum->get('scope')); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->forum->get('scope_id')); ?>" />
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
						<td><span class="item-count"><?php echo $this->forum->count('categories', $this->filters); ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_FORUM_DISCUSSIONS'); ?></th>
						<td><span class="item-count"><?php echo $this->forum->count('threads', $this->filters); ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_GROUPS_FORUM_POSTS'); ?></th>
						<td><span class="item-count"><?php echo $this->forum->count('posts', $this->filters); ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php
				$this->view('_email_settings', '/shared')
					->set('config', $this->config)
					->set('base', $base)
					->set('recvEmailOptionID', $this->recvEmailOptionID)
					->set('recvEmailOptionValue', $this->recvEmailOptionValue)
					->set('categories', $this->categories)
					->display();
		?>

		<div class="container">
			<h3><?php echo Lang::txt('PLG_GROUPS_FORUM_LAST_POST'); ?></h3>
			<p>
				<?php
				$post = $this->forum->lastActivity();

				if ($post->get('id'))
				{
					$lname = Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS');
					if (!$post->get('anonymous'))
					{
						$lname = $this->escape(stripslashes($post->creator->get('name', $lname)));
						if (in_array($post->creator->get('access'), User::getAuthorisedViewLevels()))
						{
							$lname = '<a href="' . Route::url($post->creator->link()) . '">' . $lname . '</a>';
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
						<?php echo \Hubzero\Utility\String::truncate(strip_tags($post->get('comment')), 170); ?>
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
						<input type="hidden" name="fields[id]" value="" />
						<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->forum->get('scope')); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->forum->get('scope_id')); ?>" />
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
