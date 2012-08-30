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
$live_site = rtrim(JURI::base(),'/');

?>

<?php if ($juser->get('id') == $this->member->get('uidNumber')) : ?>
<ul id="page_options">
	<li>
		<a class="add" href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task=new'); ?>">
			<?php echo JText::_('New entry'); ?>
		</a>
	</li>
	<li>
		<a class="config" href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task=settings'); ?>" title="<?php echo JText::_('Edit Settings'); ?>">
			<?php echo JText::_('Settings'); ?>
		</a>
	</li>
</ul>
<?php endif; ?>

<form method="get" action="<?php JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=blog'); ?>">
	<div class="aside">
	<?php if ($this->firstentry) : ?>
		<div class="container">
			<h4>Entries by Year</h4>
			<ul>
				<?php 
					$start = intval(substr($this->firstentry,0,4));
					$now = date("Y");
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
				?>
				<?php for ($i=$now, $n=$start; $i >= $n; $i--) : ?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task='.$i); ?>">
							<?php echo $i; ?>
						</a>
						<?php if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) : ?>
							<ul>
								<?php $months = ($i == $now) ? date("m") : 12; ?>
								<?php for ($k=0, $z=$months; $k < $z; $k++) : ?>
									<li>
										<a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->member->get('uidNumber').'&active=blog&task='.$i.'/'.sprintf( "%02d",($k+1),1)); ?>">
											<?php echo JText::_($m[$k]); ?>
										</a>
									</li>
								<?php endfor; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endfor; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<?php if ($this->popular) : ?>
		<div class="container">
			<h4><?php echo JText::_('Popular Entries'); ?></h4>
			<ul>
				<?php foreach ($this->popular as $row) : ?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<?php if ($this->recent) : ?>
		<div class="container">
			<h4><?php echo JText::_('Recent Entries'); ?></h4>
			<ul>
				<?php foreach ($this->recent as $row) : ?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	</div><!-- / .aside -->

	<div class="subject">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="Search" />
			<fieldset class="entry-search">
				<input type="text" name="search" value="<?php echo $this->escape($this->search); ?>" />
			</fieldset>
		</div><!-- / .container -->
	
		<div class="container">
			<h3>
<?php if (isset($this->search) && $this->search) { ?>
				<?php echo JText::sprintf('Search for "%s"', $this->escape($this->search)); ?>
<?php } else if (!isset($this->year) || !$this->year) { ?>
				<?php echo JText::_('Latest Entries'); ?>
<?php } else { 
		$format = '%b %Y';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$format = 'M Y';
		}
				$archiveDate  = $this->year;
				$archiveDate .= ($this->month) ? '-' . $this->month : '-01';
				$archiveDate .= '-01 00:00:00';
				echo JHTML::_('date', $archiveDate, $format, $this->tz);
} ?>
				<?php
					if ($this->config->get('feeds_enabled')) :
						$path  = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=blog&task=feed.rss';
						$path .= ($this->year)  ? '&year=' . $this->year   : '';
						$path .= ($this->month) ? '&month=' . $this->month : '';
						$feed = JRoute::_($path);
						if (substr($feed, 0, 4) != 'http') 
						{
							$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
						}
						$feed = str_replace('https:://', 'http://', $feed);
				?>
				<a class="feed" href="<?php echo $feed; ?>">
					<?php echo JText::_('RSS Feed'); ?>
				</a>
				<?php endif; ?>
			</h3>
	<?php if ($this->rows) { ?>
			<ol class="blog-entries">
<?php 
		$cls = 'even';
		foreach ($this->rows as $row)
		{
			$cls = ($cls == 'even') ? 'odd' : 'even';

			switch ($row->state)
			{
				case 1:
					$state = JText::_('Public');
					$cls = "public";
					break;
				case 2:
					$state = JText::_('Registered members');
					$cls = "registered";
					break;
				case 0:
				default:
					$state = JText::_('Private');
					$cls = "private";
					break;
			}
?>
			<li class="<?php echo $cls; ?>" id="e<?php echo $row->id; ?>">
				<h4 class="entry-title">
					<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias); ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				</h4>
				<dl class="entry-meta">
					<dt>
						<span>
							<?php echo JText::sprintf('Entry #%s', $row->id); ?>
						</span>
					</dt>
					<dd class="date">
						<time datetime="<?php echo $row->publish_up; ?>">
							<?php echo JHTML::_('date', $row->publish_up, $this->dateFormat, $this->tz); ?>
						</time>
					</dd>
					<dd class="time">
						<time datetime="<?php echo $row->publish_up; ?>">
							<?php echo JHTML::_('date', $row->publish_up, $this->timeFormat, $this->tz); ?>
						</time>
					</dd>
					<dd class="author">
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->created_by); ?>">
							<?php echo $this->escape(stripslashes($row->name)); ?>
						</a>
					</dd>
<?php if ($row->allow_comments == 1) { ?>
					<dd class="comments">
						<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias . '#comments'); ?>">
							<?php echo JText::sprintf('PLG_MEMBERS_BLOG_NUM_COMMENTS', $row->comments); ?>
						</a>
					</dd>
<?php } else { ?>
					<dd class="comments">
						<span>
							<?php echo JText::_('PLG_MEMBERS_BLOG_COMMENTS_OFF'); ?>
						</span>
					</dd>
<?php } ?>
<?php if ($juser->get('id') == $row->created_by) { ?>
					<dd class="state <?php echo $cls; ?>">
						<?php echo $state; ?>
					</dd>
<?php } ?>
					<dd class="entry-options">
<?php if ($juser->get('id') == $row->created_by) { ?>
						<a class="edit" href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task=edit&entry='.$row->id); ?>" title="<?php echo JText::_('Edit'); ?>">
							<?php echo JText::_('Edit'); ?>
						</a>
						<a class="delete" href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task=delete&entry='.$row->id); ?>" title="<?php echo JText::_('Delete'); ?>">
							<?php echo JText::_('Delete'); ?>
						</a>
<?php } ?>
					</dd>
				</dl>
				<div class="entry-content">
					<p>
						<?php 
						echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->content), 300, 0);
						?> 
					</p>
				</div>
			</li>
<?php } ?>
			</ol>
<?php } else { ?>
			<p>Currently there are no blog entries.</p>
<?php } ?>
			<?php echo $this->pagenavhtml; ?>
		</div>
	</div><!-- / .subject -->
</form>
