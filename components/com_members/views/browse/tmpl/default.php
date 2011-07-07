<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post">
		<div class="aside">
			<div class="container">
				<h3>Site Members</h3>
				<p class="starter"><span class="starter-point"></span>When people join this site and make their profiles public they will appear here.</p>
				<p>Use the sorting and filtering options to see members listed alphabetically, by their organization, or the number of contributions they have.</p>
				<p>Use the 'Search' to find specific members if you would like to check out their profiles, contributions or message them privately.</p>
			</div><!-- / .container -->
			
			<div class="container">
				<h3>Looking for groups?</h3>
				<p class="starter"><span class="starter-point"></span>Go to the <a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>">Groups page</a>.</p>
			</div><!-- / .container -->
		</div><!-- / .aside -->
		<div class="subject">
			
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend>Search for Members</legend>
					<label for="entry-search-field">Enter keyword or phrase</label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo htmlentities($this->filters['search'], ENT_COMPAT, 'UTF-8'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->filters['sortby']; ?>" />
					<input type="hidden" name="show" value="<?php echo $this->filters['show']; ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="index" value="<?php echo $this->filters['index']; ?>" />
				</fieldset>
			</div><!-- / .container -->
			
<?php 
$qs = array();
foreach ($this->filters as $f=>$v) 
{
	$qs[] = ($v != '' && $f != 'index' && $f != 'authorized' && $f != 'start') ? $f.'='.$v : '';
}
$qs[] = 'limitstart=0';
$qs = implode(a,$qs);

$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$url  = 'index.php?option='.$this->option;
$url .= ($qs != '') ? '&'.$qs : '';
$html  = '<a href="'.JRoute::_($url).'"';
if ($this->filters['index'] == '') {
	$html .= ' class="active-index"';
}
$html .= '>'.JText::_('ALL').'</a> '."\n";
foreach ($letters as $letter)
{
	$url  = 'index.php?option='.$this->option.'&index='.strtolower($letter);
	$url .= ($qs != '') ? '&'.$qs : '';
	
	$html .= "\t\t\t\t\t\t\t\t".'<a href="'.JRoute::_($url).'"';
	if ($this->filters['index'] == strtolower($letter)) {
		$html .= ' class="active-index"';
	}
	$html .= '>'.$letter.'</a> '."\n";
}
?>
			<div class="container">
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'name') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&show='.$this->filters['show'].'&sortby=name'); ?>" title="Sort by name">&darr; Name</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'organization') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&show='.$this->filters['show'].'&sortby=organization'); ?>" title="Sort by organization">&darr; Organization</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'contributions') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&show='.$this->filters['show'].'&sortby=contributions'); ?>" title="Sort by number of contributions">&darr; Contributions</a></li>
				</ul>
				
				<ul class="entries-menu filter-options">
					<li><a<?php echo ($this->filters['show'] != 'contributors') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&sortby='.$this->filters['sortby']); ?>" title="Show All members">All</a></li>
					<li><a<?php echo ($this->filters['show'] == 'contributors') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&index='.$this->filters['index'].'&show=contributors&sortby='.$this->filters['sortby']); ?>" title="Show only members with Contributions">Contributors</a></li>
				</ul>
				
				<table class="members entries" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
					<caption>
						<?php
						$s = $this->filters['start']+1;
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
						$e = ($this->filters['limit'] == 0) ? $this->total : $e;

						if ($this->filters['search'] != '') {
							echo 'Search for "'.$this->filters['search'].'" in ';
						}
						?>
						<?php if ($this->filters['show'] != 'contributors') {
							echo JText::_('All Members'); 
						} else {
							echo JText::_('Contributors'); 
						}?> 
						<?php if ($this->filters['index']) { ?>
							<?php echo JText::_('starting with'); ?> "<?php echo strToUpper($this->filters['index']); ?>"
						<?php } ?>
						<span>(<?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?>)</span>
					</caption>
					<thead>
						<tr>
							<th colspan="4">
								<?php echo $html; ?>
							</th>
						</tr>
					</thead>
					<tbody>
