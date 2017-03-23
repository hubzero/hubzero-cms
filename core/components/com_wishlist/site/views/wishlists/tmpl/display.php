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

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('vote.css')
     ->js();

$sitename = Config::get('sitename');

$base = $this->wishlist->link();

$cloud = new \Components\Wishlist\Models\Tags($this->wishlist->get('id'));

// Wish List
if ($this->wishlist->get('id'))
{
	if (!$this->wishlist->isPublic() && !$this->wishlist->access('manage')) { ?>
		<section class="main section">
			<p class="waring"><?php echo Lang::txt('WARNING_NOT_AUTHORIZED_PRIVATE_LIST'); ?></p>
		</section><!-- / .main section -->
	<?php } else { ?>
		<header id="content-header">
			<h2><?php echo $this->title; ?></h2>

			<div id="content-header-extra">
				<ul id="useroptions">
					<li class="last">
						<a class="icon-add add btn" href="<?php echo Route::url($this->wishlist->link('new')); ?>">
							<?php echo Lang::txt('COM_WISHLIST_TASK_ADD'); ?>
						</a>
					</li>
				</ul>
			</div><!-- / #content-header-extra -->
		</header><!-- / #content-header -->

		<form method="get" action="<?php echo Route::url($base); ?>">
			<?php
			// Admin messages
			if ($this->wishlist->access('manage') && !$this->getError())
			{
				// Wish was deleted from the list
				if ($this->task == 'deletewish')
				{
					echo '<p class="passed">'.Lang::txt('COM_WISHLIST_NOTICE_WISH_DELETED').'</p>'."\n";
				}

				// Wish was moved to a new list
				if ($this->task == 'movewish')
				{
					echo '<p class="passed">'.Lang::txt('COM_WISHLIST_NOTICE_WISH_MOVED').'</p>'."\n";
				}

				switch ($this->wishlist->get('saved'))
				{
					case '1':
						// List settings saved
						echo '<p class="passed">'.Lang::txt('COM_WISHLIST_NOTICE_LIST_SETTINGS_SAVED').'</p>'."\n";
					break;
					case '2':
						// Changes to wish saved
						echo '<p class="passed">'.Lang::txt('COM_WISHLIST_NOTICE_WISH_CHANGES_SAVED').'</p>'."\n";
					break;
					case '3':
						// New wish posted
						echo '<p class="passed">'.Lang::txt('COM_WISHLIST_NOTICE_WISH_POSTED').'</p>'."\n";
					break;
				}
			}
			?>
			<?php if ($this->getError()) { ?>
				<p class="error"><?php echo $this->getError(); ?></p>
			<?php } ?>

			<section class="main section">
				<div class="subject">
					<div class="container data-entry">
						<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_WISHLIST_SEARCH'); ?>" />
						<fieldset class="entry-search">
							<legend><?php echo Lang::txt('COM_WISHLIST_SEARCH_LEGEND'); ?></legend>

							<label for="entry-search-field"><?php echo Lang::txt('COM_WISHLIST_SEARCH_LABEL'); ?></label>
							<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_WISHLIST_SEARCH_PLACEHOLDER'); ?>" />

							<input type="hidden" name="tags" value="<?php echo $this->escape($this->filters['tag']); /* xss fix for ticket 1413/1417 */ ?>" />
							<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); /* xss fix for ticket 1413/1417 */ ?>" />
							<input type="hidden" name="filterby" value="<?php echo $this->escape($this->filters['filterby']); /* xss fix for ticket 1412/1419 */?>" />

							<input type="hidden" name="task" value="<?php echo $this->escape($this->task); /* XSS fix, see ticket 1420*/ ?>" />
							<input type="hidden" name="newsearch" value="1" />
						</fieldset>
						<?php if ($this->filters['tag']) { ?>
							<fieldset class="applied-tags">
								<ol class="tags">
								<?php
								$url  = $base;
								$url .= ($this->filters['search']   ? '&search=' . $this->escape($this->filters['search'])     : '');
								$url .= ($this->filters['sortby']   ? '&sortby=' . $this->escape($this->filters['sortby'])     : '');
								$url .= ($this->filters['filterby'] ? '&filterby=' . $this->escape($this->filters['filterby']) : '');

								$tags = $cloud->parseTags($this->filters['tag']);
								foreach ($tags as $tag)
								{
									?>
									<li>
										<a class="tag" href="<?php echo Route::url($url . '&tag=' . implode(',', $cloud->parseTags($this->filters['tag'], $tag))); ?>">
											<?php echo $this->escape(stripslashes($tag)); ?>
											<span class="remove">x</a>
										</a>
									</li>
									<?php
								}
								?>
								</ol>
							</fieldset>
						<?php } ?>
					</div><!-- / .container data-entry -->

					<div class="container">
						<nav class="entries-filters">
							<ul class="entries-menu order-options" data-label="<?php echo Lang::txt('COM_WISHLIST_SORT'); ?>">
								<?php if ($this->wishlist->get('admin')) { ?>
									<li><a class="sort-ranking<?php if ($this->filters['sortby'] == 'ranking') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby='.$this->filters['filterby'].'&sortby=ranking&tags='.$this->filters['tag']); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SORT_RANKING_TITLE'); ?>"><?php echo Lang::txt('COM_WISHLIST_SORT_RANKING'); ?></a></li>
								<?php } ?>
								<?php if ($this->wishlist->get('banking')) { ?>
									<li><a class="sort-bonus<?php if ($this->filters['sortby'] == 'bonus') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby='.$this->filters['filterby'].'&sortby=bonus&tags='.$this->filters['tag']); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SORT_BONUS_TITLE'); ?>"><?php echo Lang::txt('COM_WISHLIST_SORT_BONUS'); ?></a></li>
								<?php } ?>
								<li><a class="sort-feedback<?php if ($this->filters['sortby'] == 'feedback') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby='.$this->filters['filterby'].'&sortby=feedback&tags='.$this->filters['tag']); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SORT_FEEDBACK_TITLE'); ?>"><?php echo Lang::txt('COM_WISHLIST_SORT_FEEDBACK'); ?></a></li>
								<li><a class="sort-submitter<?php if ($this->filters['sortby'] == 'submitter') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby='.$this->filters['filterby'].'&sortby=submitter&tags='.$this->filters['tag']); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SORT_SUBMITTER_TITLE'); ?>"><?php echo Lang::txt('COM_WISHLIST_SORT_SUBMITTER'); ?></a></li>
								<li><a class="sort-date<?php if ($this->filters['sortby'] == 'date') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby='.$this->filters['filterby'].'&sortby=date&tags='.$this->filters['tag']); ?>" title="<?php echo Lang::txt('COM_WISHLIST_SORT_DATE'); ?>"><?php echo Lang::txt('COM_WISHLIST_SORT_DATE'); ?></a></li>
							</ul>

							<ul class="entries-menu filter-options" data-label="<?php echo Lang::txt('COM_WISHLIST_FILTER'); ?>">
								<li><a class="filter-all<?php if ($this->filters['filterby'] == 'all') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby=all&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_ALL'); ?></a></li>
								<li><a class="filter-open<?php if ($this->filters['filterby'] == 'open') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby=open&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_OPEN'); ?></a></li>
								<li><a class="filter-accepted<?php if ($this->filters['filterby'] == 'accepted') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby=accepted&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_ACCEPTED'); ?></a></li>
								<li><a class="filter-rejected<?php if ($this->filters['filterby'] == 'rejected') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby=rejected&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_REJECTED'); ?></a></li>
								<li><a class="filter-granted<?php if ($this->filters['filterby'] == 'granted') { echo ' active'; } ?>" href="<?php echo Route::url($base .'&filterby=granted&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_GRANTED'); ?></a></li>
								<?php if (!User::isGuest()) { ?>
									<li><a class="filter-submitter<?php if ($this->filters['filterby'] == 'submitter') { echo ' active'; } ?>" href="<?php echo Route::url($base . '&filterby=submitter&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_SUBMITTER'); ?></a></li>
								<?php } ?>
								<?php if ($this->wishlist->access('manage')) { //1 or 2?>
									<li><a class="filter-public<?php if ($this->filters['filterby'] == 'public') { echo ' active'; } ?>" href="<?php echo Route::url($base . '&filterby=public&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_PUBLIC'); ?></a></li>
									<li><a class="filter-private<?php if ($this->filters['filterby'] == 'private') { echo ' active'; } ?>" href="<?php echo Route::url($base . '&filterby=private&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_PRIVATE'); ?></a></li>
									<?php if ($this->wishlist->access('own')) { // 2?>
										<li><a class="filter-mine<?php if ($this->filters['filterby'] == 'mine') { echo ' active'; } ?>" href="<?php echo Route::url($base . '&filterby=mine&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo Lang::txt('COM_WISHLIST_FILTER_MINE'); ?></a></li>
									<?php } ?>
								<?php } ?>
							</ul>
						</nav>

						<table class="ideas entries">
							<caption>
								<?php echo Lang::txt('COM_WISHLIST_FILTER_'.strtoupper($this->filters['filterby'])); ?>
								<?php echo ($this->filters['tag'] != '') ? Lang::txt('COM_WISHLIST_WISHES_TAGGED_WITH', $this->escape($this->filters['tag'])) : ''; ?>
								<span>
									(<?php echo ($this->total > 0) ? ($this->filters['start'] + 1) : $this->filters['start']; ?> - <?php echo $this->filters['start'] + $this->wishes->count(); ?> of <?php echo $this->total; ?>)
								</span>
							</caption>
							<tbody>
						<?php
						if ($this->wishes->count())
						{
							$y = 1;

							$filters  = '';
							foreach ($this->filters as $key => $flt)
							{
								if ($flt)
								{
									if ($key == 'comments')
									{
										continue;
									}
									$filters .= '&' . $key . '=' . $flt;
								}
							}

							foreach ($this->wishes as $item)
							{
								$item->set('category', $this->wishlist->get('category'));
								$item->set('referenceid', $this->wishlist->get('referenceid'));
								$item->set('bonus', ($this->wishlist->get('banking') ? $item->get('bonus') : 0));

								if ($item->isReported())
								{
									$status = 'outstanding';
								}
								else if ($item->get('ranked') && !$item->isGranted() && !$item->isWithdrawn() && !$item->isRejected() && $this->wishlist->access('manage'))
								{
									$status = 'unranked';
								}
								else
								{
									$status = 'outstanding';
								}

								$state = $item->status('alias');

								$name = Lang::txt('COM_WISHLIST_ANONYMOUS');
								if (!$item->get('anonymous'))
								{
									$name = $this->escape(stripslashes($item->proposer->get('name', $name)));
									if (in_array($item->proposer->get('access'), User::getAuthorisedViewLevels()))
									{
										$name = '<a href="' . Route::url($item->proposer->link()) . '">' . $name . '</a>';
									}
								}
								?>
								<tr class="<?php echo $state; ?>">
									<th class="priority-5 <?php echo $status; ?>">
										<span class="entry-status"><?php echo $status; ?></span>
									</th>
									<td>
								<?php if (!$item->isReported()) { ?>
										<a class="entry-title" href="<?php echo Route::url($item->link('permalink', $filters)); ?>">
											<?php echo $this->escape(stripslashes($item->get('subject'))); ?>
										</a>
										<br />
										<span class="entry-details">
											<span class="entry-identifier">#<?php echo $item->get('id'); ?></span>
											<span class="entry-details-divider">&bull;</span>
											<?php echo Lang::txt('COM_WISHLIST_WISH_PROPOSED_BY'); ?> <?php echo $name . ' '; ?>
											<span class="entry-date-at"><?php echo Lang::txt('COM_WISHLIST_AT'); ?></span>
											<span class="entry-time"><time datetime="<?php echo $item->proposed(); ?>"><?php echo $item->proposed('time'); ?></time></span>
											<span class="entry-date-on"><?php echo Lang::txt('COM_WISHLIST_ON'); ?></span>
											<span class="entry-date"><time datetime="<?php echo $item->proposed(); ?>"><?php echo $item->proposed('date'); ?></time></span>
											<span class="entry-details-divider">&bull;</span>
											<span class="entry-comments">
												<a href="<?php echo Route::url($item->link('comments')); ?>" title="<?php echo $item->comments()->total(); ?> <?php echo Lang::txt('COM_WISHLIST_COMMENTS'); ?>">
													<?php echo $item->comments()->total(); ?>
												</a>
											</span>
										</span>
								<?php } else { ?>
										<span class="warning adjust"><?php echo Lang::txt('COM_WISHLIST_NOTICE_POSTING_REPORTED'); ?></span>
								<?php } ?>
									</td>
								<?php if (!$item->isReported() && $this->wishlist->get('banking')) { ?>
									<td class="priority-5 reward">
										<span class="entry-reward">
										<?php if ($item->get('bonus') > 0 && ($item->isOpen() or $item->isAccepted())) { ?>
											<a class="bonus tooltips" href="<?php echo Route::url($item->link('addbonus', $filters)); ?>" title="<?php echo Lang::txt('COM_WISHLIST_WISH_ADD_BONUS').' ::'.$item->get('bonusgivenby').' '.Lang::txt('COM_WISHLIST_MULTIPLE_USERS').' '.Lang::txt('COM_WISHLIST_WISH_BONUS_CONTRIBUTED_TOTAL').' '.$item->get('bonus').' '.Lang::txt('COM_WISHLIST_POINTS').' '.Lang::txt('COM_WISHLIST_WISH_BONUS_AS_BONUS'); ?>">
												<?php echo $item->get('bonus', 0); ?> <span><?php echo Lang::txt('COM_WISHLIST_POINTS'); ?></span>
											</a>
										<?php } else if ($item->isOpen() or $item->isAccepted()) { ?>
											<a class="nobonus tooltips" href="<?php echo Route::url($item->link('addbonus', $filters)); ?>" title="<?php echo Lang::txt('COM_WISHLIST_WISH_ADD_BONUS').' :: '.Lang::txt('COM_WISHLIST_WISH_BONUS_NO_USERS_CONTRIBUTED'); ?>">
												<?php echo $item->get('bonus', 0); ?> <span><?php echo Lang::txt('COM_WISHLIST_POINTS'); ?></span>
											</a>
										<?php } else { ?>
											<span class="inactive" title="<?php echo Lang::txt('COM_WISHLIST_WISH_BONUS_NOT_ACCEPTED'); ?>">&nbsp;</span>
										<?php } ?>
										</span>
									</td>
								<?php } ?>
								<?php if (!$item->isReported()) { ?>
									<td class="priority-4 voting">
										<?php
										$this->view('_vote')
										     ->set('option', $this->option)
										     ->set('item', $item)
										     ->set('listid', $this->wishlist->get('id'))
										     ->set('plugin', 0)
										     ->set('admin', 0)
										     ->set('page', 'wishlist')
										     ->set('filters', $this->filters)
										     ->display();
										?>
									</td>
									<td class="priority-3 ranking">
										<?php
										$html = '';
										switch ($item->get('status'))
										{
											case 0:
												if (!$item->get('ranked') && $this->wishlist->access('manage'))
												{
													$html .= '<a class="rankit" href="' . $item->link('rank', $filters) . '">'.Lang::txt('COM_WISHLIST_WISH_RANK_THIS').'</a>'."\n";
												}
												else if ($item->get('ranked'))
												{
													//$html .= Lang::txt('WISH_PRIORITY').': <span class="priority">'.$item->ranking.'</span>'."\n";
													$html .= '<span class="priority-level-base">
														<span class="priority-level" style="width: '.(($item->get('ranking', 0)/50)*100).'%">
															<span>'.Lang::txt('COM_WISHLIST_WISH_PRIORITY').': '.$item->get('ranking', 0).'</span>
														</span>
													</span>';
												}
												if ($item->isAccepted())
												{
													$html .= '<span class="accepted">'.Lang::txt('COM_WISHLIST_WISH_STATUS_ACCEPTED').'</span>';
												}
											break;
											case 1:
												$html .= '<span class="granted">'.Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED').'</span>';
												/*if ($item->granted != '0000-00-00 00:00:00') {
													$html .= ' <span class="mini">'.strtolower(Lang::txt('ON')).' '.Date::of($item->granted)->toLocal(Lang::txt('DATE_FORMAT_HZ1')).'</span>';
												}*/
											break;
											case 3:
												$html .= '<span class="rejected">'.Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED').'</span>';
											break;
											case 4:
												$html .= '<span class="withdrawn">'.Lang::txt('COM_WISHLIST_WISH_STATUS_WITHDRAWN').'</span>';
											break;
										}
										echo $html;
										?>
									</td>
								<?php } // end if (!$item->isReported()) ?>
								</tr>
							<?php } // end foreach wish ?>
						<?php } else { ?>
								<tr>
									<td>
									<?php if ($this->filters['filterby'] == 'all' && !$this->filters['tag']) { ?>
										<p>
											<?php echo Lang::txt('COM_WISHLIST_NO_WISHES_BE_FIRST'); ?>
										</p>
									<?php } else { ?>
										<p class="noresults">
											<?php echo Lang::txt('COM_WISHLIST_NO_WISHES_SELECTION'); ?>
										</p>
										<p class="nav_wishlist">
											<a href="<?php echo Route::url($base); ?>"><?php echo Lang::txt('COM_WISHLIST_VIEW_ALL_WISHES'); ?></a>
										</p>
									<?php } ?>
									</td>
								</tr>
						<?php } // end if wishlist item ?>
							</tbody>
						</table>
						<?php
						// Page navigation
						$pageNav = $this->pagination(
							$this->total,
							$this->filters['start'],
							$this->filters['limit']
						);
						$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
						$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
						$pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
						$pageNav->setAdditionalUrlParam('newsearch', 0);
						if ($this->filters['search'])
						{
							$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
						}
						echo $pageNav->render();
						?>
						<div class="clearfix"></div>
					</div><!-- / .container -->
				</div><!-- / .subject -->
				<aside class="aside">
					<?php
						// Popular tags
						if ($this->wishlist->get('category') == 'general')
						{
							$tags = $cloud->render('html', array(
								'limit'    => $this->config->get('maxtags', 10),
								'start'    => 0,
								'sort'     => 'total',
								'sort_Dir' => '',
								'scope'    => 'wishlist',
								'scope_id' => 0,
								'base'     => $base,
								'filters'  => $this->filters
							));

							if ($tags)
							{
								?>
								<div class="container">
									<h3><?php echo Lang::txt('COM_WISHLIST_POPULAR_TAGS'); ?></h3>
									<?php echo $tags; ?>
									<p><?php echo Lang::txt('COM_WISHLIST_CLICK_TAG_TO_FILTER'); ?></p>
								</div><!-- / .container -->
								<?php
							} // end if ($tags)
						} // end if ($this->wishlist->category == 'general')

						if ($this->wishlist->get('category') == 'resource')
						{
							$html  = '<p>' . Lang::txt('COM_WISHLIST_THIS_LIST_IS_FOR_RES', '<a href="'.Route::url('index.php?option=com_resources&id=' . $this->wishlist->get('referenceid')).'">'.$this->escape($this->wishlist->item('title')).'</a>') . '</p>';
						}
						else if ($this->wishlist->get('description'))
						{
							$html  = '<p>' . $this->escape($this->wishlist->get('description')) . '<p>';
						}
						else
						{
							$html  = '<p>' . Lang::txt('COM_WISHLIST_HELP_US_IMPROVE', $sitename) . '</p>';
						}

						switch ($this->wishlist->get('admin'))
						{
							case '1':
								$html .= '<p class="info">' . Lang::txt('COM_WISHLIST_NOTICE_SITE_ADMIN') . '</p>' . "\n";
							break;
							case '2':
								$html .= '<p class="info">' . Lang::txt('COM_WISHLIST_NOTICE_LIST_ADMIN') . ' Edit <a href="' . Route::url($this->wishlist->link('settings')) . '">' . Lang::txt('COM_WISHLIST_LIST_SETTINGS') . '</a>.</p>' . "\n";
							break;
							case '3':
								$html .= '<p class="info">' . Lang::txt('COM_WISHLIST_NOTICE_ADVISORY_ADMIN') . '</p>'."\n";
							break;
						}
						echo $html;

						// Show what's popular
						if ($this->wishlist->access('manage')
						 && $this->wishes->count() >= 10
						 && $this->wishlist->get('category') == 'general'
						 && $this->filters['filterby'] == 'all')
						{
							Request::setVar('rid', $this->wishlist->get('referenceid'));
							Request::setVar('category', $this->wishlist->get('category'));

							echo \Hubzero\Module\Helper::renderModules('wishvoters');
						}
					?>
				</aside><!-- / .aside -->
			</section><!-- / .main section -->
		</form>
	<?php } // end if public ?>
<?php } else { ?>
	<p class="error"><?php echo Lang::txt('COM_WISHLIST_ERROR_LIST_NOT_FOUND'); ?></p>
<?php } // end if wish list ?>
