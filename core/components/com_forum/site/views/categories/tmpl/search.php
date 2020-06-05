<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	<div class="section-inner hz-layout-with-aside">
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

									$name = Lang::txt('JANONYMOUS');
									if (!$row->get('anonymous'))
									{
										$name = $this->escape(stripslashes($row->creator->get('name')));
										if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels()))
										{
											$name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
										}
									}
									$cls = array();
									$icn = 'icon-comments';
									if ($row->get('closed'))
									{
										$cls[] = 'closed';
										$icn = 'icon-lock';
									}
									if ($row->get('sticky'))
									{
										$cls[] = 'sticky';
										$icn = 'icon-asterisk';
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
											<span class="entry-identifier <?php echo $icn; ?>"><?php echo $this->escape($row->get('id')); ?></span>
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
												<?php echo $this->escape(\Hubzero\Utility\Str::truncate($section, 100, array('exact' => true))); ?>
											</span>
										</td>
										<td class="priority-3">
											<span><?php echo Lang::txt('COM_FORUM_CATEGORY'); ?></span>
											<span class="entry-details category-name">
												<?php echo $this->escape(\Hubzero\Utility\Str::truncate($category, 100, array('exact' => true))); ?>
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
