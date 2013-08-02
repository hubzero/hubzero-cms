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

$first = $this->model->entries('first');
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
	<p><a class="icon-feed feed btn" href="<?php echo $feed; ?>"><?php echo JText::_('COM_BLOG_FEED'); ?></a></p>
</div>

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
		<div class="aside">
		<?php if ($this->config->get('access-create-entry')) { ?>
			<p>
				<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">
					<?php echo JText::_('COM_BLOG_NEW_ENTRY'); ?>
				</a>
			</p>
		<?php } ?>

			<div class="container blog-entries-years">
				<h4><?php echo JText::_('COM_BLOG_ENTRIES_BY_YEAR'); ?></h4>
				<ol>
				<?php 
			if ($first->exists()) {
				$start = intval(substr($first->get('publish_up'), 0, 4));
				$now = date("Y");
				for ($i=$now, $n=$start; $i >= $n; $i--)
				{
				?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&year='.$i); ?>"><?php echo $i; ?></a>
					<?php if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) { ?>
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
							<li>
								<a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&year=' . $i . '&month=' . sprintf("%02d", ($k+1), 1)); ?>"><?php echo JText::_($m[$k]); ?></a>
							</li>
						<?php
						}
						?>
						</ol>
					<?php } ?>
					</li>
				<?php 
				}
			}
				?>
				</ol>
			</div><!-- / .blog-entries-years -->

			<div class="container blog-popular-entries">
				<h4><?php echo JText::_('COM_BLOG_POPULAR_ENTRIES'); ?></h4>
			<?php if ($popular = $this->model->entries('recent', $this->filters)) { ?>
				<ol>
				<?php foreach ($popular as $row) { ?>
					<li>
						<a href="<?php echo JRoute::_($row->link()); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
					</li>
				<?php } ?>
				</ol>
			<?php } else { ?>
				<p><?php echo JText::_('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
			<?php } ?>
			</div><!-- / .blog-popular-entries -->

			<div class="container blog-recent-entries">
				<h4><?php echo JText::_('COM_BLOG_RECENT_ENTRIES'); ?></h4>
			<?php if ($recent = $this->model->entries('recent', $this->filters)) { ?>
				<ol>
				<?php foreach ($recent as $row) { ?>
					<li>
						<a href="<?php echo JRoute::_($row->link()); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
					</li>
				<?php } ?>
				</ol>
			<?php } else { ?>
				<p><?php echo JText::_('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
			<?php } ?>
			</div><!-- / .blog-recent-entries -->
		</div><!-- / .aside -->
		<div class="subject">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_BLOG_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<input type="text" name="search" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<h3>
				<?php if (isset($this->filters['search']) && $this->filters['search']) { ?>
					<?php echo JText::sprintf('COM_BLOG_SEARCH_FOR', $this->filters['search']); ?>
				<?php } else if (!isset($this->filters['year']) || !$this->filters['year']) { ?>
					<?php echo JText::_('COM_BLOG_LATEST_ENTRIES'); ?>
				<?php } else { 
					$format = '%b %Y';
					if (version_compare(JVERSION, '1.6', 'ge'))
					{
						$format = 'M Y';
					}
					$archiveDate  = $this->filters['year'];
					$archiveDate .= ($this->filters['month']) ? '-' . $this->filters['month'] : '-01';
					$archiveDate .= '-01 00:00:00';
					echo JHTML::_('date', $archiveDate, $format, BLOG_DATE_TIMEZONE); /* BLOG_DATE_TIMEZONE defined in BlogModelEntry */
				} ?>
				</h3>

		<?php if ($rows = $this->model->entries('list', $this->filters)) { ?>
				<ol class="blog-entries">
				<?php 
				$cls = 'even';
				foreach ($rows as $row)
				{
					$cls = ($cls == 'even') ? 'odd' : 'even';

					if ($row->ended())
					{
						$cls .= ' expired';
					}
				?>
					<li class="<?php echo $cls; ?>" id="e<?php echo $row->get('id'); ?>">
						<h4 class="entry-title">
							<a href="<?php echo JRoute::_($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</a>
						<?php if ($juser->get('id') == $row->get('created_by')) { ?>
							<a class="edit" href="<?php echo JRoute::_($row->link('edit')); ?>" title="<?php echo JText::_('COM_BLOG_EDIT'); ?>">
								<?php echo JText::_('COM_BLOG_EDIT'); ?>
							</a>
							<a class="delete" href="<?php echo JRoute::_($row->link('delete')); ?>" title="<?php echo JText::_('COM_BLOG_DELETE'); ?>">
								<?php echo JText::_('COM_BLOG_DELETE'); ?>
							</a>
						<?php } ?>
						</h4>
						<dl class="entry-meta">
							<dt>
								<span>
									<?php echo JText::sprintf('COM_BLOG_ENTRY_NUMBER', $row->get('id')); ?>
								</span>
							</dt>
							<dd class="date">
								<time datetime="<?php echo $row->published(); ?>">
									<?php echo $row->published('date'); ?>
								</time>
							</dd>
							<dd class="time">
								<time datetime="<?php echo $row->published(); ?>">
									<?php echo $row->published('time'); ?>
								</time>
							</dd>
						<?php if ($this->config->get('show_authors')) { ?>
							<dd class="author">
								<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>">
									<?php echo $this->escape(stripslashes($row->get('name'))); ?>
								</a>
							</dd>
						<?php } ?>
						<?php if ($row->get('allow_comments') == 1) { ?>
							<dd class="comments">
								<a href="<?php echo JRoute::_($row->link('comments')); ?>">
									<?php echo JText::sprintf('COM_BLOG_NUM_COMMENTS', $row->get('comments', 0)); ?>
								</a>
							</dd>
						<?php } else { ?>
							<dd class="comments">
								<span>
									<?php echo JText::_('COM_BLOG_COMMENTS_OFF'); ?>
								</span>
							</dd>
						<?php } ?>
						<?php if ($juser->get('id') == $row->get('created_by')) { ?>
							<dd class="state <?php echo $row->state('text'); ?>">
								<?php echo JText::_('COM_BLOG_STATE_' . strtoupper($row->state('text'))); ?>
							</dd>
						<?php } ?>
						</dl>
						<div class="entry-content">
						<?php if ($this->config->get('cleanintro', 1)) { ?>
							<p>
								<?php echo $row->content('clean', $this->config->get('introlength', 300)); ?> 
							</p>
						<?php } else { ?>
							<?php echo $row->content('parsed', $this->config->get('introlength', 300)); ?> 
						<?php } ?>
						</div>
					</li>
				<?php } ?>
				</ol>

				<?php 
				jimport('joomla.html.pagination');
				$pageNav = new JPagination(
					$this->model->entries('count', $this->filters), 
					$this->filters['start'], 
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('year', $this->filters['year']);
				$pageNav->setAdditionalUrlParam('month', $this->filters['month']);
				$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
				echo $pageNav->getListFooter();
				?>
		<?php } else { ?>
				<p class="warning"><?php echo JText::_('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->
