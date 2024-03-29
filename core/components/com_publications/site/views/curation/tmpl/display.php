<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Sorting and paging
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$whatsleft  = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];

// URL
$route 	= 'index.php?option=' . $this->option . '&controller=curation';

$pa = new \Components\Publications\Tables\Author($this->database);

$this->css()
	->js()
	->css('jquery.fancybox.css', 'system')
	->css('curation.css')
	->js('curation.js');

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section curation">
	<p><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LIST_INSTRUCT'); ?></p>

	<div class="container">
		<nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
			<ul class="entries-menu filter-options">
				<li>
					<a<?php echo ($this->filters['curator'] != 'owner') ? ' class="active"' : ''; ?> href="<?php echo Route::url($route); ?>">
						<?php echo Lang::txt('All'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['curator'] == 'owner') ? ' class="active"' : ''; ?> href="<?php echo Route::url($route . '&assigned=1'); ?>">
						<?php echo Lang::txt('Assigned to me'); ?>
					</a>
				</li>
			</ul>
		</nav>

		<div class="container-block">
			<?php if (count($this->rows) > 0) { ?>
				<div class="publist">
					<table class="listing">
						<thead>
							<tr>
								<th class="thtype<?php if ($this->filters['sortby'] == 'id') { echo ' activesort'; } ?>">
									<a href="<?php echo Route::url($route . '&t_sortby=id&t_sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_ID'); ?>">
										<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ID'); ?>
									</a>
								</th>
								<th></th>
								<th<?php if ($this->filters['sortby'] == 'title') { echo ' class="activesort"'; } ?>>
									<a href="<?php echo Route::url($route . '&t_sortby=title&t_sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_TITLE'); ?>">
										<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_TITLE'); ?>
									</a>
								</th>
								<th></th>
								<th class="thtype<?php if ($this->filters['sortby'] == 'type') { echo ' activesort'; } ?>">
									<a href="<?php echo Route::url($route . '&t_sortby=type&t_sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_CONTENT_TYPE'); ?>">
										<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_CONTENT_TYPE'); ?>
									</a>
								</th>
								<th<?php if ($this->filters['sortby'] == 'submitted') { echo ' class="activesort"'; } ?>>
									<a href="<?php echo Route::url($route . '&t_sortby=submitted&t_sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED') . ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED'); ?>">
										<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED'); ?>
									</a>
								</th>
								<th<?php if ($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?>>
									<a href="<?php echo Route::url($route . '&t_sortby=status&t_sortdir=' . $sortbyDir); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_STATUS'); ?>">
										<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_STATUS'); ?>
									</a>
								</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($this->rows as $row)
								{
									$submitted  = $row->reviewed && $row->state == 5
												? strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_RESUBMITTED'))
												: strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED'));
									$submitted .= ' <span class="prominent">' . Date::of($row->submitted)->toLocal('M d, Y') . '</span> ';

									// Get submitter
									$submitter  = $pa->getSubmitter($row->version_id, $row->created_by);
									$submitter->name = $submitter->name ?: Lang::txt('JUNKNOWN');
									$submitted .= ' <span class="block">' . Lang::txt('COM_PUBLICATIONS_CURATION_BY', $submitter->name) . '</span>';

									if ($row->state == 7)
									{
										$reviewed = '';

										if (!empty($row->reviewed_by))
										{
											$reviewed = strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_REVIEWED')) . ' <span class="prominent">' . Date::of($row->reviewed)->toLocal('M d, Y') . '</span> ';

											$reviewer = User::getInstance($row->reviewed_by);
											$name = $reviewer->get('name');
											$name = $name ?: Lang::txt('JUNKNOWN');
											$reviewed .= $reviewer ? ' <span class="block">' . Lang::txt('COM_PUBLICATIONS_CURATION_BY', $name) . '</span>' : '';
										}
									}

									$class = $row->state == 5 ? 'status-pending' : 'status-wip';

									$abstract  = $row->abstract ? stripslashes($row->abstract) : '';

									// Is user authorize to edit assignment?
									$assign = ($this->authorized == 'curator' || $this->authorized == 'admin' || ($this->authorized == 'limited' && in_array($row->master_type, $this->filters['master_type']))) ? true : false;
									?>
									<tr class="mline mini faded" id="tr_<?php echo $row->id; ?>">
										<td>
											<?php echo $row->id; ?>
										</td>
										<td class="pub-image">
											<img width="30" height="30" src="<?php echo Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb'; ?>" alt="" />
										</td>
										<td>
											<?php if ($row->state == 5) { ?>
												<a href="<?php echo Route::url($route . '&id=' . $row->id); ?>" <?php if ($abstract) { echo 'title="' . $this->escape($abstract) . '"'; } ?>>
											<?php } ?>
											<?php echo $this->escape($row->title); ?>
											<?php if ($row->state == 5) { ?>
												</a>
											<?php } ?>
										</td>
										<td>
											v.<?php echo $row->version_label; ?>
										</td>
										<td>
											<span class="icon <?php echo $row->base; ?>">&nbsp;</span><?php echo $row->base; ?>
										</td>
										<td>
											<span class="block"><?php echo $submitted; ?></span>
											<?php if ($row->reviewed && $row->state == 5) { ?>
												<span class="item-updated"></span>
											<?php } ?>
										</td>
										<td>
											<span class="status-icon <?php echo $class; ?>"></span> <span class="status-label"><?php echo $row->state == 5 ? Lang::txt('COM_PUBLICATIONS_CURATION_STATUS_PENDING') : Lang::txt('COM_PUBLICATIONS_CURATION_PENDING_AUTHOR_CHANGES'); ?></span>
										</td>
										<td>
											<?php
											$owner = $row->curator ? User::getInstance($row->curator) : null;
											if ($owner)
											{
												?>
												<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGNED_TO'); ?>
												<?php if ($assign) { ?>
													<a href="<?php echo Route::url($route . '&id=' . $row->id . '&task=assign&vid=' . $row->version_id . '&ajax=1&no_html=1'); ?>" class="fancybox" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_CHANGE_ASSIGNMENT'); ?>">
												<?php } ?>
												<?php echo $this->escape($owner->get('name')); ?>
												<?php if ($assign) { ?>
													</a>
												<?php } ?>
												<?php
											}
											elseif ($assign)
											{
												?>
												<a href="<?php echo Route::url($route . '&id=' . $row->id . '&task=assign&vid=' . $row->version_id . '&ajax=1&no_html=1'); ?>" class="btn icon-assign btn-secondary fancybox" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN'); ?></a>
												<?php
											}
											?>
										</td>
										<td class="nowrap">
											<?php if ($row->state == 5) : ?>
												<a href="<?php echo Route::url($route . '&id=' . $row->id . '&vid=' . $row->version_id); ?>" class="btn icon-next btn-secondary btn-primary" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_OVER_REVIEW'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REVIEW'); ?></a>
											<?php endif; ?>
											<?php if ($row->state == 7) { echo $reviewed; } ?>
											<a href="<?php echo Route::url($route . '&id=' . $row->id . '&task=history&ajax=1&no_html=1'); ?>" class="btn btn-secondary icon-history fancybox" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_OVER_HISTORY'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY'); ?></a>
											<a href="<?php echo Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number); ?>" class="public-page" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_VIEW_PUB_PAGE'); ?>">&nbsp;</a>
										</td>
									</tr>
									<?php
								}
							?>
						</tbody>
					</table>
				</div>
				<?php
				$pn = $this->pageNav->render();
				$pn = str_replace('/?/&amp;', '/?', $pn);
				$f = 'task=display';
				foreach ($this->filters as $k => $v)
				{
					$f .= ($v && ($k == 'tag' || $k == 'category')) ? '&amp;' . $k . '=' . $v : '';
				}
				$pn = str_replace('?', '?' . $f . '&amp;', $pn);
				echo $pn;
				?>
			<?php } else { ?>
				<p class="noresults"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_NO_RESULTS'); ?></p>
			<?php } ?>
		</div>
		<div class="clearfix"></div>
	</div>
</section>
