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
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-folder categories btn" href="<?php echo Route::url($this->category->link('base')); ?>">
				<?php echo Lang::txt('COM_FORUM_ALL_CATEGORIES'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
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
				</div><!-- / .container -->

				<div class="container">
					<nav class="entries-filters">
						<ul class="entries-menu order-options">
							<li>
								<a class="<?php echo ($this->filters['sortby'] == 'created' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'created')); ?>" href="<?php echo Route::url($this->category->link('here', '&sortby=created&sortdir=' . sortDir($this->filters, 'created'))); ?>" title="<?php echo Lang::txt('COM_FORUM_SORT_BY_CREATED'); ?>">
									<?php echo Lang::txt('COM_FORUM_SORT_CREATED'); ?>
								</a>
							</li>
							<li>
								<a class="<?php echo ($this->filters['sortby'] == 'activity' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'activity')); ?>" href="<?php echo Route::url($this->category->link('here', '&sortby=activity&sortdir=' . sortDir($this->filters, 'activity'))); ?>" title="<?php echo Lang::txt('COM_FORUM_SORT_BY_ACTIVITY'); ?>">
									<?php echo Lang::txt('COM_FORUM_SORT_ACTIVITY'); ?>
								</a>
							</li>
							<li>
								<a class="<?php echo ($this->filters['sortby'] == 'replies' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'replies')); ?>" href="<?php echo Route::url($this->category->link('here', '&sortby=replies&sortdir=' . sortDir($this->filters, 'replies'))); ?>" title="<?php echo Lang::txt('COM_FORUM_SORT_BY_NUM_POSTS'); ?>">
									<?php echo Lang::txt('COM_FORUM_SORT_NUM_POSTS'); ?>
								</a>
							</li>
							<li>
								<a class="<?php echo ($this->filters['sortby'] == 'title' ? 'active ' . strtolower($this->filters['sort_Dir']) : sortDir($this->filters, 'title', 'ASC')); ?>" href="<?php echo Route::url($this->category->link('here', '&sortby=title&sortdir=' . sortDir($this->filters, 'title', 'ASC'))); ?>" title="<?php echo Lang::txt('COM_FORUM_SORT_BY_TITLE'); ?>">
									<?php echo Lang::txt('COM_FORUM_SORT_TITLE'); ?>
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
									echo Lang::txt('COM_FORUM_SEARCH_FOR_IN', $this->escape($this->filters['search']), $this->escape(stripslashes($this->category->get('title'))));
								}
								else
								{
									echo Lang::txt('COM_FORUM_SEARCH_FOR', $this->escape($this->filters['search']));
								}
							} else {
								echo Lang::txt('COM_FORUM_SEARCH_IN', $this->escape(stripslashes($this->category->get('title'))));
							}
							?>
						</caption>
						<tbody>
							<?php
							if ($this->threads->count() > 0)
							{
								foreach ($this->threads as $row)
								{
									$name = Lang::txt('COM_FORUM_ANONYMOUS');
									if (!$row->get('anonymous'))
									{
										$name = $this->escape(stripslashes($row->creator()->get('name', $name)));
										if ($row->creator('public'))
										{
											$name = '<a href="' . Route::url($row->creator()->getLink()) . '">' . $name . '</a>';
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
										<th class="priority-5" scope="row">
											<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
										</th>
										<td>
											<a class="entry-title" href="<?php echo Route::url($row->link()); ?>">
												<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
											</a>
											<span class="entry-details">
												<span class="entry-date">
													<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
												</span>
												<?php echo Lang::txt('COM_FORUM_BY_USER', '<span class="entry-author">' . $name . '</span>'); ?>
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
												<?php echo Lang::txt('COM_FORUM_COMMENTS'); ?>
											</span>
										</td>
										<td class="priority-3">
											<span><?php echo Lang::txt('COM_FORUM_LAST_POST'); ?></span>
											<span class="entry-details">
												<?php
												$lastpost = $row->lastActivity();
												if ($lastpost->get('id'))
												{
													$lname = Lang::txt('COM_FORUM_ANONYMOUS');
													if (!$lastpost->get('anonymous'))
													{
														$lname = ($lastpost->creator()->get('public') ? '<a href="' . Route::url($lastpost->creator()->getLink()) . '">' : '') . $this->escape(stripslashes($lastpost->creator()->get('name'))) . ($lastpost->creator()->get('public') ? '</a>' : '');
													}
													?>
													<span class="entry-date">
														<time datetime="<?php echo $lastpost->created(); ?>"><?php echo $lastpost->created('date'); ?></time>
													</span>
													<?php echo Lang::txt('COM_FORUM_BY_USER', '<span class="entry-author">' . $lname . '</span>'); ?>
												<?php } else { ?>
													<?php echo Lang::txt('COM_FORUM_NONE'); ?>
												<?php } ?>
											</span>
										</td>
										<?php if ($this->config->get('access-manage-thread') || $this->config->get('access-edit-thread') || $this->config->get('access-delete-thread')) { ?>
											<td class="entry-options">
												<?php if ($this->config->get('access-manage-thread') || ($this->config->get('access-edit-thread') && $row->get('created_by') == User::get('id'))) { ?>
													<a class="icon-edit edit" href="<?php echo Route::url($row->link('edit')); ?>">
														<?php echo Lang::txt('COM_FORUM_EDIT'); ?>
													</a>
												<?php } ?>
												<?php if ($this->config->get('access-manage-thread') || ($this->config->get('access-delete-thread') && $row->get('created_by') == User::get('id'))) { ?>
													<a class="icon-delete delete" data-txt-confirm="<?php echo Lang::txt('COM_FORUM_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($row->link('delete')); ?>">
														<?php echo Lang::txt('COM_FORUM_DELETE'); ?>
													</a>
												<?php } ?>
											</td>
										<?php } ?>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td><?php echo Lang::txt('COM_FORUM_CATEGORY_EMPTY'); ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<?php
					$pageNav = $this->threads->pagination;
					$pageNav->setAdditionalUrlParam('section', $this->filters['section']);
					$pageNav->setAdditionalUrlParam('category', $this->filters['category']);
					$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
					echo $pageNav;
					?>
					<div class="clearfix"></div>
				</div><!-- / .container -->
			</form>
		</div><!-- /.subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_FORUM_LAST_POST'); ?></h3>
				<p>
					<?php
					$last = $this->category->lastActivity();
					if ($last->get('id'))
					{
						$lname = Lang::txt('COM_FORUM_ANONYMOUS');
						if (!$last->get('anonymous'))
						{
							$lname = $this->escape(stripslashes($last->creator()->get('name', $lname)));
							if ($last->creator()->get('public'))
							{
								$lname = '<a href="' . Route::url($last->creator()->getLink()) . '">' . $lname . '</a>';
							}
						}
						$last->set('category', $this->filters['category']);
						$last->set('section', $this->filters['section']);
						?>
						<a class="entry-comment" href="<?php echo Route::url($last->link()); ?>">
							<?php echo \Hubzero\Utility\String::truncate(strip_tags($last->get('comment')), 170); ?>
						</a>
						<span class="entry-author">
							<?php echo $lname; ?>
						</span>
						<span class="entry-date">
							<span class="entry-date-at"><?php echo Lang::txt('COM_FORUM_AT'); ?></span>
							<span class="icon-time time"><time datetime="<?php echo $last->created(); ?>"><?php echo $last->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo Lang::txt('COM_FORUM_ON'); ?></span>
							<span class="icon-date date"><time datetime="<?php echo $last->created(); ?>"><?php echo $last->created('date'); ?></time></span>
						</span>
					<?php } else { ?>
						<?php echo Lang::txt('COM_FORUM_NONE'); ?>
					<?php } ?>
				</p>
			</div><!-- / .container -->

			<?php if ($this->config->get('access-create-thread')) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('COM_FORUM_CREATE_YOUR_OWN'); ?></h3>
					<?php if (!$this->category->isClosed()) { ?>
						<p>
							<?php echo Lang::txt('COM_FORUM_CREATE_YOUR_OWN_DISCUSSION'); ?>
						</p>
						<p>
							<a class="icon-add add btn" href="<?php echo Route::url($this->category->link('newthread')); ?>"><?php echo Lang::txt('COM_FORUM_NEW_DISCUSSION'); ?></a>
						</p>
					<?php } else { ?>
						<p class="warning">
							<?php echo Lang::txt('COM_FORUM_CATEGORY_CLOSED'); ?>
						</p>
					<?php } ?>
				</div><!-- / .container -->
			<?php } ?>
		</aside><!-- / .aside -->
	</div>
</section><!-- /.main -->