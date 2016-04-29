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

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>" method="post">
		<div class="subject">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('COM_MEMBERS_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('COM_MEMBERS_SEARCH_LABEL'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="index" value="<?php echo $this->escape($this->filters['index']); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<?php
			$qs = array();
			foreach ($this->filters as $f=>$v)
			{
				$qs[] = ($v != '' && $f != 'index' && $f != 'authorized' && $f != 'start') ? $f . '=' . $v : '';
			}
			$qs[] = 'limitstart=0';
			$qs = implode('&', $qs);

			$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

			$url  = 'index.php?option=' . $this->option . '&task=browse';
			$url .= ($qs != '') ? '&' . $qs : '';

			$html  = '<a href="' . Route::url($url) . '"';
			if ($this->filters['index'] == '')
			{
				$html .= ' class="active-index"';
			}
			$html .= '>' . Lang::txt('COM_MEMBERS_BROWSE_FILTER_ALL') . '</a> ' . "\n";
			foreach ($letters as $letter)
			{
				$url  = 'index.php?option=' . $this->option . '&task=browse&index=' . strtolower($letter);
				$url .= ($qs != '') ? '&' . $qs : '';

				$html .= '<a href="' . Route::url($url) . '"';
				if ($this->filters['index'] == strtolower($letter))
				{
					$html .= ' class="active-index"';
				}
				$html .= '>' . $letter . '</a> ' . "\n";
			}
			?>
			<div class="container">
				<nav class="entries-filters">
					<ul class="entries-menu order-options">
						<li>
							<a<?php echo ($this->filters['sortby'] == 'name') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show='.$this->filters['show'] . '&sortby=name'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_BY_NAME'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_NAME'); ?>
							</a>
						</li>
						<li>
							<a<?php echo ($this->filters['sortby'] == 'organization') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show='.$this->filters['show'] . '&sortby=organization'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_BY_ORG'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_ORG'); ?>
							</a>
						</li>
						<?php if ($this->contribution_counting) { ?>
						<li>
							<a<?php echo ($this->filters['sortby'] == 'contributions') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show='.$this->filters['show'] . '&sortby=contributions'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_BY_CONTRIBUTIONS'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_CONTRIBUTIONS'); ?>
							</a>
						</li>
						<?php } ?>
					</ul>

					<ul class="entries-menu filter-options">
						<li>
							<a<?php echo ($this->filters['show'] != 'contributors') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&sortby=' . $this->filters['sortby']); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_BY_ALL'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_ALL'); ?>
							</a>
						</li>
						<?php if ($this->contribution_counting) { ?>
						<li>
							<a<?php echo ($this->filters['show'] == 'contributors') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show=contributors&sortby=' . $this->filters['sortby']); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_BY_CONTRIBUTORS'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_CONTRIBUTORS'); ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</nav>

				<table class="members entries">
					<caption>
						<?php
						$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
						$e = ($this->filters['limit'] == 0) ? $this->total : $e;

						if ($this->filters['show'] != 'contributors') {
							$title = 'COM_MEMBERS_BROWSE_ALL_MEMBERS';
						} else {
							$title = 'COM_MEMBERS_BROWSE_CONTRIBUTORS';
						}
						if ($this->filters['index']) {
							$title = Lang::txt($title . '_STARTING_WITH', $this->escape(strToUpper($this->filters['index'])));
						}
						else
						{
							$title = Lang::txt($title);
						}
						if ($this->filters['search'] != '')
						{
							$title = Lang::txt('COM_MEMBERS_SEARCH_FOR_IN', $this->escape($this->filters['search']), $title);
						}
						echo $title;
						?>
						<span><?php echo Lang::txt('COM_MEMBERS_BROWSE_NUM_OF_RESULTS', $s, $e, $this->total); ?></span>
					</caption>
					<thead>
						<tr>
							<th colspan="4">
								<span class="index-wrap">
									<span class="index">
										<?php echo $html; ?>
									</span>
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (count($this->rows) > 0)
					{
						$areas = array();
						$activeareas = Event::trigger('members.onMembersContributionsAreas', array($this->authorized));
						foreach ($activeareas as $area)
						{
							$areas = array_merge($areas, $area);
						}

						$cols = 2;

						$cls = ''; //'even';

						// User messaging
						$messaging = false;
						if ($this->config->get('user_messaging') > 0 && !User::isGuest())
						{
							switch ($this->config->get('user_messaging'))
							{
								case 1:
									// Get the groups the visiting user
									$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
									$usersgroups = array();
									if (!empty($xgroups))
									{
										foreach ($xgroups as $group)
										{
											if ($group->regconfirmed)
											{
												$usersgroups[] = $group->cn;
											}
										}
									}
								break;

								case 2:
								case 0:
								default:
								break;
							}
							$messaging = true;
						}
						if (!Plugin::isEnabled('members', 'messages'))
						{
							$messaging = false;
						}

						foreach ($this->rows as $row)
						{
							//$cls = ($cls == 'odd') ? 'even' : 'odd';
							$cls = '';
							if ($row->public != 1)
							{
								$cls = 'private';
							}

							if ($row->uidNumber < 0)
							{
								$id = 'n' . -$row->uidNumber;
							}
							else
							{
								$id = $row->uidNumber;
							}

							if ($row->uidNumber == User::get('id'))
							{
								$cls .= ($cls) ? ' me' : 'me';
							}

							// User name
							$row->name       = stripslashes($row->name);
							$row->surname    = stripslashes($row->surname);
							$row->givenName  = stripslashes($row->givenName);
							$row->middelName = stripslashes($row->middleName);

							if (!$row->surname)
							{
								$bits = explode(' ', $row->name);
								$row->surname = array_pop($bits);
								if (count($bits) >= 1)
								{
									$row->givenName = array_shift($bits);
								}
								if (count($bits) >= 1)
								{
									$row->middleName = implode(' ', $bits);
								}
							}

							$name = ($row->surname) ? stripslashes($row->surname) : '';
							if ($row->givenName)
							{
								$name .= ($row->surname) ? ', ' : '';
								$name .= stripslashes($row->givenName);
								$name .= ($row->middleName) ? ' ' . stripslashes($row->middleName) : '';
							}
							if (!trim($name))
							{
								$name = Lang::txt('COM_MEMBERS_UNKNOWN') . ' (' . $row->username . ')';
							}

							$profile = new \Hubzero\User\Profile();
							$profile->set('uidNumber', $row->uidNumber);
							$profile->set('email', $row->email);
							$profile->set('picture', $row->picture);

							$p = \Hubzero\User\Profile\Helper::getMemberPhoto($profile);

							// User messaging
							$messageuser = false;
							if ($messaging && $row->uidNumber > 0 && $row->uidNumber != User::get('id'))
							{
								switch ($this->config->get('user_messaging'))
								{
									case 1:
										// Get the groups of the profile
										$pgroups = \Hubzero\User\Helper::getGroups($row->uidNumber, 'all');
										// Get the groups the user has access to
										$profilesgroups = array();
										if (!empty($pgroups))
										{
											foreach ($pgroups as $group)
											{
												if ($group->regconfirmed)
												{
													$profilesgroups[] = $group->cn;
												}
											}
										}

										// Find the common groups
										$common = array_intersect($usersgroups, $profilesgroups);

										if (count($common) > 0)
										{
											$messageuser = true;
										}
									break;

									case 2:
										$messageuser = true;
									break;

									case 0:
									default:
										$messageuser = false;
									break;
								}
							}
					?>
						<tr<?php echo ($cls) ? ' class="'.$cls.'"' : ''; ?>>
							<td class="entry-img">
								<img width="50" height="50" src="<?php echo $p; ?>" alt="<?php echo Lang::txt('COM_MEMBERS_BROWSE_AVATAR', $this->escape($name)); ?>" />
							</td>
							<td>
								<a class="entry-title" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $id); ?>">
									<?php echo $name; ?>
								</a>
							<?php if ($row->organization && $this->registration->Organization != REG_HIDE) { ?>
								<br />
								<span class="entry-details">
									<span class="organization"><?php echo $this->escape(stripslashes($row->organization)); ?></span>
								</span>
							<?php } ?>
							</td>
							<td class="priority-4">
								<?php if ($this->contribution_counting) { ?>
								<!-- rcount: <?php echo $row->rcount; ?> -->
								<span class="activity"><?php echo $row->resource_count . ' Resources, ' . $row->wiki_count . ' Topics'; ?></span>
								<?php } ?>
							</td>
							<td class="message-member">
							<?php if ($messageuser) { ?>
								<a class="tooltips" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . User::get('id') . '&active=messages&task=new&to[]=' . $row->uidNumber); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE_TO_TITLE', $this->escape($name)); ?>">
									<?php echo Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE_TO', $this->escape($name)); ?>
								</a>
							<?php } ?>
							</td>
						</tr>
					<?php
						}
					} else { ?>
						<tr>
							<td colspan="4">
								<p class="warning"><?php echo Lang::txt('COM_MEMBERS_BROWSE_NO_MEMBERS_FOUND'); ?></p>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php
					$pageNav = $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					$pageNav->setAdditionalUrlParam('index', $this->filters['index']);
					$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
					$pageNav->setAdditionalUrlParam('show', $this->filters['show']);
					echo $pageNav->render();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->

		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_MEMBERS_BROWSE_SITE_MEMBERS'); ?></h3>
				<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_EXPLANATION'); ?></p>
				<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_SORTING_EXPLANATION'); ?></p>
				<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_SEARCH_EXPLANATION'); ?></p>
			</div><!-- / .container -->

			<div class="container">
				<h3><?php echo Lang::txt('COM_MEMBERS_BROWSE_MEMBER_STATS'); ?></h3>
				<table>
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_MEMBERS_BROWSE_TOTAL_MEMBERS'); ?></th>
							<td><span class="item-count"><?php echo $this->total_members; ?></span></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_MEMBERS_BROWSE_PRIVATE_PROFILES'); ?></th>
							<td><span class="item-count"><?php echo $this->total_members - $this->total_public_members; ?></span></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_MEMBERS_BROWSE_NEW_PROFILES'); ?></th>
							<td><span class="item-count"><?php echo $this->past_month_members; ?></span></td>
						</tr>
					</tbody>
				</table>
			</div><!-- / .container -->

			<div class="container">
				<h3><?php echo Lang::txt('COM_MEMBERS_BROWSE_LOOKING_FOR_GROUPS'); ?></h3>
				<p>
					<?php echo Lang::txt('COM_MEMBERS_BROWSE_GO_TO_GROUPS', Route::url('index.php?option=com_groups')); ?>
				</p>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</form>
</section><!-- / .main section -->
