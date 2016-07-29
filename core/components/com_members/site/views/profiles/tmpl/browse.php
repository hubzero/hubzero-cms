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

$this->css()
     ->js()
     ->js('hubzero', 'system')
     ->js('browse');

$base = 'index.php?option=' . $this->option . '&task=browse';

$exclude = array();
if (!empty($this->filters['q']))
{
	foreach ($this->filters['q'] as $q)
	{
		$exclude[] = strtolower($q['human_field']);
	}
}

$fields = Components\Members\Helpers\Filters::getFieldNames(); //$exclude);
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<form action="<?php echo Route::url($base); ?>" method="get">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER'); ?>" />
					<fieldset class="entry-search">
						<?php echo $this->autocompleter('tags', 'tags', $this->escape($this->filters['tags']), 'actags'); ?>
					</fieldset>
				</div><!-- / .container -->
			</form>

			<form action="<?php echo Route::url($base); ?>" method="get">
				<div id="add-filters">
					<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_RESULTS'); ?>:
						<select name="q[0][field]" id="filter-field" data-base="<?php echo rtrim(Request::root(), '/'); ?>">
							<?php foreach ($fields as $c) : ?>
								<?php
								if (in_array($c['raw'], $exclude))
								{
									continue;
								}
								?>
								<option value="<?php echo $this->escape($c['raw']); ?>"><?php echo $this->escape($c['human']); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo Components\Members\Helpers\Filters::buildSelectOperators(); ?>
						<input type="text" name="q[0][value]" id="filter-value" value="" />
						<?php
							$qs = array();
							foreach ($this->filters['q'] as $i => $q) :
								echo '<input type="hidden" name="q[' . ($i + 1) . '][field]" value="' . $this->escape($q['field']) . '" />' . "\n";
								echo '<input type="hidden" name="q[' . ($i + 1) . '][operator]" value="' . $this->escape($q['operator']) . '" />' . "\n";
								echo '<input type="hidden" name="q[' . ($i + 1) . '][value]" value="' . $this->escape($q['value']) . '" />' . "\n";

								$qs[$i] = '&q[' . $i . '][field]=' . $q['field'] . '&q[' . $i . '][operator]=' . $q['operator'] . '&q[' . $i . '][value]=' . $q['value'];
							endforeach;
							?>
						<input class="btn btn-secondary" id="filter-submit" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_ADD'); ?>" />
					</p>
				</div><!-- / .filters -->
			</form>

			<?php if (!empty($this->filters['q']) || (is_array($this->filters['search']) && !empty($this->filters['search'][0]))) : ?>
				<div id="applied-filters">
					<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_APPLIED'); ?>:</p>
					<ul class="filters-list">
						<?php if (!empty($this->filters['q'])) : ?>
							<?php foreach ($this->filters['q'] as $i => $q) : ?>
								<?php
								$route = $base;
								foreach ($qs as $k => $s)
								{
									if ($k == $i)
									{
										continue;
									}
									$route .= $s;
								}
								?>
								<li>
									<a href="<?php echo Route::url($route); ?>"
										class="filters-x">x
									</a>
									<i><?php echo $q['human_field'] . ' ' . $q['human_operator']; ?></i>: <?php echo $this->escape($q['human_value']); ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (is_array($this->filters['search']) && !empty($this->filters['search'][0])) : ?>
							<li>
								<a href="<?php echo Route::url($base . '&search='); ?>" class="filters-x">x</a>
								<i><?php echo Lang::txt('COM_MEMBERS_SEARCH'); ?></i>: <?php echo $this->escape(implode(' ', $this->filters['search'])); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form class="container members-container" action="<?php echo Route::url($base); ?>" method="get">
				<div class="results tiled members">
					<?php
					if ($this->rows->count() > 0)
					{
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
									$xgroups = User::groups();
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
							$cls = '';
							if ($row->get('access') != 1)
							{
								$cls = 'private';
							}

							if ($row->get('id') < 0)
							{
								$id = 'n' . -$row->get('id');
							}
							else
							{
								$id = $row->get('id');
							}

							if ($row->get('id') == User::get('id'))
							{
								$cls .= ($cls) ? ' me' : 'me';
							}

							// User name
							if (!$row->get('surname'))
							{
								$bits = explode(' ', $row->get('name'));

								$row->set('surname', array_pop($bits));
								if (count($bits) >= 1)
								{
									$row->set('givenName', array_shift($bits));
								}
								if (count($bits) >= 1)
								{
									$row->set('middleName', implode(' ', $bits));
								}
							}

							$name = stripslashes($row->get('surname', ''));
							if ($row->get('givenName'))
							{
								$name .= ($row->get('surname')) ? ', ' : '';
								$name .= stripslashes($row->get('givenName'));
								$name .= ($row->get('middleName')) ? ' ' . stripslashes($row->get('middleName')) : '';
							}
							if (!trim($name))
							{
								$name = Lang::txt('COM_MEMBERS_UNKNOWN') . ' (' . $row->get('username') . ')';
							}

							// User messaging
							$messageuser = false;
							if ($messaging && $row->get('id') > 0 && $row->get('uidNumber') != User::get('id'))
							{
								switch ($this->config->get('user_messaging'))
								{
									case 1:
										// Get the groups of the profile
										$pgroups = $row->groups();
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
							<div class="result<?php echo ($cls) ? ' ' . $cls : ''; ?>">
								<div class="result-body">
									<div class="result-img">
										<img src="<?php echo $row->picture(); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_BROWSE_AVATAR', $this->escape($name)); ?>" />
									</div>
									<div class="result-title">
										<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $id); ?>">
											<?php echo $name; ?>
										</a>
										<?php foreach ($fields as $c) { ?>
											<?php
											if (!in_array($c['raw'], array('org', 'organization'))) {
												continue;
											}

											if ($val = $row->get($c['raw'])) { ?>
												<span class="result-details">
													<br />
													<span class="<?php echo $this->escape($c['raw']); ?>"><?php echo $this->escape(Hubzero\Utility\String::truncate(stripslashes($val), 60)); ?></span>
												</span>
											<?php } ?>
										<?php } ?>
									</div>
									<div class="result-snippet">
										<?php foreach ($fields as $c) { ?>
											<?php
											if (in_array($c['raw'], array('name', 'org', 'organization'))) {
												continue;
											}

											if ($val = $row->get($c['raw'])) {
												$val = (is_array($val) ? implode(', ', $val) : $val);
											?>
												<div class="result-snippet-<?php echo $this->escape($c['raw']); ?>"><?php echo $this->escape(Hubzero\Utility\String::truncate(strip_tags(stripslashes($val)), 150)); ?></div>
											<?php } ?>
										<?php } ?>
									</div>
									<?php if ($messageuser) { ?>
										<div class="result-extras message-member">
											<a class="btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . User::get('id') . '&active=messages&task=new&to[]=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE_TO_TITLE', $this->escape($name)); ?>">
												<?php echo Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE'); ?>
											</a>
										</div>
									<?php } ?>
									<?php if (!User::isGuest() && User::get('id') == $row->get('id')) { ?>
										<div class="result-extras">
											<span class="you">
												<?php echo Lang::txt('COM_MEMBERS_BROWSE_YOUR_PROFILE'); ?>
											</span>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php
						}
					} else { ?>
						<div class="results-none">
							<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_NO_MEMBERS_FOUND'); ?></p>
						</div>
					<?php } ?>
				</div>
				<?php
				$pageNav = $this->rows->pagination;
				if ($this->filters['search'])
				{
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
				}
				if ($this->filters['tags'])
				{
					$pageNav->setAdditionalUrlParam('tags', $this->filters['tags']);
				}
				/*if ($this->filters['sortby'])
				{
					$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
				}*/
				if (!empty($this->filters['q']))
				{
					foreach ($this->filters['q'] as $i => $q)
					{
						$pageNav->setAdditionalUrlParam('q[' . $i . '][field]', $q['human_field']);
						$pageNav->setAdditionalUrlParam('q[' . $i . '][operator]', $q['o']);
						$pageNav->setAdditionalUrlParam('q[' . $i . '][value]', strtolower($q['human_value']));
					}
				}
				echo $pageNav;
				?>
				<div class="clearfix"></div>
			</form><!-- / .container -->
		</div><!-- / .subject -->

		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_MEMBERS_BROWSE_SITE_MEMBERS'); ?></h3>
				<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_EXPLANATION'); ?></p>
				<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_SORTING_EXPLANATION'); ?></p>
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
	</div>
</section><!-- / .main section -->
