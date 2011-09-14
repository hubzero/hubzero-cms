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

//import helper class
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Wiki_Parser');

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => $this->group->get('gidNumber') . DS . 'blog',
	'pagename' => 'group',
	'pageid'   => '',
	'filepath' => $this->path,
	'domain'   => $this->group->get('cn')
);

$p =& Hubzero_Wiki_Parser::getInstance();
?>
<a name="blog"></a>
<h3 class="heading"><?php echo JText::_('PLG_GROUPS_BLOG'); ?></h3>

<div class="main section">
	<div class="aside">
		<?php if($this->config->get('feeds_enabled') || $this->canpost || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
			<div class="container">
				<h4>Blog Actions</h4>
				<?php
					if ($this->config->get('feeds_enabled')) {
						$path  = 'index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&scope=feed.rss';
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

						echo "<p class=\"feed\"><a href=\"{$feed}\">".JText::_('Subscribe RSS')."</a></p>";
					}
				?>
			
				<?php if ($this->canpost) { ?>
					<p class="add">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=new'); ?>">
							<?php echo JText::_('New entry'); ?>
						</a>
					</p>
				<?php } ?>
			
				<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
					<p class="config">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=settings'); ?>" title="<?php echo JText::_('Edit Settings'); ?>">
							<?php echo JText::_('Settings'); ?>
						</a>
					</p>
				<?php } ?>
			</div>
		<?php } ?>
		
		<div class="blog-entries-years">
			<h4><?php echo JText::_('Entries By Year'); ?></h4>
			<ol>
				<?php if ($this->firstentry) { ?>
					<?php 
						$start = intval(substr($this->firstentry,0,4));
						$now = date("Y");
					?>
					<?php for ($i=$now, $n=$start; $i >= $n; $i--) { ?>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.$i); ?>">
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
											<a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->get('cn').'&active=blog&scope='.$i.'/'.sprintf( "%02d",($k+1),1)); ?>">
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
		
		<div class="blog-popular-entries">
			<h4><?php echo JText::_('Popular Entries'); ?></h4>
			<ol>
				<?php if ($this->popular) { ?>
					<?php foreach ($this->popular as $row) { ?>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>">
								<?php echo stripslashes($row->title); ?>
							</a>
						</li>
					<?php } ?>
				<?php } ?>
			</ol>
		</div><!-- / .blog-popular-entries -->

		<div class="blog-recent-entries">
			<h4><?php echo JText::_('Recent Entries'); ?></h4>
			<ol>
				<?php if ($this->recent) { ?>
					<?php foreach ($this->recent as $row) { ?>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>">
								<?php echo stripslashes($row->title); ?>
							</a>
						</li>
					<?php } ?>
				<?php } ?>
			</ol>
		</div><!-- / .blog-recent-entries -->
	</div><!-- / .aside -->
	
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=browse'); ?>" method="get" id="blogentries">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="Search" />
			<fieldset class="entry-search">
				<legend>Search for articles</legend>				
				<label for="entry-search-field">Enter keyword or phrase</label>
				<input type="text" name="search" id="entry-search-field" value="<?php echo htmlentities(utf8_encode(stripslashes($this->search)),ENT_COMPAT,'UTF-8'); ?>" />
			</fieldset>
		</div><!-- / .container -->
		<div class="container">
			<table class="entries">
				<caption><?php echo JText::_('Blog Entries'); ?></caption>
				<tbody>
					<?php if($this->rows) { ?>
						<?php foreach ($this->rows as $row) { ?>
							<tr>
								<?php 
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
								<td id="e<?php echo $row->id; ?>" class="<?php echo $cls; ?>">
									<dl class="entry-meta">
										<dt class="date"><?php echo JHTML::_('date',$row->publish_up, '%d %b, %Y', 0); ?></dt>
										<dd class="time"><?php echo JHTML::_('date',$row->publish_up, '%I:%M %p', 0); ?></dd>

										<?php if ($row->allow_comments == 1) { ?>
											<dd class="comments"><a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias.'#comments'); ?>"><?php echo JText::sprintf('PLG_GROUPS_BLOG_NUM_COMMENTS', $row->comments); ?></a></dd>
										<?php } else { ?>
											<dd class="comments"><?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_OFF'); ?></dd>
										<?php } ?>
										<dd class="state"><?php echo $state; ?></dd>
									</dl>

									<h4 class="entry-title">
										<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a>

										<?php if ($this->juser->get('id') == $row->created_by || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
											<a class="edit" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=edit&entry='.$row->id); ?>" title="<?php echo JText::_('Edit'); ?>"><?php echo JText::_('Edit'); ?></a>
											<a class="delete" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=delete&entry='.$row->id); ?>" title="<?php echo JText::_('Delete'); ?>"><?php echo JText::_('Delete'); ?></a>
										<?php } ?>
									</h4>
									<div class="entry-content">
										<p class="entry-author">Posted by <cite><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo stripslashes($row->name); ?></a></cite></p>
										<p>
											<?php 
												$content = plgGroupsBlog::stripWiki($row->content);
												echo Hubzero_View_Helper_Html::shortenText($content, 300, 0);
												//echo $p->parse( "\n".stripslashes($content), $wikiconfig, true, true );
											?> 
											<a class="readmore" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>" title="<?php echo JText::sprintf('PLG_GROUPS_BLOG_READMORE', strip_tags(stripslashes($row->title))) ?>">Continue reading &rarr;</a>
										</p>
									</div>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td>Currently there are no blog entries.</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php echo $this->pagenavhtml; ?>
		</div>
	</div><!-- / .subject -->
</div><!-- /.main -->

