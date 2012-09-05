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

ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

$juser =& JFactory::getUser();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<?php
	$path  = 'index.php?option=' . $this->option . '&task=feed.rss';
	$path .= ($this->filters['year']) ? '&year=' . $this->filters['year'] : '';
	$path .= ($this->filters['month']) ? '&month=' . $this->filters['month'] : '';
	$feed = JRoute::_($path);
	if (substr($feed, 0, 4) != 'http') 
	{
		$jconfig =& JFactory::getConfig();
		$live_site = rtrim(JURI::base(),'/');
		
		$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
	}
	$feed = str_replace('https:://','http://', $feed);
	?>
	<p><a class="feed btn" href="<?php echo $feed; ?>"><?php echo JText::_('RSS Feed'); ?></a></p>
</div>

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
		<div class="aside">
<?php if ($this->config->get('access-create-entry')) { ?>
			<p>
				<a class="add" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">
					<?php echo JText::_('New entry'); ?>
				</a>
			</p>
<?php } ?>
			<div class="container blog-entries-years">
				<h4><?php echo JText::_('Entries By Year'); ?></h4>
				<ol>
<?php 
			if ($this->firstentry) {
				$start = intval(substr($this->firstentry, 0, 4));
				$now = date("Y");
				for ($i=$now, $n=$start; $i >= $n; $i--)
				{
?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&year='.$i); ?>"><?php echo $i; ?></a>
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
							<li><a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&year=' . $i . '&month=' . sprintf("%02d", ($k+1), 1)); ?>"><?php echo JText::_($m[$k]); ?></a></li>
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
			<div class="container blog-popular-entries">
				<h4><?php echo JText::_('Popular Entries'); ?></h4>
<?php if ($this->popular) { ?>
				<ol>
<?php 
			foreach ($this->popular as $row)
			{
?>
					<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
			}
?>
				</ol>
<?php } else { ?>
				<p><?php echo JText::_('No entries found.'); ?></p>
<?php } ?>
			</div><!-- / .blog-popular-entries -->
			<div class="container blog-recent-entries">
				<h4><?php echo JText::_('Recent Entries'); ?></h4>
<?php if ($this->recent) { ?>
				<ol>
<?php 
			foreach ($this->recent as $row)
			{
?>
					<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
			}
?>
				</ol>
<?php } else { ?>
				<p><?php echo JText::_('No entries found.'); ?></p>
<?php } ?>
			</div><!-- / .blog-recent-entries -->
		</div><!-- / .aside -->
		<div class="subject">
<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<input type="text" name="search" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<h3>
<?php if (isset($this->filters['search']) && $this->filters['search']) { ?>
					<?php echo JText::sprintf('Search for "%s"', $this->filters['search']); ?>
<?php } else if (!isset($this->filters['year']) || !$this->filters['year']) { ?>
					<?php echo JText::_('Latest Entries'); ?>
<?php } else { 
			$format = '%b %Y';
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$format = 'M Y';
			}
					$archiveDate  = $this->filters['year'];
					$archiveDate .= ($this->filters['month']) ? '-' . $this->filters['month'] : '-01';
					$archiveDate .= '-01 00:00:00';
					echo JHTML::_('date', $archiveDate, $format, $this->tz);
	} ?>
				</h3>
<?php 
		if ($this->rows) {
?>
				<ol class="blog-entries">
<?php 
			$cls = 'even';
			foreach ($this->rows as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				$wikiconfig = array(
					'option'   => $this->option,
					'scope'    => 'blog',
					'pagename' => $row->alias,
					'pageid'   => 0,
					'filepath' => $this->config->get('uploadpath'),
					'domain'   => ''
				);
				$row->content = $p->parse(stripslashes($row->content), $wikiconfig);
				if ($this->config->get('cleanintro', 1)) {
					$row->content = Hubzero_View_Helper_Html::shortenText(stripslashes($row->content), $this->config->get('introlength', 300), 0, 1);
				} else {
					$row->content = Hubzero_View_Helper_Html::shortenText(stripslashes($row->content), $this->config->get('introlength', 300), 0, 0);
				}
				if (substr($row->content, -7) == '&#8230;') {
					$row->content .= '</p>';
				}
				switch ($row->state)
				{
					case 1:
						$state = 'public';
					break;
					case 2:
						$state = 'registered';;
					break;
					case 0:
					default:
						$state = 'private';
					break;
				}
?>

					<li class="<?php echo $cls; ?>" id="e<?php echo $row->id; ?>">
						<h4 class="entry-title">
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a>
<?php if ($juser->get('id') == $row->created_by) { ?>
							<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=edit&entry=' . $row->id); ?>" title="<?php echo JText::_('Edit'); ?>">
								<?php echo JText::_('Edit'); ?>
							</a>
							<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=delete&entry=' . $row->id); ?>" title="<?php echo JText::_('Delete'); ?>">
								<?php echo JText::_('Delete'); ?>
							</a>
<?php } ?>
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
<?php if ($this->config->get('show_authors')) { ?>
							<dd class="author">
								<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->created_by); ?>">
									<?php echo $this->escape(stripslashes($row->name)); ?>
								</a>
							</dd>
<?php } ?>
<?php if ($row->allow_comments == 1) { ?>
							<dd class="comments">
								<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=' . JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias . '#comments'); ?>">
									<?php echo JText::sprintf('COM_BLOG_NUM_COMMENTS', $row->comments); ?>
								</a>
							</dd>
<?php } else { ?>
							<dd class="comments">
								<span>
									<?php echo JText::_('COM_BLOG_COMMENTS_OFF'); ?>
								</span>
							</dd>
<?php } ?>
<?php if ($juser->get('id') == $row->created_by) { ?>
							<dd class="state <?php echo $state; ?>">
								<?php echo JText::_(strtoupper($this->option) . '_STATE_' . strtoupper($state)); ?>
							</dd>
<?php } ?>
						</dl>
						<div class="entry-content">

<?php if ($this->config->get('cleanintro', 1)) { ?>
							<p>
								<?php echo $row->content; ?> 
								<!-- <a class="readmore" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>" title="<?php echo JText::sprintf('COM_BLOG_READMORE', strip_tags(stripslashes($row->title))) ?>">
									Continue reading &rarr;
								</a> -->
							</p>
<?php } else { ?>
							<?php echo $row->content; ?> 
							<!-- <p>
								<a class="readmore" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>" title="<?php echo JText::sprintf('COM_BLOG_READMORE', strip_tags(stripslashes($row->title))) ?>">
									Continue reading &rarr;
								</a>
							</p> -->
<?php } ?>
						</div>
					</li>
<?php
			}
?>
				</ol>
<?php 
			$this->pageNav->setAdditionalUrlParam('year', $this->filters['year']);
			$this->pageNav->setAdditionalUrlParam('month', $this->filters['month']);
			$this->pageNav->setAdditionalUrlParam('search', $this->filters['search']);
			echo $this->pageNav->getListFooter();
		} else {
?>
				<p class="warning">No entries found.</p>
<?php
		}
?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->
