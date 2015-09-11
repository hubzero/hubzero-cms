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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('browse');
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if (User::authorise('core.create', $this->option)) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=new'); ?>">
					<?php echo Lang::txt('COM_GROUPS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<?php
	foreach ($this->notifications as $notification)
	{
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<form action="<?php echo Route::url('index.php?option='.$this->option.'&task=browse'); ?>" method="get">
	<section class="main section">
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_HELP'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="policy" value="<?php echo $this->escape($this->filters['policy']); ?>" />
					<input type="hidden" name="index" value="<?php echo $this->escape($this->filters['index']); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<nav class="entries-filters">
					<?php
						$fltrs  = ($this->filters['index'])  ? '&index=' . $this->filters['index']   : '';
						$fltrs .= ($this->filters['policy']) ? '&policy=' . $this->filters['policy'] : '';
						$fltrs .= ($this->filters['search']) ? '&search=' . $this->filters['search'] : '';
					?>
					<ul class="entries-menu order-options" data-label="<?php echo Lang::txt('COM_GROUPS_BROWSE_SORT'); ?>">
						<li><a class="sort-title<?php echo ($this->filters['sortby'] == 'title') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&sortby=title' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SORT_BY_TITLE'); ?>"><?php echo Lang::txt('COM_GROUPS_GROUP_TITLE'); ?></a></li>
						<li><a class="sort-alias<?php echo ($this->filters['sortby'] == 'alias') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&sortby=alias' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SORT_BY_ALIAS'); ?>"><?php echo Lang::txt('COM_GROUPS_GROUP_ALIAS'); ?></a></li>
					</ul>
					<?php
					$fltrs  = ($this->filters['index'])  ? '&index=' . $this->filters['index']   : '';
					$fltrs .= ($this->filters['sortby']) ? '&sortby=' . $this->filters['sortby'] : '';
					$fltrs .= ($this->filters['search']) ? '&search=' . $this->filters['search'] : '';
					?>
					<ul class="entries-menu filter-options" data-label="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW'); ?>">
						<li><a class="filter-all<?php echo ($this->filters['policy'] == '') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_ALL'); ?>"><?php echo Lang::txt('JALL'); ?></a></li>
						<li><a class="filter-open<?php echo ($this->filters['policy'] == 'open') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=open' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN'); ?></a></li>
						<li><a class="filter-restricted<?php echo ($this->filters['policy'] == 'restricted') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=restricted' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED'); ?></a></li>
						<li><a class="filter-invite<?php echo ($this->filters['policy'] == 'invite') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=invite' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY'); ?></a></li>
						<li><a class="filter-closed<?php echo ($this->filters['policy'] == 'closed') ? ' active' : ''; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&policy=closed' . $fltrs); ?>" title="<?php echo Lang::txt('COM_GROUPS_BROWSE_SHOW_WITH_POLICY', Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED')); ?>"><?php echo Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED'); ?></a></li>
					</ul>
				</nav>

				<?php
				$qs = array();
				foreach ($this->filters as $f=>$v)
				{
					$qs[] = ($v != '' && $f != 'index' && $f != 'authorized' && $f != 'type' && $f != 'fields') ? $f.'='.$v : '';
				}
				$qs[] = 'limitstart=0';
				$qs = implode('&amp;',$qs);

				$url  = 'index.php?option='.$this->option.'&task=browse';
				$url .= ($qs) ? '&'.$qs : '';

				$html  = '<a href="'.Route::url($url).'"';
				if ($this->filters['index'] == '')
				{
					$html .= ' class="active-index"';
				}
				$html .= '>'.Lang::txt('JALL').'</a> '."\n";

				$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
				foreach ($letters as $letter)
				{
					$url  = 'index.php?option='.$this->option.'&task=browse&index='.strtolower($letter);
					$url .= ($qs) ? '&'.$qs : '';

					$html .= "\t\t\t\t\t\t\t\t".'<a href="'.Route::url($url).'"';
					if ($this->filters['index'] == strtolower($letter))
					{
						$html .= ' class="active-index"';
					}
					$html .= '>'.$letter.'</a> '."\n";
				}
				?>
				<div class="clearfix"></div>

				<table class="groups entries">
					<caption>
						<?php
						$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '')
						{
							echo Lang::txt('COM_GROUPS_BROWSE_SEARCH_FOR_IN', $this->filters['search']);
						}
						else
						{
							echo Lang::txt('COM_GROUPS');
						}
						?>
						<?php if ($this->filters['index']) { ?>
							<?php echo Lang::txt('COM_GROUPS_STARTING_WITH'); ?> "<?php echo strToUpper($this->filters['index']); ?>"
						<?php } ?>
						<?php if ($this->groups) { ?>
							<span>(<?php echo Lang::txt('COM_GROUPS_BROWSE_RESULTS_OF', $s, $e, $this->total); ?>)</span>
						<?php } ?>
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
					if ($this->groups) {
						foreach ($this->groups as $group)
						{
							//
							$g = \Hubzero\User\Group::getInstance($group->gidNumber);
							$invitees = $g->get('invitees');
							$applicants = $g->get('applicants');
							$members = $g->get('members');
							$managers = $g->get('managers');

							//get status
							$status = '';

							//determine group status
							if ($g->get('published') && in_array(User::get('id'), $managers))
							{
								$status = 'manager';
							}
							elseif ($g->get('published') && in_array(User::get('id'), $members))
							{
								$status = 'member';
							}
							elseif ($g->get('published') && in_array(User::get('id'), $invitees))
							{
								$status = 'invitee';
							}
							elseif ($g->get('published') && in_array(User::get('id'), $applicants))
							{
								$status = 'pending';
							}
							else
							{
								if (!$g->get('published'))
								{
									$status = 'new';
								}
							}
					?>
						<tr<?php echo ($status) ? ' class="'.$status.'"' : ''; ?>>
							<th class="priority-4">
								<span class="entry-id"><?php echo $group->gidNumber; ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$group->cn); ?>"><?php echo stripslashes($group->description); ?></a><br />
								<span class="entry-details">
									<span class="entry-alias"><?php echo $group->cn; ?></span>
								</span>
							</td>
							<td class="priority-2">
								<?php
								switch ($group->join_policy)
								{
									case 3: echo '<span class="closed join-policy">'.Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED').'</span>'."\n"; break;
									case 2: echo '<span class="inviteonly join-policy">'.Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY').'</span>'."\n"; break;
									case 1: echo '<span class="restricted join-policy">'.Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED').'</span>'."\n";  break;
									case 0:
									default: echo '<span class="open join-policy">'.Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN').'</span>'."\n"; break;
								}
								?>
							</td>
							<td class="priority-3">
								<span class="<?php echo $status; ?> status"><?php
									switch ($status)
									{
										case 'manager': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_MANAGER'); break;
										case 'new': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_NEW'); break;
										case 'member': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_MEMBER'); break;
										case 'pending': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_PENDING'); break;
										case 'invitee': echo Lang::txt('COM_GROUPS_BROWSE_STATUS_INVITED'); break;
										default: break;
									}
								?></span>
							</td>
						</tr>
					<?php
						} // for loop
					} else {
					?>
						<tr>
							<td colspan="<?php echo ($this->authorized) ? '4' : '3'; ?>">
								<p class="warning"><?php echo Lang::txt('COM_GROUPS_BROWSE_NO_GROUPS'); ?></p>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php
				// Initiate paging
				$pageNav = $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('index', $this->filters['index']);
				$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
				$pageNav->setAdditionalUrlParam('policy', $this->filters['policy']);
				$pageNav->setAdditionalUrlParam('search', $this->filters['search']);

				echo $pageNav->render();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_TITLE'); ?></h3>
				<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_ONE'); ?></p>
				<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_TWO'); ?></p>
				<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_THREE'); ?></p>
			</div><!-- / .container -->

			<div class="container">
				<h3><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_TITLE'); ?></h3>
				<p><?php echo Lang::txt('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_DEATAILS', Route::url('index.php?option=com_members')); ?></p>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</section><!-- / .main section -->
</form>