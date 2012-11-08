<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as state by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$maxtextlen = 42;
$juser =& JFactory::getUser();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>"><?php echo JText::_('Create Course'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<?php
	foreach ($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>" method="get">
	<div class="main section">
		<div class="aside">
			<div class="container">
				<h3>Finding a course</h3>
				<p>Use the sorting and filtering options to see courses listed alphabetically by their title, by their alias, or active state.</p>
				<p>Use the 'Search' to find specific courses if you would like to check out their offerings.</p>
			</div><!-- / .container -->
		</div><!-- / .aside -->
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend>Search for Courses</legend>
					<label for="entry-search-field">Enter keyword or phrase</label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->filters['sortby']; ?>" />
					<input type="hidden" name="policy" value="<?php echo $this->escape($this->filters['policy']); ?>" />
					<!-- <input type="hidden" name="option" value="<?php echo $this->option; ?>" /> -->
					<input type="hidden" name="index" value="<?php echo $this->filters['index']; ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&policy='.$this->filters['policy'].'&sortby=title'); ?>" title="Sort by title">&darr; Title</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'alias') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&policy='.$this->filters['policy'].'&sortby=alias'); ?>" title="Sort by alias">&darr; Alias</a></li>
				</ul>
				
				<ul class="entries-menu filter-options">
					<li><a<?php echo ($this->filters['policy'] == '') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&sortby='.$this->filters['sortby']); ?>" title="Show All courses">All</a></li>
					<li><a<?php echo ($this->filters['policy'] == 'open') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&policy=open&sortby='.$this->filters['sortby']); ?>" title="Show courses with an Open join policy">Open</a></li>
					<li><a<?php echo ($this->filters['policy'] == 'restricted') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&policy=restricted&sortby='.$this->filters['sortby']); ?>" title="Show courses with a Restricted join policy">Restricted</a></li>
					<li><a<?php echo ($this->filters['policy'] == 'invite') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&policy=invite&sortby='.$this->filters['sortby']); ?>" title="Show courses with an Invite only join policy">Invite only</a></li>
					<li><a<?php echo ($this->filters['policy'] == 'closed') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&policy=closed&sortby='.$this->filters['sortby']); ?>" title="Show courses with a Closed join policy">Closed</a></li>
				</ul>

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

$html  = '<a href="'.JRoute::_($url).'"';
if ($this->filters['index'] == '') {
	$html .= ' class="active-index"';
}
$html .= '>'.JText::_('ALL').'</a> '."\n";

$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
foreach ($letters as $letter)
{
	$url  = 'index.php?option='.$this->option.'&task=browse&index='.strtolower($letter);
	$url .= ($qs) ? '&'.$qs : '';

	$html .= "\t\t\t\t\t\t\t\t".'<a href="'.JRoute::_($url).'"';
	if ($this->filters['index'] == strtolower($letter)) {
		$html .= ' class="active-index"';
	}
	$html .= '>'.$letter.'</a> '."\n";
}
?>
				<div class="clearfix"></div>

				<table class="courses entries" summary="<?php echo JText::_('COURSES_BROWSE_TBL_SUMMARY'); ?>">
					<caption>
<?php
						$s = $this->filters['start']+1;
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '') {
							echo 'Search for "'.$this->filters['search'].'" in ';
						}
?>
						<?php echo JText::_('Courses'); ?> 
<?php if ($this->filters['index']) { ?>
							<?php echo JText::_('starting with'); ?> "<?php echo strToUpper($this->filters['index']); ?>"
<?php } ?>
<?php if ($this->courses) { ?>
						<span>(<?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?>)</span>
<?php } ?>
					</caption>
					<thead>
						<tr>
							<th colspan="<?php echo ($this->config->get('access-admin-component')) ? '4' : '3'; ?>">
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
if ($this->courses) 
{
	foreach ($this->courses as $course)
	{
		//
		//$g = CoursesCourse::getInstance($course->id);
		//$invitees = $course->get('invitees');
		//$applicants = $g->get('applicants');
		//$members = $g->get('members');
		//$managers = $course->get('managers');
		
		//get status
		$status = '';
		
		//determine course status
		if ($course->get('state') == 1)
		{
			if ($course->access('manage'))
			{
				$status = 'manager';
			}
		}
		else
		{
			$status = 'new';
		}
?>
						<tr<?php echo ($status) ? ' class="'.$status.'"' : ''; ?>>
							<th>
								<span class="entry-id"><?php echo $course->get('id'); ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$course->get('alias')); ?>"><?php echo $this->escape(stripslashes($course->get('title'))); ?></a><br />
								<span class="entry-details">
									<span class="entry-alias"><?php echo $course->get('alias'); ?></span>
								</span>
							</td>
							<td>
								<?php
								/*switch ($course->join_policy)
								{
									case 3: echo '<span class="closed join-policy">'.JText::_('Closed').'</span>'."\n"; break;
									case 2: echo '<span class="inviteonly join-policy">'.JText::_('Invite Only').'</span>'."\n"; break;
									case 1: echo '<span class="restricted join-policy">'.JText::_('Restricted').'</span>'."\n";  break;
									case 0:
									default: echo '<span class="open join-policy">'.JText::_('Open').'</span>'."\n"; break;
								}*/
?>
							</td>
<?php if ($this->config->get('access-admin-component')) { ?>
							<td>
								<span class="<?php echo $status; ?> status"><?php
									switch ($status)
									{
										case 'manager': echo JText::_('COM_COURSES_STATUS_MANAGER'); break;
										case 'new': echo JText::_('COM_COURSES_STATUS_NEW_COURSE'); break;
										case 'member': echo JText::_('COM_COURSES_STATUS_APPROVED'); break;
										case 'pending': echo JText::_('COM_COURSES_STATUS_PENDING'); break;
										case 'invitee': echo JText::_('COM_COURSES_STATUS_INVITED'); break;
										default: break;
									}
								?></span>
							</td>
<?php } ?>
						</tr>
<?php 
	} // for loop 
} else {
?>
						<tr>
							<td colspan="<?php echo ($this->authorized) ? '4' : '3'; ?>">
								<p class="warning"><?php echo JText::_('No results found'); ?></p>
							</td>
						</tr>
<?php } ?>
					</tbody>
				</table>
<?php
$this->pageNav->setAdditionalUrlParam('index', $this->filters['index']);
$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
$this->pageNav->setAdditionalUrlParam('policy', $this->filters['policy']);

echo $this->pageNav->getListFooter();
?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
	</div><!-- / .main section -->
	<div class="clear"></div>
</form>
