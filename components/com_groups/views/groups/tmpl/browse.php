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
 * the GNU Lesser General Public License as published by the Free Software
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>">
				<?php echo JText::_('COM_GROUPS_NEW'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<?php
	foreach ($this->notifications as $notification) 
	{
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" method="get">
	<div class="main section">
		<div class="aside">
			<div class="container">
				<h3><?php echo JText::_('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_TITLE'); ?></h3>
				<p class="starter">
					<span class="starter-point"></span>
					<?php echo JText::_('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_ONE'); ?>
				</p>
				<p><?php echo JText::_('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_TWO'); ?></p>
				<p><?php echo JText::_('COM_GROUPS_BROWSE_ASIDE_SECTION_ONE_DEATAILS_THREE'); ?></p>
			</div><!-- / .container -->
			
			<div class="container">
				<h3><?php echo JText::_('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_TITLE'); ?></h3>
				<p class="starter">
					<span class="starter-point"></span>
					<?php echo JText::sprintf('COM_GROUPS_BROWSE_ASIDE_SECTION_TWO_DEATAILS', JRoute::_('index.php?option=com_members')); ?>
				</p>
			</div><!-- / .container -->
		</div><!-- / .aside -->
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('COM_GROUPS_BROWSE_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('COM_GROUPS_BROWSE_SEARCH_HELP'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_GROUPS_BROWSE_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->filters['sortby']; ?>" />
					<input type="hidden" name="policy" value="<?php echo $this->escape($this->filters['policy']); ?>" />
					<!-- <input type="hidden" name="option" value="<?php echo $this->option; ?>" /> -->
					<input type="hidden" name="index" value="<?php echo $this->filters['index']; ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<?php
					$fltrs  = ($this->filters['index'])  ? '&index=' . $this->filters['index']   : '';
					$fltrs .= ($this->filters['policy']) ? '&policy=' . $this->filters['policy'] : '';
					$fltrs .= ($this->filters['search']) ? '&search=' . $this->filters['search'] : '';
				?>
				<ul class="entries-menu order-options">
					<li><a class="sort-title<?php echo ($this->filters['sortby'] == 'title') ? ' active' : ''; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=title' . $fltrs); ?>" title="Sort by title">&darr; Title</a></li>
					<li><a class="sort-alias<?php echo ($this->filters['sortby'] == 'alias') ? ' active' : ''; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=alias' . $fltrs); ?>" title="Sort by alias">&darr; Alias</a></li>
				</ul>
				<?php
				$fltrs  = ($this->filters['index'])  ? '&index=' . $this->filters['index']   : '';
				$fltrs .= ($this->filters['sortby']) ? '&sortby=' . $this->filters['sortby'] : '';
				$fltrs .= ($this->filters['search']) ? '&search=' . $this->filters['search'] : '';
				?>
				<ul class="entries-menu filter-options">
					<li><a class="filter-all<?php echo ($this->filters['policy'] == '') ? ' active' : ''; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse' . $fltrs); ?>" title="Show All groups">All</a></li>
					<li><a class="filter-open<?php echo ($this->filters['policy'] == 'open') ? ' active' : ''; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&policy=open' . $fltrs); ?>" title="Show groups with an Open join policy">Open</a></li>
					<li><a class="filter-restricted<?php echo ($this->filters['policy'] == 'restricted') ? ' active' : ''; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&policy=restricted' . $fltrs); ?>" title="Show groups with a Restricted join policy">Restricted</a></li>
					<li><a class="filter-invite<?php echo ($this->filters['policy'] == 'invite') ? ' active' : ''; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&policy=invite' . $fltrs); ?>" title="Show groups with an Invite only join policy">Invite only</a></li>
					<li><a class="filter-closed<?php echo ($this->filters['policy'] == 'closed') ? ' active' : ''; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&policy=closed' . $fltrs); ?>" title="Show groups with a Closed join policy">Closed</a></li>
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

				<table class="groups entries" summary="<?php echo JText::_('GROUPS_BROWSE_TBL_SUMMARY'); ?>">
					<caption>
<?php
						$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '') {
							echo 'Search for "'.$this->filters['search'].'" in ';
						}
?>
						<?php echo JText::_('Groups'); ?> 
<?php if ($this->filters['index']) { ?>
							<?php echo JText::_('starting with'); ?> "<?php echo strToUpper($this->filters['index']); ?>"
<?php } ?>
<?php if ($this->groups) { ?>
						<span>(<?php echo $s . '-' . $e; ?> of <?php echo $this->total; ?>)</span>
<?php } ?>
					</caption>
					<thead>
						<tr>
							<th colspan="<?php echo ($this->authorized) ? '4' : '3'; ?>">
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
		$g = Hubzero_Group::getInstance($group->gidNumber);
		$invitees = $g->get('invitees');
		$applicants = $g->get('applicants');
		$members = $g->get('members');
		$managers = $g->get('managers');
		
		//get status
		$status = '';
		
		//determine group status
		if($g->get('published') && in_array($this->juser->get('id'), $managers))
		{
			$status = 'manager';
		}
		elseif($g->get('published') && in_array($this->juser->get('id'), $members))
		{
			$status = 'member';
		}
		elseif($g->get('published') && in_array($this->juser->get('id'), $invitees))
		{
			$status = 'invitee';
		}
		elseif($g->get('published') && in_array($this->juser->get('id'), $applicants))
		{
			$status = 'pending';
		}
		else
		{
			if(!$g->get('published'))
			{
				$status = 'new';
			}
		}
?>
						<tr<?php echo ($status) ? ' class="'.$status.'"' : ''; ?>>
							<th>
								<span class="entry-id"><?php echo $group->gidNumber; ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$group->cn); ?>"><?php echo stripslashes($group->description); ?></a><br />
								<span class="entry-details">
									<span class="entry-alias"><?php echo $group->cn; ?></span>
								</span>
							</td>
							<td>
								<?php
								switch ($group->join_policy)
								{
									case 3: echo '<span class="closed join-policy">'.JText::_('Closed').'</span>'."\n"; break;
									case 2: echo '<span class="inviteonly join-policy">'.JText::_('Invite Only').'</span>'."\n"; break;
									case 1: echo '<span class="restricted join-policy">'.JText::_('Restricted').'</span>'."\n";  break;
									case 0:
									default: echo '<span class="open join-policy">'.JText::_('Open').'</span>'."\n"; break;
								}
?>
							</td>
<?php if ($this->authorized) { ?>
							<td>
								<span class="<?php echo $status; ?> status"><?php
									switch ($status)
									{
										case 'manager': echo JText::_('GROUPS_STATUS_MANAGER'); break;
										case 'new': echo JText::_('GROUPS_STATUS_NEW_GROUP'); break;
										case 'member': echo JText::_('GROUPS_STATUS_APPROVED'); break;
										case 'pending': echo JText::_('GROUPS_STATUS_PENDING'); break;
										case 'invitee': echo JText::_('GROUPS_STATUS_INVITED'); break;
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
$this->pageNav->setAdditionalUrlParam('search', $this->filters['search']);

echo $this->pageNav->getListFooter();
?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
	</div><!-- / .main section -->
	<div class="clear"></div>
</form>