<?php
if (count($this->rows) > 0) {
	// Get plugins
	JPluginHelper::importPlugin( 'members' );
	$dispatcher =& JDispatcher::getInstance();
	
	$areas = array();
	$activeareas = $dispatcher->trigger( 'onMembersContributionsAreas', array($this->authorized) );
	foreach ($activeareas as $area) 
	{
		$areas = array_merge( $areas, $area );
	}
	
	$cols = 2;
	
	$cls = ''; //'even';

	// Default thumbnail
	$config =& JComponentHelper::getParams( 'com_members' );
	$thumb = $config->get('webpath');
	if (substr($thumb, 0, 1) != DS) {
		$thumb = DS.$thumb;
	}
	if (substr($thumb, -1, 1) == DS) {
		$thumb = substr($thumb, 0, (strlen($thumb) - 1));
	}
	$dfthumb = $config->get('defaultpic');
	if (substr($dfthumb, 0, 1) != DS) {
		$dfthumb = DS.$dfthumb;
	}
	$dfthumb = Hubzero_View_Helper_Html::thumbit($dfthumb);

	foreach ($this->rows as $row)
	{
		//$cls = ($cls == 'odd') ? 'even' : 'odd';
		$cls = '';
		if ($row->public != 1) {
			$cls = 'private';
		}
		
		if ($row->uidNumber < 0) {
			$id = 'n' . -$row->uidNumber;
		} else {
			$id = $row->uidNumber;
		}
		
		if ($row->uidNumber == $juser->get('id')) {
			$cls .= ($cls) ? ' me' : 'me';
		}
		
		// User name
		$row->name = stripslashes($row->name);
		$row->surname = stripslashes($row->surname);
		$row->givenName = stripslashes($row->givenName);
		$row->middelName = stripslashes($row->middleName);
		
		if (!$row->surname) {
			$bits = explode(' ', $row->name);
			$row->surname = array_pop($bits);
			if (count($bits) >= 1) {
				$row->givenName = array_shift($bits);
			}
			if (count($bits) >= 1) {
				$row->middleName = implode(' ',$bits);
			}
		}
		
		$name = ($row->surname) ? stripslashes($row->surname) : '';
		if ($row->givenName) {
			$name .= ($row->surname) ? ', ' : '';
			$name .= stripslashes($row->givenName);
			$name .= ($row->middleName) ? ' '.stripslashes($row->middleName) : '';
		}
		if (!trim($name)) {
			$name = 'Unknown ('.$row->username.')';
		}
		
		// User picture
		$uthumb = '';
		if ($row->picture) {
			$uthumb = $thumb.DS.Hubzero_View_Helper_Html::niceidformat($row->uidNumber).DS.$row->picture;
			$uthumb = Hubzero_View_Helper_Html::thumbit($uthumb);
		}

		if ($uthumb && is_file(JPATH_ROOT.$uthumb)) {
			$p = $uthumb;
		} else {
			$p = $dfthumb;
		}
?>
						<tr<?php echo ($cls) ? ' class="'.$cls.'"' : ''; ?>>
							<th class="entry-img">
								<img width="50" height="50" src="<?php echo $p; ?>" alt="Avatar for <?php echo htmlentities($name,ENT_COMPAT,'UTF-8'); ?>" />
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$id); ?>"><?php echo $name; ?></a><br />
								<span class="entry-details">
									<span class="organization"><?php echo Hubzero_View_Helper_Html::xhtml(stripslashes($row->organization)); ?></span>
								</span>
							</td>
							<td>
								<!-- rcount: <?php echo $row->rcount; ?> --> 
								<span class="activity"><?php echo $row->resource_count.' Resources, '.$row->wiki_count.' Topics'; ?></span>
							</td>
<?php /* ?>							<td class="message-member">
<?php if (!$juser->get('guest') && $row->uidNumber > 0 && $row->uidNumber != $juser->get('id')) { ?>
								<a class="message tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$juser->get('id').'&active=messages&task=new&to='.$row->uidNumber); ?>" title="Message :: Send a message to <?php echo htmlentities($name,ENT_COMPAT,'UTF-8'); ?>"><?php echo JText::_('Send a message to '.htmlentities($name,ENT_COMPAT,'UTF-8')); ?></a></td>
<?php } ?>
							</td><?php */ ?>
						</tr>
<?php
	}
} else { ?>
						<tr>
							<td colspan="4">
								<p class="warning"><?php echo JText::_('NO_MEMBERS_FOUND'); ?></p>
							</td>
						</tr>
<?php } ?>
					</tbody>
				</table>
<?php
	$pn = $this->pageNav->getListFooter();
	$pn = str_replace('/?/&amp;','/?',$pn);
	$f = '';
	foreach ($this->filters as $k=>$v) 
	{
		$f .= ($v && $k != 'authorized' && $k != 'limit' && $k != 'start') ? $k.'='.$v.'&amp;' : '';
	}
	$pn = str_replace('?','?'.$f,$pn);
	$pn = str_replace('&amp;&amp;','&amp;',$pn);
	echo $pn;
?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->
