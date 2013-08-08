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

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=blog';
?>

<?php if ($this->canpost || ($this->authorized == 'manager' || $this->authorized == 'admin')) { ?>
<ul id="page_options">
	<?php if ($this->canpost) { ?>
	<li>
		<a class="icon-add add btn" href="<?php echo JRoute::_($base . '&action=new'); ?>">
			<?php echo JText::_('New entry'); ?>
		</a>
	</li>
	<?php } ?>
	<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
	<li>
		<a class="icon-config config btn" href="<?php echo JRoute::_($base . '&action=settings'); ?>" title="<?php echo JText::_('Edit Settings'); ?>">
			<?php echo JText::_('Settings'); ?>
		</a>
	</li>
	<?php } ?>
</ul>
<?php } ?>

<form method="get" action="<?php echo JRoute::_($base . '&action=browse'); ?>" id="blogentries">
	<div class="aside">
		<div class="container blog-entries-years">
			<h4><?php echo JText::_('Entries By Year'); ?></h4>
			<ol>
				<?php if ($first->exists()) { ?>
					<?php 
						$start = intval(substr($first->get('publish_up'), 0, 4));
						$now = date("Y");
					?>
					<?php for ($i=$now, $n=$start; $i >= $n; $i--) { ?>
						<li>
							<a href="<?php echo JRoute::_($base . '&scope=' . $i); ?>">
								<?php echo $i; ?>
							</a>
							<?php if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) { ?>
								<ol>
									<?php
										$m = array(
											'PLG_GROUPS_BLOG_JANUARY',
											'PLG_GROUPS_BLOG_FEBRUARY',
											'PLG_GROUPS_BLOG_MARCH',
											'PLG_GROUPS_BLOG_APRIL',
											'PLG_GROUPS_BLOG_MAY',
											'PLG_GROUPS_BLOG_JUNE',
											'PLG_GROUPS_BLOG_JULY',
											'PLG_GROUPS_BLOG_AUGUST',
											'PLG_GROUPS_BLOG_SEPTEMBER',
											'PLG_GROUPS_BLOG_OCTOBER',
											'PLG_GROUPS_BLOG_NOVEMBER',
											'PLG_GROUPS_BLOG_DECEMBER'
										);
										if ($i == $now) {
											$months = date("m");
										} else {
											$months = 12;
										}
									?>
									<?php for ($k=0, $z=$months; $k < $z; $k++) { ?>
										<li>
											<a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_($base . '&scope='.$i.'/'.sprintf( "%02d",($k+1),1)); ?>">
												<?php echo JText::_($m[$k]); ?>
											</a>
										</li>
									<?php } ?>
								</ol>
							<?php } ?>
						</li>
					<?php } ?>
				<?php } ?>
			</ol>
		</div>

	<?php 
	$limit = $this->filters['limit']; 
	$this->filters['limit'] = 5;
	?>
		<div class="container blog-popular-entries">
			<h4><?php echo JText::_('PLG_GROUPS_BLOG_POPULAR_ENTRIES'); ?></h4>
		<?php if ($popular = $this->model->entries('popular', $this->filters)) { ?>
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
			<p><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-popular-entries -->

		<div class="container blog-recent-entries">
			<h4><?php echo JText::_('PLG_GROUPS_BLOG_RECENT_ENTRIES'); ?></h4>
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
			<p><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-recent-entries -->
	<?php
	$this->filters['limit'] = $limit; 
	?>
	</div><!-- / .aside -->
	
	<div class="subject">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo JText::_('Search for posts'); ?></legend>
				<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
				<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape(utf8_encode(stripslashes($this->search))); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase...'); ?>" />
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
					if ($this->config->get('feeds_enabled', 1)) :
						$path  = $base . '&scope=feed.rss';
						$path .= ($this->year)  ? '&year=' . $this->year   : '';
						$path .= ($this->month) ? '&month=' . $this->month : '';
						$feed = JRoute::_($path);
						$live_site = 'https://' . $_SERVER['HTTP_HOST'];
						if (substr($feed, 0, 4) != 'http') 
						{
							$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
						}
						$feed = str_replace('https://', 'http://', $feed);
				?>
				<a class="feed" href="<?php echo $feed; ?>">
					<?php echo JText::_('RSS Feed'); ?>
				</a>
				<?php endif; ?>
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
					</h4>
					<dl class="entry-meta">
						<dt>
							<span>
								<?php echo JText::sprintf('Entry #%s', $row->get('id')); ?>
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
						<dd class="author">
							<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->get('created_by')); ?>">
								<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
							</a>
						</dd>
					<?php if ($row->get('allow_comments') == 1) { ?>
						<dd class="comments">
							<a href="<?php echo JRoute::_($row->link('comments')); ?>">
								<?php echo JText::sprintf('PLG_GROUPS_BLOG_NUM_COMMENTS',  $row->get('comments', 0)); ?>
							</a>
						</dd>
					<?php } else { ?>
						<dd class="comments">
							<span>
								<?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_OFF'); ?>
							</span>
						</dd>
					<?php } ?>
					<?php if ($this->juser->get('id') == $row->get('created_by') || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
						<dd class="state <?php echo $row->state('text'); ?>">
							<?php echo JText::_('PLG_GROUPS_BLOG_STATE_' . strtoupper($row->state('text'))); ?>
						</dd>
					<?php } ?>
						<dd class="entry-options">
						<?php if ($this->juser->get('id') == $row->get('created_by') || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
							<a class="icon-edit edit" href="<?php echo JRoute::_($row->link('edit')); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_EDIT'); ?>">
								<?php echo JText::_('PLG_GROUPS_BLOG_EDIT'); ?>
							</a>
							<a class="icon-delete delete" href="<?php echo JRoute::_($row->link('delete')); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_DELETE'); ?>">
								<?php echo JText::_('PLG_GROUPS_BLOG_DELETE'); ?>
							</a>
						<?php } ?>
						</dd>
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
				$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
				$pageNav->setAdditionalUrlParam('active', 'blog');
				if ($this->filters['year'])
				{
					$pageNav->setAdditionalUrlParam('year', $this->filters['year']);
				}
				if ($this->filters['month'])
				{
					$pageNav->setAdditionalUrlParam('month', $this->filters['month']);
				}
				if ($this->filters['search'])
				{
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
				}
				echo $pageNav->getListFooter();
			?>
<?php } else { ?>
			<p class="warning"><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
<?php } ?>
		</div>
	</div><!-- / .subject -->
</form><!-- /.main -->

