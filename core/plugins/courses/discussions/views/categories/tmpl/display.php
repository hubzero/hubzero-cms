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

$base = $this->offering->link() . '&active=forum';
?>

<section class="main section">
	<div class="section-inner">
		<form action="<?php echo Route::url($base); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('Search for posts'); ?></legend>

					<label for="entry-search-field"><?php echo Lang::txt('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
					<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="search" />
				</fieldset>
			</div><!-- / .container -->

			<?php if ($this->category->closed) { ?>
				<p class="warning">
					<?php echo Lang::txt('This category is closed and no new discussions may be created.'); ?>
				</p>
			<?php } ?>

			<div class="container">
				<table class="entries">
					<caption>
						<?php
						if ($this->filters['search']) {
							if ($this->category->get('id')) {
								echo Lang::txt('Search for "%s" in "%s"', $this->escape($this->filters['search']), $this->escape(stripslashes($this->category->get('title'))));
							} else {
								echo Lang::txt('Search for "%s"', $this->escape($this->filters['search']));
							}
						} else {
							if ($this->category->get('id')) {
								echo Lang::txt('Discussions in "%s"', $this->escape(stripslashes($this->category->get('title'))));
							} else {
								echo Lang::txt('Discussions');
							}
						}
						?>
					</caption>
					<?php if (!$this->category->get('closed')) { ?>
						<tfoot>
							<tr>
								<td colspan="<?php echo ($this->config->get('access-delete-thread') || $this->config->get('access-edit-thread')) ? '5' : '4'; ?>">
									<a class="add btn" href="<?php echo Route::url($base . '&unit=' . $this->filters['category'] . '&b=new'); ?>">
										<?php echo Lang::txt('Add Discussion'); ?>
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
								$name = Lang::txt('PLG_COURSES_DISCUSSIONS_ANONYMOUS');
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
									<th>
										<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
									</th>
									<td>
										<a class="entry-title" href="<?php echo Route::url($base . '&unit=' . $this->filters['category'] . '&b=' . $row->get('id')); ?>">
											<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
										</a>
										<span class="entry-details">
											<span class="entry-date">
												<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
											</span>
											<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_BY_USER', '<span class="entry-author">' . $name . '</span>'); ?>
										</span>
									</td>
									<td>
										<span><?php
										echo $row->thread()
											->whereEquals('state', $row->get('state'))
											->whereIn('access', $this->filters['access'])
											->total();
										?></span>
										<span class="entry-details">
											<?php echo Lang::txt('Comments'); ?>
										</span>
									</td>
									<td>
										<span><?php echo Lang::txt('Last Post:'); ?></span>
										<span class="entry-details">
											<?php
											$lastpost = $row->lastActivity();
											if ($lastpost->get('id'))
											{
												$lname = Lang::txt('PLG_COURSES_DISCUSSIONS_ANONYMOUS');
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
												<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_BY_USER', '<span class="entry-author">' . $lname . '</span>'); ?>
											<?php } else { ?>
												<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NONE'); ?>
											<?php } ?>
										</span>
									</td>
									<?php if ($this->config->get('access-delete-thread') || $this->config->get('access-edit-thread')) { ?>
										<td class="entry-options">
											<?php if ($row->get('created_by') == User::get('id') || $this->config->get('access-edit-thread')) { ?>
												<a class="edit" href="<?php echo Route::url($base . '&scope=' . $this->filters['category'] . '&b=' . $row->get('id') . '&c=edit'); ?>">
													<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT'); ?>
												</a>
											<?php } ?>
											<?php if ($this->config->get('access-delete-thread')) { ?>
												<a class="delete" href="<?php echo Route::url($base . '&scope=' . $this->filters['category'] . '&b=' . $row->get('id') . '&c=delete'); ?>">
													<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_DELETE'); ?>
												</a>
											<?php } ?>
										</td>
									<?php } ?>
								</tr>
						<?php
							}
						} else { ?>
								<tr>
									<td colspan="<?php echo ($this->config->get('access-delete-thread') || $this->config->get('access-edit-thread')) ? '5' : '4'; ?>">
										<?php echo Lang::txt('There are currently no discussions.'); ?>
									</td>
								</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php
				if ($this->threads->count() > $this->filters['limit'])
				{
					$pageNav = $this->threads->pagination;
					$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
					$pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
					$pageNav->setAdditionalUrlParam('active', 'forum');
					$pageNav->setAdditionalUrlParam('unit', $this->filters['category']);
					echo $pageNav;
				}
				?>
			</div><!-- / .container -->
		</form>
	</div>
</section><!-- /.main -->
