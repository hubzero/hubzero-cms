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

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->filters['category'];

if (!function_exists('sortDir'))
{
	function sortDir($filters, $current, $dir='DESC')
	{
		if ($filters['sortby'] == $current && $filters['sort_Dir'] == $dir)
		{
			$dir = ($dir == 'ASC' ? 'DESC' : 'ASC');
		}
		return strtolower($dir);
	}
}

$this->category->set('section_alias', $this->filters['section']);

$this->css()
     ->js();
?>

<ul id="page_options">
	<li>
		<a class="icon-folder categories btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum'); ?>">
			<?php echo Lang::txt('PLG_GROUPS_FORUM_ALL_CATEGORIES'); ?>
		</a>
	</li>
</ul>

<section class="main section">
	<form action="<?php echo Route::url($base); ?>" method="get">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LEGEND'); ?></legend>
				<label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LABEL'); ?></label>
				<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER'); ?>" />
			</fieldset>
		</div><!-- / .container -->

		<?php if ($this->category->get('closed')) { ?>
			<p class="warning">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_CATEGORY_CLOSED'); ?>
			</p>
		<?php } ?>

		<div class="container">
			<nav class="entries-filters">
				<ul class="entries-menu order-options">
					<li>
						<a class="<?php echo ($this->filters['sortby'] == 'created' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'created')); ?>" href="<?php echo Route::url($base . '&sortby=created&sortdir=' . sortDir($this->filters, 'created')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_BY_CREATED'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_CREATED'); ?>
						</a>
					</li>
					<li>
						<a class="<?php echo ($this->filters['sortby'] == 'activity' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'activity')); ?>" href="<?php echo Route::url($base . '&sortby=activity&sortdir=' . sortDir($this->filters, 'activity')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_BY_ACTIVITY'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_ACTIVITY'); ?>
						</a>
					</li>
					<li>
						<a class="<?php echo ($this->filters['sortby'] == 'replies' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'replies')); ?>" href="<?php echo Route::url($base . '&sortby=replies&sortdir=' . sortDir($this->filters, 'replies')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_BY_NUM_POSTS'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_NUM_POSTS'); ?>
						</a>
					</li>
					<li>
						<a class="<?php echo ($this->filters['sortby'] == 'title' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'title', 'ASC')); ?>" href="<?php echo Route::url($base . '&sortby=title&sortdir=' . sortDir($this->filters, 'title', 'ASC')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_BY_TITLE'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_FORUM_SORT_TITLE'); ?>
						</a>
					</li>
				</ul>
			</nav>

			<table class="entries">
				<caption>
					<?php
					if ($this->filters['search'])
					{
						if ($this->category->get('title'))
						{
							echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_FOR_IN', $this->escape($this->filters['search']), $this->escape(stripslashes($this->category->get('title'))));
						}
						else
						{
							echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_FOR', $this->escape($this->filters['search']));
						}
					}
					else
					{
						echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_IN', $this->escape(stripslashes($this->category->get('title'))));
					}
					?>
				</caption>
				<?php if (!$this->category->get('closed') && $this->config->get('access-create-thread')) { ?>
					<tfoot>
						<tr>
							<td colspan="<?php echo ($this->config->get('access-delete-thread') || $this->config->get('access-edit-thread')) ? '5' : '4'; ?>">
								<a class="icon-add add btn" href="<?php echo Route::url($base . '/new'); ?>">
									<?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_DISCUSSION'); ?>
								</a>
							</td>
						</tr>
					</tfoot>
				<?php } ?>
				<tbody>
					<?php
					if ($this->threads->count() > 0)
					{
						foreach ($this->threads as $row)
						{
							$name = Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS');
							if (!$row->get('anonymous'))
							{
								$name = $this->escape(stripslashes($row->creator->get('name', $name)));
								if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels()))
								{
									$name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
								}
							}
							$cls = array();
							if ($row->isClosed())
							{
								$cls[] = 'closed';
							}
							if ($row->isSticky())
							{
								$cls[] = 'sticky';
							}

							$row->set('category', $this->filters['category']);
							$row->set('section', $this->filters['section']);
							?>
							<tr<?php if (count($cls) > 0) { echo ' class="' . implode(' ', $cls) . '"'; } ?>>
								<th class="priority-5">
									<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
								</th>
								<td>
									<a class="entry-title" href="<?php echo Route::url($base . '/' . $row->get('id')); ?>">
										<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
									</a>
									<span class="entry-details">
										<span class="entry-date">
											<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
										</span>
										<?php echo Lang::txt('PLG_GROUPS_FORUM_BY_USER', '<span class="entry-author">' . $name . '</span>'); ?>
									</span>
								</td>
								<td class="priority-4">
									<span><?php
									echo $row->thread()
										->whereEquals('state', $row->get('state'))
										->whereIn('access', $this->filters['access'])
										->total();
									?></span>
									<span class="entry-details">
										<?php echo Lang::txt('PLG_GROUPS_FORUM_COMMENTS'); ?>
									</span>
								</td>
								<td class="priority-3">
									<span><?php echo Lang::txt('PLG_GROUPS_FORUM_LAST_POST'); ?></span>
									<span class="entry-details">
										<?php
										$lastpost = $row->lastActivity();
										if ($lastpost->get('id'))
										{
											$lname = Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS');
											if (!$lastpost->get('anonymous'))
											{
												$lname = $this->escape(stripslashes($lastpost->creator->get('name')));
												if (in_array($lastpost->creator->get('access'), User::getAuthorisedViewLevels()))
												{
													$lname = '<a href="' . Route::url($lastpost->creator->link()) . '">' . $lname . '</a>';
												}
											}
											?>
											<span class="entry-date">
												<time datetime="<?php echo $lastpost->created(); ?>"><?php echo $lastpost->created('date'); ?></time>
											</span>
											<?php echo Lang::txt('PLG_GROUPS_FORUM_BY_USER', '<span class="entry-author">' . $lname . '</span>'); ?>
										<?php } else { ?>
											<?php echo Lang::txt('PLG_GROUPS_FORUM_NONE'); ?>
										<?php } ?>
									</span>
								</td>
								<?php if ($this->config->get('access-delete-thread') || $this->config->get('access-edit-thread') || User::get('id') == $row->get('created_by')) { ?>
									<td class="entry-options">
										<?php if ($row->get('created_by') == User::get('id') || $this->config->get('access-edit-thread')) { ?>
											<a class="icon-edit edit" href="<?php echo Route::url($base . '/' . $row->get('id') . '/edit'); ?>">
												<?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?>
											</a>
										<?php } ?>
										<?php if ($this->config->get('access-delete-thread')) { ?>
											<a class="icon-delete delete" href="<?php echo Route::url($base . '/' . $row->get('id') . '/delete'); ?>">
												<?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE'); ?>
											</a>
										<?php } ?>
									</td>
								<?php } ?>
							</tr>
						<?php } ?>
					<?php } else { ?>
							<tr>
								<td><?php echo Lang::txt('PLG_GROUPS_FORUM_CATEGORY_EMPTY'); ?></td>
							</tr>
					<?php } ?>
				</tbody>
			</table>

			<?php
			$pageNav = $this->threads->pagination;
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'forum');
			$pageNav->setAdditionalUrlParam('scope', $this->filters['section'] . '/' . $this->filters['category']);
			echo $pageNav;
			?>
		</div><!-- / .container -->
		<input type='hidden' name='sortdir' value='<?php echo $this->filters['sort_Dir']; ?>' />
		<input type='hidden' name='sortby' value='<?php echo $this->filters['sortby']; ?>' />
	</form>
</section><!-- /.main -->
