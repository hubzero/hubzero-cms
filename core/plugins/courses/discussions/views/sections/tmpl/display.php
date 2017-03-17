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

$ct = count($this->sections);

$base = $this->offering->link() . '&active=discussions&unit=manage';
?>
<section class="main section">
	<div class="subject">
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
				->rows();
			?>
			<div class="container">
				<span class="ordering-controls">
					<?php if ($i != 0) { ?>
						<a class="order-up reorder" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=orderup'); ?>" title="<?php echo Lang::txt('Move up'); ?>"><?php echo Lang::txt('Move up'); ?></a>
					<?php } else { ?>
						<span class="order-up reorder"><?php echo Lang::txt('Move up'); ?></span>
					<?php } ?>

					<?php if ($i < $ct) { ?>
						<a class="order-down reorder" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=orderdown'); ?>" title="<?php echo Lang::txt('Move down'); ?>"><?php echo Lang::txt('Move down'); ?></a>
					<?php } else { ?>
						<span class="order-down reorder"><?php echo Lang::txt('Move down'); ?></span>
					<?php } ?>
				</span>

				<?php if ($this->config->get('access-edit-section') && $this->edit == $section->get('alias') && $section->get('id')) { ?>
				<form action="<?php echo Route::url($base); ?>" method="post">
				<?php } ?>
				<table class="entries categories">
					<caption>
						<?php if ($this->config->get('access-edit-section') && $this->edit == $section->get('alias') && $section->get('id')) { ?>
								<!-- <a name="s<?php echo $section->get('id'); ?>"></a> [!] This seems to cause some serious display issues -->
								<input type="text" name="fields[title]" value="<?php echo $this->escape(stripslashes($section->get('title'))); ?>" />
								<input type="submit" value="<?php echo Lang::txt('Save'); ?>" />
								<input type="hidden" name="fields[id]" value="<?php echo $section->get('id'); ?>" />
								<input type="hidden" name="fields[scope]" value="course" />
								<input type="hidden" name="fields[scope_id]" value="<?php echo $section->get('scope_id'); ?>" />
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
								<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
								<input type="hidden" name="action" value="savesection" />
								<input type="hidden" name="unit" value="manage" />
								<input type="hidden" name="active" value="discussions" />
						<?php } else { ?>
							<?php echo $this->escape(stripslashes($section->get('title'))); ?>
						<?php } ?>
						<?php if (($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) && $section->get('id')) { ?>
							<?php if ($this->config->get('access-delete-section')) { ?>
								<a class="delete" href="<?php echo Route::url($base . '&b=' . $section->get('alias') . '&c=delete'); ?>" title="<?php echo Lang::txt('Delete'); ?>">
									<span><?php echo Lang::txt('Delete'); ?></span>
								</a>
							<?php } ?>
							<?php if ($this->config->get('access-edit-section') && $this->edit != $section->get('alias') && $section->get('id')) { ?>
								<a class="edit" href="<?php echo Route::url($base . '&b=' . $section->get('alias') . '&c=edit#s' . $section->get('id')); ?>" title="<?php echo Lang::txt('Edit'); ?>">
									<span><?php echo Lang::txt('Edit'); ?></span>
								</a>
							<?php } ?>
						<?php } ?>
					</caption>
					<?php if ($this->config->get('access-create-category')) { ?>
						<tfoot>
							<tr>
								<td<?php if ($section->categories()->total() > 0) { echo ' colspan="5"'; } ?>>
									<a class="icon-add add btn" href="<?php echo Route::url($base . '&b=' . $section->get('alias') . '&c=new'); ?>">
										<span><?php echo Lang::txt('Add Category'); ?></span>
									</a>
								</td>
							</tr>
						</tfoot>
					<?php } ?>
					<tbody>
						<?php if ($categories->count() > 0) { ?>
							<?php foreach ($categories as $row) { ?>
								<tr<?php if ($row->get('closed')) { echo ' class="closed"'; } ?>>
									<th scope="row">
										<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
									</th>
									<td>
										<span class="entry-title" data-href="<?php echo Route::url($base . '&b=' . $row->get('alias')); ?>">
											<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
										</span>
										<span class="entry-details">
											<span class="entry-description">
												<?php echo $this->escape(stripslashes($row->get('description'))); ?>
											</span>
										</span>
									</td>
									<td>
										<span><?php echo $row->threads()
													->whereEquals('state', $this->filters['state'])
													->whereIn('access', $this->filters['access'])
													->total(); ?></span>
										<span class="entry-details">
											<?php echo Lang::txt('Discussions'); ?>
										</span>
									</td>
									<td>
										<span><?php echo $row->posts()
													->whereEquals('state', $this->filters['state'])
													->whereIn('access', $this->filters['access'])
													->total(); ?></span>
										<span class="entry-details">
											<?php echo Lang::txt('Posts'); ?>
										</span>
									</td>
									<?php if ($this->config->get('access-edit-category') || $this->config->get('access-delete-category')) { ?>
										<td class="entry-options">
											<?php if (($row->get('created_by') == User::get('id') || $this->config->get('access-edit-category')) && $section->get('id')) { ?>
												<a class="edit" href="<?php echo Route::url($base . '&b=' . $section->get('alias') . '&c=' . $row->get('alias') . '/edit'); ?>" title="<?php echo Lang::txt('Edit'); ?>">
													<span><?php echo Lang::txt('Edit'); ?></span>
												</a>
											<?php } ?>
											<?php if ($this->config->get('access-delete-category') && $section->get('id')) { ?>
												<a class="delete tooltips" title="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_DELETE_CATEGORY'); ?>" href="<?php echo Route::url($base . '&b=' . $section->get('alias') . '&c=' . $row->get('alias') . '/delete'); ?>" title="<?php echo Lang::txt('Delete'); ?>">
													<span><?php echo Lang::txt('Delete'); ?></span>
												</a>
											<?php } ?>
										</td>
									<?php } ?>
								</tr>
							<?php } ?>
						<?php } else { ?>
								<tr>
									<td><?php echo Lang::txt('There are no categories.'); ?></td>
								</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php if ($this->config->get('access-edit-section') && $this->edit == $section->get('alias') && $section->get('id')) { ?>
				</form>
				<?php } ?>
			</div><!-- /.container -->
		<?php } // foreach ?>

		<?php if ($this->config->get('access-create-section')) { ?>
			<div class="container">
				<form method="post" action="<?php echo Route::url($base); ?>">
					<fieldset>
						<table class="entries categories">
							<caption>
								<label for="field-title">
									<?php echo Lang::txt('New Section'); ?>
									<input type="text" name="fields[title]" id="field-title" value="" />
								</label>
								<input type="submit" value="<?php echo Lang::txt('Create'); ?>" />
							</caption>
							<tbody>
								<tr>
									<td><?php echo Lang::txt('Use sections to group related categories.'); ?></td>
								</tr>
							</tbody>
						</table>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
						<input type="hidden" name="fields[id]" value="" />
						<input type="hidden" name="fields[scope]" value="course" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->course->offering()->get('id'); ?>" />
						<input type="hidden" name="fields[state]" value="1" />
						<input type="hidden" name="active" value="discussions" />
						<input type="hidden" name="unit" value="manage" />
						<input type="hidden" name="action" value="savesection" />
					</fieldset>
				</form>
			</div><!-- /.container -->
		<?php } ?>
	</div><!-- /.subject -->
	<aside class="aside">
		<div class="container">
			<h3><?php echo Lang::txt('Statistics'); ?></h3>
			<table>
				<tbody>
					<tr>
						<th><?php echo Lang::txt('Categories'); ?></th>
						<td><span class="item-count"><?php echo $this->forum->count('categories', $this->filters); ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('Discussions'); ?></th>
						<td><span class="item-count"><?php echo $this->forum->count('threads', $this->filters); ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('Posts'); ?></th>
						<td><span class="item-count"><?php echo $this->forum->count('posts', $this->filters); ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="container">
			<h3><?php echo Lang::txt('Last Post'); ?></h3>
			<p>
				<?php
				$post = $this->forum->lastActivity();

				if ($post->get('id'))
				{
					$lname = Lang::txt('PLG_COURSES_DISCUSSIONS_ANONYMOUS');
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
						<span class="entry-date-at"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_AT'); ?></span>
						<span class="icon-time time"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('time'); ?></time></span>
						<span class="entry-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
						<span class="icon-date date"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('date'); ?></time></span>
					</span>
				<?php } else { ?>
					<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NONE'); ?>
				<?php } ?>
			</p>
		</div>
	</aside><!-- / .aside -->
</section><!-- /.main -->