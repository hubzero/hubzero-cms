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

$fields = Components\Members\Helpers\Filters::getFieldNames($exclude);
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<form action="<?php echo Route::url($base); ?>" method="get">
				<?php /*<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<legend><?php echo Lang::txt('COM_MEMBERS_SEARCH_LEGEND'); ?></legend>
						<label for="entry-search-field"><?php echo Lang::txt('COM_MEMBERS_SEARCH_LABEL'); ?></label>
						<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape((is_array($this->filters['search']) && !empty($this->filters['search'][0])) ? implode(' ', $this->filters['search']) : ''); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER'); ?>" />
						<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					</fieldset>
				</div><!-- / .container -->*/ ?>

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
						<select name="q[field]" id="filter-field" data-base="<?php echo rtrim(Request::root(), '/'); ?>">
							<?php foreach ($fields as $c) : ?>
								<option value="<?php echo $this->escape($c['raw']); ?>"><?php echo $this->escape($c['human']); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo Components\Members\Helpers\Filters::buildSelectOperators(); ?>
						<input type="text" name="q[value]" id="filter-value" value="" />
						<input class="btn btn-secondary" id="filter-submit" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_ADD'); ?>" />
					</p>
				</div><!-- / .filters -->
			</form>

			<?php if (!empty($this->filters['q']) || (is_array($this->filters['search']) && !empty($this->filters['search'][0]))) : ?>
				<div id="applied-filters">
					<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_APPLIED'); ?>:</p>
					<ul class="filters-list">
						<?php if (!empty($this->filters['q'])) : ?>
							<?php foreach ($this->filters['q'] as $q) : ?>
								<li>
									<a href="<?php echo Route::url($base . '&q[field]=' . $q['field'] . '&q[operator]=' . $q['operator'] . '&q[value]=' . $q['value'] . '&q[delete]'); ?>"
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

			<form class="container" action="<?php echo Route::url($base); ?>" method="get">
				<?php /*<nav class="entries-filters">
					<ul class="entries-menu order-options">
						<li>
							<a<?php echo ($this->filters['sortby'] == 'name') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=name'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_BY_NAME'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_NAME'); ?>
							</a>
						</li>
						<li>
							<a<?php echo ($this->filters['sortby'] == 'organization') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse&sortby=organization'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_BY_ORG'); ?>">
								<?php echo Lang::txt('COM_MEMBERS_BROWSE_SORT_ORG'); ?>
							</a>
						</li>
						<li>
							Sort by:
							<select name="sortby" id="filter-sortby">
								<option value="name"<?php echo ($this->filters['sortby'] == 'name') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_MEMBERS_NAME'); ?></option>
								<?php if (User::authorise('core.manage', $this->option)): ?>
									<option value="username"<?php echo ($this->filters['sortby'] == 'username') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_MEMBERS_USERNAME'); ?></option>
									<option value="id"<?php echo ($this->filters['sortby'] == 'id') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_MEMBERS_ID'); ?></option>
									<option value="registerDate"<?php echo ($this->filters['sortby'] == 'registerDate') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_MEMBERS_REGISTERDATE'); ?></option>
								<?php endif; ?>
								<?php foreach (Components\Members\Helpers\Filters::getFieldNames() as $c) : ?>
									<option value="<?php echo $this->escape($c['raw']); ?>"<?php echo ($this->filters['sortby'] == $c['raw']) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($c['human']); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
					</ul>
				</nav>*/ ?>

				<table class="members entries">
					<tbody>
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

							$id = $row->get('id');

							if ($id < 0)
							{
								$id = 'n' . -$id;
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
							if ($messaging && $row->get('id') > 0 && $row->get('id') != User::get('id'))
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
						<tr<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>>
							<td class="entry-img">
								<img width="50" height="50" src="<?php echo $row->picture(); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_BROWSE_AVATAR', $this->escape($name)); ?>" />
							</td>
							<td>
								<a class="entry-title" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $id); ?>">
									<?php echo $name; ?>
								</a>
								<?php if ($org = $row->get('organization')) { ?>
									<br />
									<span class="entry-details">
										<span class="organization"><?php echo $this->escape(stripslashes($org)); ?></span>
									</span>
								<?php } ?>
							</td>
							<td class="message-member">
								<?php if ($messageuser) { ?>
									<a class="tooltips" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . User::get('id') . '&active=messages&task=new&to[]=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE_TO_TITLE', $this->escape($name)); ?>">
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
				$pageNav = $this->rows->pagination;
				$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
				//$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
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
