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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=blog&task=browse'); ?>" method="get">
<h3><a name="blog"></a><?php echo JText::_('PLG_MEMBERS_BLOG'); ?></h3>
<div class="aside">
<?php
	if ($this->config->get('feeds_enabled')) {
		$path  = 'index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=blog&task=feed.rss';
		$path .= ($this->year) ? '&year='.$this->year : '';
		$path .= ($this->month) ? '&month='.$this->month : '';
		$feed = JRoute::_($path);
		if (substr($feed, 0, 4) != 'http') {
			if (substr($feed, 0, 1) != DS) {
				$feed = DS.$feed;
			}
			$jconfig =& JFactory::getConfig();
			$feed = $jconfig->getValue('config.live_site').$feed;
		}
		$feed = str_replace('https:://','http://',$feed);
?>
	<p><a class="feed" href="<?php echo $feed; ?>"><?php echo JText::_('RSS Feed'); ?></a></p>
<?php } ?>
<?php if ($juser->get('id') == $this->member->get('uidNumber')) { ?>
	<p><a class="add" href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task=new'); ?>"><?php echo JText::_('New entry'); ?></a></p>
	<p><a class="config" href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task=settings'); ?>" title="<?php echo JText::_('Edit Settings'); ?>"><?php echo JText::_('Settings'); ?></a></p>
<?php } ?>
	<fieldset>
		<legend>Search</legend>
		<label>
			Search Entries
			<input type="text" name="search" value="<?php echo htmlentities(utf8_encode(stripslashes($this->search)),ENT_COMPAT,'UTF-8'); ?>" />
		</label>
		<input type="submit" name="go" value="Go" />
	</fieldset>
	<div class="blog-entries-years">
		<h4><?php echo JText::_('Entries By Year'); ?></h4>
		<ol>
<?php 
	if ($this->firstentry) {
		$start = intval(substr($this->firstentry,0,4));
		$now = date("Y");
		for ($i=$now, $n=$start; $i >= $n; $i--)
		{
?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task='.$i); ?>"><?php echo $i; ?></a>
<?php
			if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) {
?>
				<ol>
<?php
						$m = array(
							'PLG_MEMBERS_BLOG_JANUARY',
							'PLG_MEMBERS_BLOG_FEBRUARY',
							'PLG_MEMBERS_BLOG_MARCH',
							'PLG_MEMBERS_BLOG_APRIL',
							'PLG_MEMBERS_BLOG_MAY',
							'PLG_MEMBERS_BLOG_JUNE',
							'PLG_MEMBERS_BLOG_JULY',
							'PLG_MEMBERS_BLOG_AUGUST',
							'PLG_MEMBERS_BLOG_SEPTEMBER',
							'PLG_MEMBERS_BLOG_OCTOBER',
							'PLG_MEMBERS_BLOG_NOVEMBER',
							'PLG_MEMBERS_BLOG_DECEMBER'
						);
						if ($i == $now) {
							$months = date("m");
						} else {
							$months = 12;
						}

						for ($k=0, $z=$months; $k < $z; $k++)
						{
?>
							<li><a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task='.$i.'/'.sprintf( "%02d",($k+1),1)); ?>"><?php echo JText::_($m[$k]); ?></a></li>
<?php
						}
?>
				</ol>
<?php
			}
?>
			</li>
<?php 
		}
	}
?>
		</ol>
	</div><!-- / .blog-entries-years -->
	<div class="blog-popular-entries">
		<h4><?php echo JText::_('Popular Entries'); ?></h4>
		<ol>
<?php 
if ($this->popular) {
	foreach ($this->popular as $row)
	{
?>
			<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
	}
}
?>
		</ol>
	</div><!-- / .blog-popular-entries -->
	<div class="blog-recent-entries">
		<h4><?php echo JText::_('Recent Entries'); ?></h4>
		<ol>
<?php 
if ($this->recent) {
	foreach ($this->recent as $row)
	{
?>
			<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
	}
}
?>
		</ol>
	</div><!-- / .blog-recent-entries -->
</div><!-- / .aside -->
<div class="subject">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<ol class="blog-entries">
<?php 
if ($this->rows) {
	ximport('Hubzero_View_Helper_Html');
	$cls = 'even';
	foreach ($this->rows as $row)
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';
		switch ($row->state)
		{
			case 1:
				$cls .= ' public';
			break;
			case 2:
				$cls .= ' registered';
			break;
			case 0:
			default:
				$cls .= ' private';
			break;
		}
?>

			<li class="entry <?php echo $cls; ?>" id="e<?php echo $row->id; ?>">
				<dl class="entry-meta">
					<dt class="date"><?php echo JHTML::_('date',$row->publish_up, '%d %b, %Y', 0); ?></dt>
					<dd class="time"><?php echo JHTML::_('date',$row->publish_up, '%I:%M %p', 0); ?></dd>
<?php if ($row->allow_comments == 1) { ?>
					<dd class="comments"><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias.'#comments'); ?>"><?php echo JText::sprintf('PLG_MEMBERS_BLOG_NUM_COMMENTS', $row->comments); ?></a></dd>
<?php } else { ?>
					<dd class="comments"><?php echo JText::_('PLG_MEMBERS_BLOG_COMMENTS_OFF'); ?></dd>
<?php } ?>
<?php if ($juser->get('id') == $row->created_by) { ?>
					<dd class="state"><?php 
switch ($row->state)
{
	case 1:
		echo JText::_('Public');
	break;
	case 2:
		echo JText::_('Registered members');
	break;
	case 0:
	default:
		echo JText::_('Private');
	break;
}
?></dd>
<?php } ?>
				</dl>
				<h4 class="entry-title">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a>
<?php if ($juser->get('id') == $row->created_by) { ?>
					<a class="edit" href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task=edit&entry='.$row->id); ?>" title="<?php echo JText::_('Edit'); ?>"><?php echo JText::_('Edit'); ?></a>
					<a class="delete" href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task=delete&entry='.$row->id); ?>" title="<?php echo JText::_('Delete'); ?>"><?php echo JText::_('Delete'); ?></a>
<?php } ?>
				</h4>
				<div class="entry-content">
					<p><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->content), 300, 0); ?> <a class="readmore" href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>" title="<?php echo JText::sprintf('PLG_MEMBERS_BLOG_READMORE', strip_tags(stripslashes($row->title))) ?>">Continue reading &rarr;</a></p>
				</div>
			</li>
<?php
	}
}
?>
		</ol>
		<?php echo $this->pagenavhtml; ?>
</div><!-- / .subject -->
</form>
