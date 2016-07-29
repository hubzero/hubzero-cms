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

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-info btn popup" href="<?php echo Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
				<span><?php echo Lang::txt('COM_FORUM_GETTING_STARTED'); ?></span>
			</a>
		</p>
	</div>
</header>

<section class="main section">
<?php if ($this->sections->count()) { ?>
	<div class="section-inner">
		<div class="subject">
			<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=categories&task=search'); ?>" method="get">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_FORUM_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<legend><span><?php echo Lang::txt('COM_FORUM_SEARCH_LEGEND'); ?></span></legend>

						<label for="entry-search-field"><?php echo Lang::txt('COM_FORUM_SEARCH_LABEL'); ?></label>
						<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_FORUM_SEARCH_PLACEHOLDER'); ?>" />
					</fieldset>
				</div>
			</form>

			<?php
			foreach ($this->sections as $section)
			{
				$categories = $section
					->categories()
					->whereEquals('state', $this->filters['state'])
					->whereIn('access', $this->filters['access'])
					->rows();
				?>
				<div class="container" id="section-<?php echo $section->get('id'); ?>">
					<table class="entries categories">
						<caption>
							<?php if ($this->config->get('access-edit-section') && $this->edit == $section->get('alias') && $section->get('id')) { ?>
								<a name="s<?php echo $section->get('id'); ?>"></a>
								<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
									<input type="text" name="fields[title]" value="<?php echo $this->escape(stripslashes($section->get('title'))); ?>" />
									<input type="submit" value="<?php echo Lang::txt('COM_FORUM_SUBMIT'); ?>" />
									<input type="hidden" name="fields[id]" value="<?php echo $section->get('id'); ?>" />
									<input type="hidden" name="fields[scope]" value="site" />
									<input type="hidden" name="fields[scope_id]" value="0" />
									<input type="hidden" name="fields[access]" value="<?php echo $this->escape($section->get('access')); ?>" />
									<input type="hidden" name="fields[state]" value="<?php echo $this->escape($section->get('state')); ?>" />
									<input type="hidden" name="controller" value="sections" />
									<input type="hidden" name="task" value="save" />
									<?php echo Html::input('token'); ?>
								</form>
							<?php } else { ?>
								<?php echo $this->escape(stripslashes($section->get('title'))); ?>
							<?php } ?>

							<?php if (($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) && $section->get('id')) { ?>
								<?php if ($this->config->get('access-delete-section')) { ?>
									<a class="icon-delete delete" data-txt-confirm="<?php echo Lang::txt('COM_FORUM_CONFIRM_DELETE'); ?>" href="<?php echo Route::url('index.php?option='.$this->option . '&section=' . $section->get('alias') . '&task=delete'); ?>" title="<?php echo Lang::txt('COM_FORUM_DELETE'); ?>">
										<span><?php echo Lang::txt('COM_FORUM_DELETE'); ?></span>
									</a>
								<?php } ?>
								<?php if ($this->config->get('access-edit-section') && $this->edit != $section->get('alias') && $section->get('id')) { ?>
									<a class="icon-edit edit" href="<?php echo Route::url('index.php?option=' . $this->option . '&section=' . $section->get('alias') . '&task=edit#s' . $section->get('id')); ?>" title="<?php echo Lang::txt('COM_FORUM_EDIT'); ?>">
										<span><?php echo Lang::txt('COM_FORUM_EDIT'); ?></span>
									</a>
								<?php } ?>
							<?php } ?>
						</caption>
						<?php if ($this->config->get('access-create-category')) { ?>
							<tfoot>
								<tr>
									<td<?php if ($section->categories()->total() > 0) { echo ' colspan="5"'; } ?>>
										<a class="icon-add add btn" id="addto-<?php echo $section->get('id'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&section=' . $section->get('alias') . '&task=new'); ?>">
											<span><?php echo Lang::txt('COM_FORUM_NEW_CATEGORY'); ?></span>
										</a>
									</td>
								</tr>
							</tfoot>
						<?php } ?>
						<tbody>
							<?php if ($categories->count() > 0) { ?>
								<?php foreach ($categories as $row) { ?>
									<?php
									$row->set('section_alias', $section->get('alias'));
									?>
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
										<td class="priority-3">
											<span><?php
											$threads = $row->threads()
												->whereEquals('state', $this->filters['state'])
												->whereIn('access', $this->filters['access'])
												->total();
											echo $threads; ?></span>
											<span class="entry-details">
												<?php echo Lang::txt('COM_FORUM_DISCUSSIONS'); ?>
											</span>
										</td>
										<td  class="priority-3">
											<span><?php echo ($threads ? $row->posts()
												->whereEquals('state', $this->filters['state'])
												->whereIn('access', $this->filters['access'])
												->total() : 0); ?></span>
											<span class="entry-details">
												<?php echo Lang::txt('COM_FORUM_POSTS'); ?>
											</span>
										</td>
									<?php if ($this->config->get('access-edit-category') || $this->config->get('access-delete-categort')) { ?>
										<td class="entry-options">
											<?php if (($row->get('created_by') == User::get('id') || $this->config->get('access-edit-category')) && $section->get('id')) { ?>
												<a class="icon-edit edit" href="<?php echo Route::url($row->link('edit')); ?>" title="<?php echo Lang::txt('COM_FORUM_EDIT'); ?>">
													<span><?php echo Lang::txt('COM_FORUM_EDIT'); ?></span>
												</a>
											<?php } ?>
											<?php if ($this->config->get('access-delete-category') && $section->get('id')) { ?>
												<a class="icon-delete delete tooltips" data-txt-confirm="<?php echo Lang::txt('COM_FORUM_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($row->link('delete')); ?>" title="<?php echo Lang::txt('COM_FORUM_DELETE_CATEGORY'); ?>">
													<span><?php echo Lang::txt('COM_FORUM_DELETE'); ?></span>
												</a>
											<?php } ?>
										</td>
									<?php } ?>
									</tr>
								<?php } ?>
							<?php } else { ?>
									<tr>
										<td><?php echo Lang::txt('COM_FORUM_SECTION_EMPTY'); ?></td>
									</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<?php
			}
			?>
		</div><!-- /.subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_FORUM_STATS'); ?></h3>
				<table>
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_FORUM_CATEGORIES'); ?></th>
							<td><span class="item-count"><?php echo $this->forum->count('categories', $this->filters); ?></span></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_FORUM_DISCUSSIONS'); ?></th>
							<td><span class="item-count"><?php echo $this->forum->count('threads', $this->filters); ?></span></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_FORUM_POSTS'); ?></th>
							<td><span class="item-count"><?php echo $this->forum->count('posts', $this->filters); ?></span></td>
						</tr>
					</tbody>
				</table>
			</div><!-- / .container -->
			<div class="container">
				<h3><?php echo Lang::txt('COM_FORUM_LAST_POST'); ?></h3>
				<p>
					<?php
					$post = $this->forum->lastActivity();

					if ($post->get('id'))
					{
						$lname = Lang::txt('COM_FORUM_ANONYMOUS');
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
								foreach ($section->categories()->rows() as $row)
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
							<span class="entry-date-at"><?php echo Lang::txt('COM_FORUM_AT'); ?></span>
							<span class="icon-time time"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo Lang::txt('COM_FORUM_ON'); ?></span>
							<span class="icon-date date"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('date'); ?></time></span>
						</span>
					<?php } else { ?>
						<?php echo Lang::txt('COM_FORUM_NONE'); ?>
					<?php } ?>
				</p>
			</div><!-- / .container -->

			<?php if ($this->config->get('access-create-section')) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('COM_FORUM_SECTION'); ?></h3>
					<p>
						<?php echo Lang::txt('COM_FORUM_SECTION_EXPLANATION'); ?>
					</p>
					<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
						<fieldset>
							<legend><?php echo Lang::txt('COM_FORUM_NEW_SECTION'); ?></legend>
							<label for="field-title">
								<?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
								<input type="text" name="fields[title]" id="field-title" value="" />
							</label>
							<p class="submit">
								<input type="submit" value="<?php echo Lang::txt('COM_FORUM_CREATE'); ?>" />
							</p>
							<input type="hidden" name="task" value="save" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="sections" />
							<input type="hidden" name="fields[id]" value="" />
							<input type="hidden" name="fields[scope]" value="site" />
							<input type="hidden" name="fields[scope_id]" value="0" />
							<input type="hidden" name="fields[access]" value="1" />
							<input type="hidden" name="fields[state]" value="1" />
							<?php echo Html::input('token'); ?>
						</fieldset>
					</form>
				</div>
			<?php } ?>
		</aside><!-- / .aside -->
	</div>
<?php } else { ?>
	<div class="instructions">
		<?php if ($this->config->get('access-create-section')) { ?>
			<p class="notification"><?php echo Lang::txt('COM_FORUM_EMPTY_MODERATOR', Route::url('index.php?option=' . $this->option . '&action=populate')); ?></p>

			<div class="container">
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
					<fieldset class="entry-section">
						<legend><?php echo Lang::txt('COM_FORUM_NEW_SECTION'); ?></legend>

						<span class="input-wrap">
							<label for="field-title"><span><?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?></span></label>
							<span class="input-cell">
								<input type="text" name="fields[title]" id="field-title" value="" placeholder="<?php echo Lang::txt('COM_FORUM_ENTER_TITLE'); ?>" />
							</span>
							<span class="input-cell">
								<input type="submit" class="btn" value="<?php echo Lang::txt('COM_FORUM_CREATE'); ?>" />
							</span>
						</span>

						<input type="hidden" name="task" value="save" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="sections" />
						<input type="hidden" name="fields[id]" value="" />
						<input type="hidden" name="fields[scope]" value="site" />
						<input type="hidden" name="fields[scope_id]" value="0" />
						<input type="hidden" name="fields[access]" value="1" />
						<input type="hidden" name="fields[state]" value="1" />

						<?php echo Html::input('token'); ?>
					</fieldset>
				</form>
			</div><!-- / .container -->
		<?php } else { ?>
			<p class="notification"><?php echo Lang::txt('COM_FORUM_EMPTY_NOT_MODERATOR'); ?></p>
		<?php } ?>
	</div>
<?php } ?>
</section><!-- /.main -->
