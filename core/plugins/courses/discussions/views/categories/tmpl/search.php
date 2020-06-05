<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
											<?php echo $this->escape(\Hubzero\Utility\Str::truncate($this->sections[$this->categories[$row->get('category_id')]->get('section_id')]->get('title'), 100, array('exact' => true))); ?>
										</span>
									</td>
									<td>
										<span><?php echo Lang::txt('Category'); ?></span>
										<span class="entry-details category-name">
											<?php echo $this->escape(\Hubzero\Utility\Str::truncate($this->categories[$row->get('category_id')]->get('title'), 100, array('exact' => true))); ?>
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