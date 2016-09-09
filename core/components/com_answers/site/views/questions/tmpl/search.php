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

// No direct access
defined('_HZEXEC_') or die();

if (Pathway::count() <= 0)
{
	Pathway::append(
		Lang::txt(strtoupper($this->option)),
		'index.php?option=' . $this->option
	);
}

Document::setTitle(Lang::txt('COM_ANSWERS'));

$this->css()
     ->js();

if (!$this->filters['filterby'])
{
	$this->filters['filterby'] = 'all';
}
if ($this->filters['filterby'] == 'none')
{
	$this->filters['filterby'] = 'all';
}
$sortdir = $this->filters['sort_Dir'] == 'DESC' ? 'ASC' : 'DESC';
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_ANSWERS'); ?></h2>

	<?php if (User::authorise('core.create', $this->option)) { ?>
	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
				<span><?php echo Lang::txt('COM_ANSWERS_NEW_QUESTION'); ?></span>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">

				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_ANSWERS_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<label for="entry-search-field"><?php echo Lang::txt('COM_ANSWERS_SEARCH_LABEL'); ?></label>
						<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_ANSWERS_SEARCH_PLACEHOLDER'); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="area" value="<?php echo $this->escape($this->filters['area']); ?>" />
						<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
						<input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
						<input type="hidden" name="filterby" value="<?php echo $this->escape($this->filters['filterby']); ?>" />
						<input type="hidden" name="task" value="<?php echo $this->escape($this->task); ?>" />
					</fieldset>
				</div><!-- / .container -->

				<div class="container">
					<?php if (!User::isGuest()) { ?>
						<nav class="entries-filters">
							<ul class="entries-menu user-options">
								<li>
									<a<?php echo ($this->filters['area'] == '') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
										<?php echo Lang::txt('COM_ANSWERS_FILTER_EVERYTHING'); ?>
									</a>
								</li>
								<li>
									<a<?php echo ($this->filters['area'] == 'mine') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=mine&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
										<?php echo Lang::txt('COM_ANSWERS_QUESTIONS_I_ASKED'); ?>
									</a>
								</li>
								<li>
									<a<?php echo ($this->filters['area'] == 'assigned') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=assigned&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
										<?php echo Lang::txt('COM_ANSWERS_QUESTIONS_RELATED_TO_CONTRIBUTIONS'); ?>
									</a>
								</li>
								<li>
									<a<?php echo ($this->filters['area'] == 'interest') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=interest&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
										<?php echo Lang::txt('COM_ANSWERS_QUESTIONS_TAGGED_WITH_MY_INTERESTS'); ?>
									</a>
								</li>
							</ul>
						</nav>
					<?php } ?>
					<nav class="entries-filters">
						<ul class="entries-menu order-options" data-label="<?php echo Lang::txt('COM_ANSWERS_SORT'); ?>">
						<?php if ($this->config->get('banking')) { ?>
							<li>
								<a<?php echo ($this->filters['sortby'] == 'rewards') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=' . urlencode($this->filters['filterby']).'&sortby=rewards&sortdir=' . $sortdir); ?>" title="<?php echo Lang::txt('COM_ANSWERS_SORT_REWARDS_TITLE'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_SORT_REWARDS'); ?>
								</a>
							</li>
						<?php } ?>
							<li>
								<a<?php echo ($this->filters['sortby'] == 'votes') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=' . urlencode($this->filters['filterby']).'&sortby=votes&sortdir=' . $sortdir); ?>" title="<?php echo Lang::txt('COM_ANSWERS_SORT_POPULAR_TITLE'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_SORT_POPULAR'); ?>
								</a>
							</li>
							<li>
								<a<?php echo ($this->filters['sortby'] == 'date') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=' . urlencode($this->filters['filterby']).'&sortby=date&sortdir=' . $sortdir); ?>" title="<?php echo Lang::txt('COM_ANSWERS_SORT_RECENT_TITLE'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_SORT_RECENT'); ?>
								</a>
							</li>
						</ul>

						<ul class="entries-menu filter-options" data-label="<?php echo Lang::txt('COM_ANSWERS_FILTER'); ?>">
							<li>
								<a<?php echo ($this->filters['filterby'] == 'all' || $this->filters['filterby'] == '') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=all&sortby=' . urlencode($this->filters['sortby'])); ?>" title="<?php echo Lang::txt('COM_ANSWERS_FILTER_ALL_TITLE'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_FILTER_ALL'); ?>
								</a>
							</li>
							<li>
								<a<?php echo ($this->filters['filterby'] == 'open') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=open&sortby=' . urlencode($this->filters['sortby'])); ?>" title="<?php echo Lang::txt('COM_ANSWERS_FILTER_OPEN_TITLE'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_FILTER_OPEN'); ?>
								</a>
							</li>
							<li>
								<a<?php echo ($this->filters['filterby'] == 'closed') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=closed&sortby=' . urlencode($this->filters['sortby'])); ?>" title="<?php echo Lang::txt('COM_ANSWERS_FILTER_CLOSED_TITLE'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_FILTER_CLOSED'); ?>
								</a>
							</li>
						</ul>
					</nav>

					<table class="questions entries">
						<caption>
							<?php
								$total = $this->results->count();
								$s = ($total > 0) ? $this->filters['start']+1 : $this->filters['start'];
								$e = ($total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $total;
								if ($this->filters['search'] != '')
								{
									echo Lang::txt('COM_ANSWERS_SEARCH_FOR', $this->escape($this->filters['search']), Lang::txt('COM_ANSWERS_FILTER_' . strtoupper($this->filters['filterby'])));
								}
								else
								{
									echo Lang::txt('COM_ANSWERS_FILTER_' . strtoupper($this->filters['filterby']));
								}
							?>
							<span>(<?php echo Lang::txt('COM_ANSWERS_RESULTS_TOTAL', $s, $e, $total); ?>)</span>
						</caption>
						<tbody>
				<?php if ($total > 0) { ?>
					<?php
					foreach ($this->results as $row)
					{
						// author name
						$name = Lang::txt('COM_ANSWERS_ANONYMOUS');
						if (!$row->get('anonymous'))
						{
							$name = $this->escape(stripslashes($row->creator->get('name', $name)));
							if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels()))
							{
								$name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
							}
						}
						$cls  = ($row->isclosed())   ? 'answered' : '';
						$cls  = ($row->isReported()) ? 'flagged'  : $cls;
						$cls .= ($row->get('created_by') == User::get('id')) ? ' mine' : '';
						?>
							<tr<?php echo ($cls) ? ' class="'.$cls.'"' : ''; ?>>
								<th class="priority-5" scope="row">
									<span class="entry-id"><?php echo $row->get('id'); ?></span>
								</th>
								<td>
									<?php if (!$row->isReported()) { ?>
										<a class="entry-title" href="<?php echo Route::url($row->link()); ?>">
											<?php echo $this->escape(strip_tags($row->get('subject'))); ?>
										</a><br />
									<?php } else { ?>
										<span class="entry-title">
											<?php echo Lang::txt('COM_ANSWERS_QUESTION_UNDER_REVIEW'); ?>
										</span><br />
									<?php } ?>
									<span class="entry-details">
										<?php echo Lang::txt('COM_ANSWERS_ASKED_BY', $name) . ' '; ?>
										<span class="entry-date-at"><?php echo Lang::txt('COM_ANSWERS_DATETIME_AT'); ?> </span>
										<span class="entry-time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time> </span>
										<span class="entry-date-on"><?php echo Lang::txt('COM_ANSWERS_DATETIME_ON'); ?> </span>
										<span class="entry-date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time> </span>
										<span class="entry-details-divider">&bull;</span>
										<span class="entry-state">
											<?php echo ($row->get('state')==1) ? Lang::txt('COM_ANSWERS_STATE_CLOSED') : Lang::txt('COM_ANSWERS_STATE_OPEN'); ?>
										</span>
										<span class="entry-details-divider">&bull;</span>
										<span class="entry-comments">
											<a href="<?php echo Route::url($row->link() . '#answers'); ?>" title="<?php echo Lang::txt('COM_ANSWERS_RESPONSES_TO_THIS_QUESTION', $row->get('rcount')); ?>">
												<?php echo $row->responses->count(); ?>
											</a>
										</span>
									</span>
								</td>
								<?php if ($this->config->get('banking')) { ?>
									<td class="priority-3 reward">
										<?php if ($row->get('reward')) { ?>
											<span class="entry-reward">
												<?php echo $row->get('points'); ?>
												<a href="<?php echo $this->config->get('infolink'); ?>" title="<?php echo Lang::txt('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->get('points')); ?>">
													<?php echo Lang::txt('COM_ANSWERS_POINTS'); ?>
												</a>
											</span>
										<?php } ?>
									</td>
								<?php } ?>
								<td class="priority-4 voting">
									<?php
									$this->view('_vote')
										->set('option', $this->option)
										->set('item', $row)
										->set('vote', $row->ballot())
										->display();
									?>
								</td>
							</tr>
					<?php } // end foreach ?>
				<?php } else { ?>
							<tr class="noresults">
								<td>
									<?php echo Lang::txt('COM_ANSWERS_NO_RESULTS'); ?>
								</td>
							</tr>
				<?php } // end if (count($this->results) > 0) { ?>
						</tbody>
					</table>
					<?php
					// Initiate paging
					$pageNav = $this->results->pagination;
					$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
					$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
					$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
					$pageNav->setAdditionalUrlParam('area', $this->filters['area']);
					$pageNav->setAdditionalUrlParam('sortdir', $this->filters['sort_Dir']);
					echo $pageNav;
					?>
					<div class="clearfix"></div>
				</div><!-- / .container -->
			</form>
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_ANSWERS_NEED_AN_ANSWER'); ?></h3>
				<p>
					<?php echo Lang::txt('COM_ANSWERS_CANT_FIND_ANSWER', '<a href="' . Route::url('index.php?option=com_kb') . '">' . Lang::txt('COM_ANSWERS_KNOWLEDGE_BASE') . '</a>', Config::get('sitename')); ?>
				</p>
			</div><!-- / .container -->
			<div class="container">
				<h3><?php echo Lang::txt('COM_ANSWERS_GET_STARTED'); ?></h3>
				<p>
					<?php echo Lang::txt('COM_ANSWERS_GET_STARTED_HELP', Route::url('index.php?option=com_help&component=answers&page=index')); ?>
				</p>
			</div><!-- / .container -->
			<?php if ($this->config->get('banking')) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('COM_ANSWERS_EARN_POINTS'); ?></h3>
					<p>
						<?php echo Lang::txt('COM_ANSWERS_START_EARNING_POINTS'); ?> <a href="<?php echo $this->config->get('infolink'); ?>"><?php echo Lang::txt('COM_ANSWERS_LEARN_MORE'); ?></a>.
					</p>
				</div><!-- / .container -->
			<?php } ?>
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
