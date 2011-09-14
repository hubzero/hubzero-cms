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
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<?php
	$path  = 'index.php?option='.$this->option.'&task=feed.rss';
	$path .= ($this->filters['year']) ? '&year='.$this->filters['year'] : '';
	$path .= ($this->filters['month']) ? '&month='.$this->filters['month'] : '';
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
	<p><a class="feed" href="<?php echo $feed; ?>"><?php echo JText::_('Blog RSS Feed'); ?></a></p>
</div>

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" method="get">
		<div class="aside">
<?php if ($this->authorized) { ?>
			<p><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>"><?php echo JText::_('New entry'); ?></a></p>
<?php } ?>
			<fieldset>
				<legend>Search</legend>
				<label>
					Search Entries
					<input type="text" name="search" value="<?php echo htmlentities(utf8_encode(stripslashes($this->filters['search'])),ENT_COMPAT,'UTF-8'); ?>" />
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
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i); ?>"><?php echo $i; ?></a>
<?php
					if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) {
?>
						<ol>
<?php
						$m = array(
							'COM_BLOG_JANUARY',
							'COM_BLOG_FEBRUARY',
							'COM_BLOG_MARCH',
							'COM_BLOG_APRIL',
							'COM_BLOG_MAY',
							'COM_BLOG_JUNE',
							'COM_BLOG_JULY',
							'COM_BLOG_AUGUST',
							'COM_BLOG_SEPTEMBER',
							'COM_BLOG_OCTOBER',
							'COM_BLOG_NOVEMBER',
							'COM_BLOG_DECEMBER'
						);
						//if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) {
						if ($i == $now) {
							$months = date("m");
						} else {
							$months = 12;
						}

						for ($k=0, $z=$months; $k < $z; $k++)
						{
?>
							<li><a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month='.sprintf( "%02d",($k+1),1)); ?>"><?php echo JText::_($m[$k]); ?></a></li>
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
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
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
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
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
<?php 
		if ($this->rows) {
?>
				<ol class="blog-entries">
<?php 
			$cls = 'even';
			foreach ($this->rows as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				//$user =& JUser::getInstance($row->created_by);
?>

					<li class="entry <?php echo $cls; ?>" id="e<?php echo $row->id; ?>">
						<dl class="entry-meta">
							<dt class="date"><?php echo JHTML::_('date',$row->publish_up, '%d %b, %Y', 0); ?></dt>
							<dd class="time"><?php echo JHTML::_('date',$row->publish_up, '%I:%M %p', 0); ?></dd>
<?php if ($row->allow_comments == 1) { ?>
							<dd class="comments"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias.'#comments'); ?>"><?php echo JText::sprintf('COM_BLOG_NUM_COMMENTS', $row->comments); ?></a></dd>
<?php } else { ?>
							<dd class="comments"><?php echo JText::_('COM_BLOG_COMMENTS_OFF'); ?></dd>
<?php } ?>
						</dl>
						<h4 class="entry-title">
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a>
<?php if ($juser->get('id') == $row->created_by) { ?>
							<span class="state"><?php 
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
?></span>
							<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=edit&entry='.$row->id); ?>" title="<?php echo JText::_('Edit'); ?>"><?php echo JText::_('Edit'); ?></a>
							<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete&entry='.$row->id); ?>" title="<?php echo JText::_('Delete'); ?>"><?php echo JText::_('Delete'); ?></a>
<?php } ?>
						</h4>
						<div class="entry-content">
<?php if ($this->config->get('show_authors')) { ?>
							<p class="entry-author">Posted by <cite><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo stripslashes($row->name); ?></a></cite></p>
<?php } ?>
							<p><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->content), 300, 0); ?> <a class="readmore" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>" title="<?php echo JText::sprintf('COM_BLOG_READMORE', strip_tags(stripslashes($row->title))) ?>">Continue reading &rarr;</a></p>
						</div>
					</li>
<?php
			}
?>
				</ol>
<?php 
			$pagenavhtml = $this->pageNav->getListFooter();
			$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
			$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
			echo $pagenavhtml;
		} else {
?>
			<p class="warning">No entries found.</p>
<?php
		}
?>
		</div><!-- / .subject -->
	</form>
</div><!-- / .main section -->
