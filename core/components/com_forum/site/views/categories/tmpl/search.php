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
			<a class="icon-folder categories btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
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
					<table class="entries">
						<caption>
							<?php echo Lang::txt('COM_FORUM_SEARCH_FOR', $this->escape($this->filters['search'])); ?>
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

									$name = Lang::txt('COM_FORUM_ANONYMOUS');
									if (!$row->get('anonymous'))
									{
										$name = $this->escape(stripslashes($row->creator->get('name')));
										if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels()))
										{
											$name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
										}
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

									$category = $row->get('category_id');
									$catalias = $row->get('category_id');
									$section  = Lang::txt('COM_FORUM_UNKNOWN');
									$secalias = '';
									if (isset($this->categories[$row->get('category_id')]))
									{
										$category = $this->categories[$row->get('category_id')];
										$secalias = $category->get('section_id');
										if (isset($this->sections[$category->get('section_id')]))
										{
											$section  = $this->sections[$category->get('section_id')]->get('title');
											$secalias = $this->sections[$category->get('section_id')]->get('alias');
										}
										$catalias = $category->get('alias');
										$category = $category->get('title');
									}
									?>
									<tr<?php if (count($cls) > 0) { echo ' class="' . implode(' ', $cls) . '"'; } ?>>
										<th class="priority-5" scope="row">
											<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
										</th>
										<td>
											<a class="entry-title" href="<?php echo Route::url('index.php?option=' . $this->option . '&section=' . $secalias . '&category=' . $catalias . '&thread=' . $row->get('thread') . '&q=' . $this->filters['search']); ?>">
												<span><?php echo $title; ?></span>
											</a>
											<span class="entry-details">
												<span class="entry-date">
													<?php echo $row->created('date'); ?>
												</span>
												<?php echo Lang::txt('COM_FORUM_BY_USER', '<span class="entry-author">' . $name . '</span>'); ?>
											</span>
										</td>
										<td class="priority-4">
											<span><?php echo Lang::txt('COM_FORUM_SECTION'); ?></span>
											<span class="entry-details section-name">
												<?php echo $this->escape(\Hubzero\Utility\String::truncate($section, 100, array('exact' => true))); ?>
											</span>
										</td>
										<td class="priority-3">
											<span><?php echo Lang::txt('COM_FORUM_CATEGORY'); ?></span>
											<span class="entry-details category-name">
												<?php echo $this->escape(\Hubzero\Utility\String::truncate($category, 100, array('exact' => true))); ?>
											</span>
										</td>
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
						$pageNav = $rows->pagination;
						$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
						echo $pageNav;
					?>
					<div class="clearfix"></div>
				</div><!-- / .container -->
			</form>
		</div><!-- /.subject -->
		<aside class="aside">
			<?php if ($this->config->get('access-create-thread')) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('COM_FORUM_CREATE_YOUR_OWN'); ?></h3>
					<p>
						<?php echo Lang::txt('COM_FORUM_CREATE_YOUR_OWN_DISCUSSION'); ?>
					</p>
					<p>
						<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_FORUM_NEW_DISCUSSION'); ?></a>
					</p>
				</div><!-- / .container -->
			<?php } ?>
		</aside><!-- / .aside -->
	</div>
</section><!-- /.main -->
