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

$searches = array();
$filters = array();
foreach ($this->fields as $field)
{
	if ($field->get('type') == 'hidden' || $field->get('type') == 'number')
	{
		continue;
	}
	if (in_array($field->get('type'), array('text', 'textarea', 'orcid', 'address')))
	{
		$searches[] = $field;
	}
	else
	{
		$filters[] = $field;
	}
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<form action="<?php echo Route::url($base); ?>" method="get" class="section-inner">
		<aside class="aside">
			<?php /*<div class="container">
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
			</div><!-- / .container -->*/ ?>

			<div class="container">
				<fieldset>
					<legend><?php echo Lang::txt('Narrow results'); ?></legend>

					<div class="input-wrap">
						<label for="filter-value-name">
							<?php echo Lang::txt('COM_MEMBERS_SEARCH'); ?>
							<input type="text" name="search" id="filter-value-name" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('Name or keyword...'); ?>" />
						</label>
					</div>

					<fieldset class="filters">
						<legend><?php echo Lang::txt('Filter'); ?></legend>

						<?php foreach ($filters as $field) : ?>
							<div class="input-wrap">
								<?php
								$value = array();
								foreach ($this->filters['q'] as $i => $q)
								{
									if ($q['field'] == $field->get('name'))
									{
										$value[] = $q['value'];
									}
								}
								?>
								<?php if ($field->get('type') == 'radio' || $field->get('type') == 'checkboxes') { ?>
									<fieldset>
										<legend><?php echo $this->escape($field->get('label')); ?></legend>
										<?php foreach ($field->options as $option) { ?>
											<label class="option" for="filter-value-<?php echo $this->escape($field->get('name') . '-' . $option->get('value')); ?>">
												<?php
												$checked = '';
												if (in_array($option->get('value'), $value))
												{
													$checked = 'checked="checked"';
												}
												?>
												<input class="option" type="checkbox" name="q[<?php echo $this->escape($field->get('name')); ?>][]" value="<?php echo $this->escape($option->get('value')); ?>" <?php echo $checked; ?> id="filter-value-<?php echo $this->escape($field->get('name') . '-' . $option->get('value')); ?>" />
												<?php echo $this->escape($option->get('label')); ?>
											</label>
										<?php } ?>
									</fieldset>
								<?php } elseif ($field->get('type') == 'select') { ?>
									<label for="filter-value-<?php echo $this->escape($field->get('name')); ?>">
										<?php echo $this->escape($field->get('label')); ?>
										<select name="q[<?php echo $this->escape($field->get('name')); ?>]" id="filter-value-<?php echo $this->escape($field->get('name')); ?>">
											<option value="">- All -</option>
											<?php foreach ($field->options as $option) { ?>
												<option value="<?php echo $this->escape($option->get('value')); ?>"<?php if (in_array($option->get('value'), $value)) { echo ' selected="selected"'; } ?>><?php echo $this->escape($option->get('label')); ?></option>
											<?php } ?>
										</select>
									</label>
								<?php } elseif ($field->get('type') == 'number') { ?>
									<label for="filter-value-<?php echo $this->escape($field->get('name')); ?>">
										<?php echo $this->escape($field->get('label')); ?>
										<?php if ($field->get('max')) { ?>
											<input type="range" name="q[<?php echo $this->escape($field->get('name')); ?>]" id="filter-value-<?php echo $this->escape($field->get('name')); ?>" min="<?php echo $field->get('min', 0); ?>" <?php if ($field->get('max')) { echo ' max="' . $field->get('max') . '"'; } ?> step="1" value="<?php echo $this->escape(implode('', $value)); ?>" />
										<?php } else{ ?>
											<input type="number" name="q[<?php echo $this->escape($field->get('name')); ?>]" id="filter-value-<?php echo $this->escape($field->get('name')); ?>" <?php if ($field->get('min')) { echo ' min="' . $field->get('min') . '"'; } ?> <?php if ($field->get('max')) { echo ' max="' . $field->get('max') . '"'; } ?> value="<?php echo $this->escape(implode('', $value)); ?>" />
										<?php } ?>
									</label>
								<?php } else { ?>
									<label for="filter-value-<?php echo $this->escape($field->get('name')); ?>">
										<?php echo $this->escape($field->get('label')); ?>
										<input type="text" name="q[<?php echo $this->escape($field->get('name')); ?>]" id="filter-value-<?php echo $this->escape($field->get('name')); ?>" value="<?php echo $this->escape(implode('', $value)); ?>" />
									</label>
								<?php } ?>
							</div>
						<?php endforeach; ?>
					</fieldset><!-- / filters -->

					<fieldset class="sorting">
						<legend><?php echo Lang::txt('Sort'); ?></legend>

						<div class="input-wrap">
							<label for="filter-value-sort">
								<?php echo Lang::txt('Sort by'); ?>
								<select name="sort" id="filter-value-sort">
									<option value="name"><?php echo $this->escape('Name'); ?></option>
									<?php foreach ($this->fields as $field) : ?>
										<option value="<?php echo $this->escape($field->get('name')); ?>"><?php echo $this->escape($field->get('label')); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
						</div>

						<div class="input-wrap">
							<label for="filter-value-sort-dir">
								<?php echo Lang::txt('Direction'); ?>
								<select name="sort_Dir" id="filter-value-sort-dir">
									<option value="asc"><?php echo $this->escape('Ascending (A-Z)'); ?></option>
									<option value="desc"><?php echo $this->escape('Descending (Z-A)'); ?></option>
								</select>
							</label>
						</div>
					</fieldset><!-- / sort -->

					<p><input class="btn" type="submit" value="<?php echo Lang::txt('Apply'); ?>" /></p>
				</fieldset>
			</div>
		</aside><!-- / .aside -->

		<div class="subject">
			<?php if (!empty($this->filters['q'])) : ?>
				<?php
				$qs = array();
				foreach ($this->filters['q'] as $i => $q) :
					if (is_array($q['value']))
					{
						$qs[$i] = '&';
						foreach ($q['value'] as $key => $val)
						{
							$qs[$i] .= 'q[' . $q['field'] . '][]=' . $val;
						}
					}
					else if ($q['field'] == 'search')
					{
						$qs[$i] = '&' . $q['field'] . '=' . $q['value'];
					}
					else
					{
						$qs[$i] = '&q[' . $q['field'] . ']=' . $q['value'];
					}
				endforeach;
				?>
				<div id="applied-filters">
					<p><?php echo Lang::txt('COM_MEMBERS_BROWSE_FILTER_APPLIED'); ?>:</p>
					<ul class="filters-list">
						<?php if (!empty($this->filters['q'])) : ?>
							<?php foreach ($this->filters['q'] as $i => $q) : ?>
								<?php
								$route = $base;
								if (is_array($q['value']))
								{
									foreach ($q['value'] as $key => $val)
									{
										?>
										<li>
											<i><?php echo $q['human_field']; ?></i>: <?php echo $this->escape($val); ?>
											<a href="<?php echo Route::url($route); ?>" class="icon-remove filters-x" title="<?php echo Lang::txt('Remove filter'); ?>"><?php echo Lang::txt('Remove filter'); ?></a>
										</li>
										<?php
									}
								}
								else
								{
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
										<i><?php echo $q['human_field']; ?></i>: <?php echo $this->escape($q['human_value']); ?>
										<a href="<?php echo Route::url($route); ?>" class="icon-remove filters-x" title="<?php echo Lang::txt('Remove filter'); ?>"><?php echo Lang::txt('Remove filter'); ?></a>
									</li>
								<?php } ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<div class="container members-container">
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
							if ($messaging && $row->get('id') > 0 && $row->get('uidNumber') != User::get('id') && substr($row->get('email'), -8) != '@invalid')
							{
								switch ($this->config->get('user_messaging'))
								{
									case 1:
										// Get the groups of the profile
										$pgroups = Hubzero\User\Helper::getGroups($row->get('id'), 'all', 1); //$row->groups();
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
										<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $id); ?>">
											<img src="<?php echo $row->picture(); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_BROWSE_AVATAR', $this->escape($name)); ?>" />
										</a>
									</div>
									<div class="result-title">
										<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $id); ?>">
											<?php echo $name; ?>
										</a>
										<?php foreach ($this->fields as $c) { ?>
											<?php
											if (!in_array($c->get('name'), array('org', 'organization')))
											{
												continue;
											}

											if ($val = $row->get($c->get('name'))) { ?>
												<span class="result-details">
													<span class="<?php echo $this->escape($c->get('name')); ?>">
														<?php echo $this->escape(Hubzero\Utility\String::truncate(stripslashes($val), 60)); ?>
													</span>
												</span>
											<?php } ?>
										<?php } ?>
									</div>
									<div class="result-snippet">
										<?php foreach ($this->fields as $c) { ?>
											<?php
											if (in_array($c->get('name'), array('name', 'org', 'organization')))
											{
												continue;
											}

											if ($val = $row->get($c->get('name'))) {
												$val = (is_array($val) ? implode(', ', $val) : $val);
											?>
												<div class="result-snippet-<?php echo $this->escape($c->get('name')); ?>">
													<?php echo $this->escape(Hubzero\Utility\String::truncate(strip_tags(stripslashes($val)), 150)); ?>
												</div>
											<?php } ?>
										<?php } ?>
									</div>
									<div class="result-options">
										<?php if ($messageuser) { ?>
											<a class="icon-email btn message-member" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . User::get('id') . '&active=messages&task=new&to[]=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE_TO_TITLE', $this->escape($name)); ?>">
												<?php echo Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE'); ?>
											</a>
										<?php } ?>
										<a class="icon-user btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $row->get('id') . '&active=profile'); ?>" title="<?php echo Lang::txt('COM_MEMBERS_GO_TO_MEMBER_PROFILE', $this->escape($row->get('name'))); ?>">
											<?php echo Lang::txt('COM_MEMBERS_PROFILE'); ?>
										</a>
									</div>
									<?php if (!User::isGuest() && User::get('id') == $row->get('id')) { ?>
										<span class="you">
											<?php echo Lang::txt('COM_MEMBERS_BROWSE_YOUR_PROFILE'); ?>
										</span>
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
				if ($this->filters['sort'])
				{
					$pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
				}
				if ($this->filters['sort_Dir'])
				{
					$pageNav->setAdditionalUrlParam('sort_Dir', $this->filters['sort_Dir']);
				}
				if (!empty($this->filters['q']))
				{
					foreach ($this->filters['q'] as $i => $q)
					{
						$pageNav->setAdditionalUrlParam('q[' . $q['field'] . ']', $q['value']);
					}
				}
				echo $pageNav;
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
	</form>
</section><!-- / .main section -->
