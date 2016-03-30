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

$base = $this->offering->alias() . '&active=forum';
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

			<div class="container">
				<table class="entries">
					<caption>
						<?php echo Lang::txt('Search for "%s"', $this->escape($this->filters['search'])); ?>
					</caption>
					<tbody>
						<?php
						$rows = $this->forum->posts($this->filters)
							->paginated()
							->rows();

						if ($this->filters['search'] && $rows->count() > 0)
						{
							foreach ($rows as $row)
							{
								$title = $this->escape(stripslashes($row->get('title')));
								$title = preg_replace('#' . $this->filters['search'] . '#i', "<span class=\"highlight\">\\0</span>", $title);

								$name = Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS');
								if (!$row->get('anonymous'))
								{
									$name = ($row->creator()->get('public') ? '<a href="' . Route::url($row->creator()->getLink()) . '">' : '') . $this->escape(stripslashes($row->creator()->get('name'))) . ($row->creator()->get('public') ? '</a>' : '');
								}
								$cls = array();
								if ($row->get('closed'))
								{
									$cls[] = 'closed';
								}
								if ($row->get('sticky'))
								{
									$cls[] = 'sticky';
								}
								?>
								<tr<?php if (count($cls) > 0) { echo ' class="' . implode(' ', $cls) . '"'; } ?>>
									<th>
										<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
									</th>
									<td>
										<a class="entry-title" href="<?php echo Route::url($base . '&unit=' . $this->categories[$row->get('category_id')]->get('alias') . '&b=' . $row->get('thread') . '#c' . $row->get('id')); ?>">
											<span><?php echo $title; ?></span>
										</a>
										<span class="entry-details">
											<span class="entry-date">
												<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
											</span>
											<?php echo Lang::txt('by'); ?>
											<span class="entry-author">
												<?php echo $name; ?>
											</span>
										</span>
									</td>
									<td class="priority-4">
										<span><?php echo Lang::txt('Section'); ?></span>
										<span class="entry-details section-name">
											<?php echo $this->escape(\Hubzero\Utility\String::truncate($this->sections[$this->categories[$row->get('category_id')]->get('section_id')]->get('title'), 100, array('exact' => true))); ?>
										</span>
									</td>
									<td>
										<span><?php echo Lang::txt('Category'); ?></span>
										<span class="entry-details category-name">
											<?php echo $this->escape(\Hubzero\Utility\String::truncate($this->categories[$row->get('category_id')]->get('title'), 100, array('exact' => true))); ?>
										</span>
									</td>
								</tr>
								<?php
							}
						} else { ?>
							<tr>
								<td><?php echo Lang::txt('No discussions found.'); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php
				$pageNav = $rows->pagination;
				$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
				$pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
				$pageNav->setAdditionalUrlParam('active', 'forum');
				$pageNav->setAdditionalUrlParam('action', 'search');
				$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
				echo $pageNav;
				?>
			</div><!-- / .container -->
		</form>

	</div>
</section><!-- /.main -->