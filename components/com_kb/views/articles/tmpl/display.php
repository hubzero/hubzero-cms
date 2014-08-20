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
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
	<?php } ?>
		<div class="subject">
			<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=all'); ?>" method="post">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_KB_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<legend><?php echo JText::_('COM_KB_SEARCH_LEGEND'); ?></legend>
						<label for="entry-search-field"><?php echo JText::_('COM_KB_SEARCH_LABEL'); ?></label>
						<input type="text" name="search" id="entry-search-field" value="" placeholder="<?php echo JText::_('COM_KB_SEARCH_PLACEHOLDER'); ?>" />
						<input type="hidden" name="order" value="recent" />
						<input type="hidden" name="task" value="category" />
						<input type="hidden" name="section" value="all" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					</fieldset>
				</div><!-- / .container -->
	
				<div class="container">
					<div class="container-block">
						<h3>Articles</h3>
						<div class="grid">
							<div class="col span-half">
								<h4>
									<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=article&section=all&order=popularity'); ?>">
										<?php echo JText::_('COM_KB_POPULAR_ARTICLES'); ?> <span class="more">&raquo;</span>
									</a>
								</h4>
							<?php
							$popular = $this->archive->articles('popular', array('limit' => 5));
							if ($popular->total() > 0) { ?>
								<ul class="articles">
								<?php foreach ($popular as $row) { ?>
									<li>
										<a href="<?php echo $row->link(); ?>" title="<?php echo JText::_('COM_KB_READ_ARTICLE'); ?>">
											<?php echo $this->escape(stripslashes($row->get('title'))); ?>
										</a>
									</li>
								<?php } ?>
								</ul>
							<?php } else { ?>
								<p><?php echo JText::_('COM_KB_NO_ARTICLES'); ?></p>
							<?php } ?>
							</div><!-- / .col span-half -->
							<div class="col span-half omega">
								<h4>
									<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=article&section=all&order=recent'); ?>">
										<?php echo JText::_('COM_KB_RECENT_ARTICLES'); ?> <span class="more">&raquo;</span>
									</a>
								</h4>
							<?php
							$recent = $this->archive->articles('recent', array('limit' => 5));
							if ($recent->total() > 0) { ?>
								<ul class="articles">
								<?php foreach ($recent as $row) { ?>
									<li>
										<a href="<?php echo $row->link(); ?>" title="<?php echo JText::_('COM_KB_READ_ARTICLE'); ?>">
											<?php echo $this->escape(stripslashes($row->get('title'))); ?>
										</a>
									</li>
								<?php } ?>
								</ul>
							<?php } else { ?>
								<p><?php echo JText::_('COM_KB_NO_ARTICLES'); ?></p>
							<?php } ?>
							</div><!-- / .col span-half -->
						</div><!-- / .grid -->
	
						<h3><?php echo JText::_('COM_KB_CATEGORIES'); ?></h3>
						<div class="grid">
						<?php
							$i = 0;
							$filters = array();
							$filters['limit']    = JRequest::getInt('limit', 3);
							$filters['start']    = JRequest::getInt('limitstart', 0);
							$filters['order']    = JRequest::getWord('order', 'recent');
							$filters['category'] = 0;
							$filters['state']    = 1;
							$filters['access']   = 0;
							foreach ($this->archive->categories('list', array('sort' => 'title', 'sort_Dir' => 'ASC')) as $row)
							{
								$i++;
								switch ($i)
								{
									case 1: $cls = ''; break;
									case 2: $cls = ' omega'; break;
								}
								$filters['section'] = $row->get('id');
								$articles = $row->articles('list', $filters);
								?>
							<div class="col span-half<?php echo $cls; ?>">
								<h4>
									<a href="<?php echo JRoute::_($row->link()); ?>">
										<?php echo $this->escape(stripslashes($row->get('title'))); ?> <span>(<?php echo $row->get('articles', 0); ?>)</span> <span class="more">&raquo;</span>
									</a>
								</h4>
							<?php if ($articles->total() > 0) { ?>
								<ul class="articles">
								<?php foreach ($articles as $article) { ?>
									<li>
										<a href="<?php echo $article->link(); ?>">
											<?php echo $this->escape(stripslashes($article->get('title'))); ?>
										</a>
									</li>
								<?php } ?>
								</ul>
							<?php } else { ?>
								<p><?php echo JText::_('COM_KB_NO_ARTICLES'); ?></p>
							<?php } ?>
							</div><!-- / .col span-half <?php echo $cls; ?> -->
							<?php //echo ($i >= 2) ? '<div class="clearfix"></div>' : ''; ?>
								<?php
								if ($i >= 2)
								{
									$i = 0;
								}
							}
						?>
						</div><!-- / .grid -->
					</div><!-- / .container-block -->
				</div><!-- / .container -->
			</form>
		</div><!-- / .subject -->
	
		<aside class="aside">
		<?php if (JComponentHelper::isEnabled('com_answers')) { ?>
			<div class="container">
				<h3><?php echo JText::_('COM_KB_COMMUNITY'); ?></h3>
				<p>
					<?php echo JText::_('COM_KB_COMMUNITY_CANT_FIND'); ?> <?php echo JText::sprintf('COM_KB_COMMUNITY_TRY_ANSWERS', '<a href="' . JRoute::_('index.php?option=com_answers') . '">' . JText::_('COM_ANSWERS') . '</a>'); ?>
				</p>
			</div><!-- / .container -->
		<?php } ?>
		<?php if (JComponentHelper::isEnabled('com_wishlist')) { ?>
			<div class="container">
				<h3><?php echo JText::_('COM_KB_FEATURE_REQUEST'); ?></h3>
				<p>
					<?php echo JText::_('COM_KB_HAVE_A_FEATURE_REQUEST'); ?> <a href="<?php echo JRoute::_('index.php?option=com_wishlist'); ?>"><?php echo JText::_('COM_KB_FEATURE_TELL_US'); ?></a>
				</p>
			</div><!-- / .container -->
		<?php } ?>
		<?php if (JComponentHelper::isEnabled('com_support')) { ?>
			<div class="container">
				<h3><?php echo JText::_('COM_KB_TROUBLE_REPORT'); ?></h3>
				<p>
					<?php echo JText::_('COM_KB_TROUBLE_FOUND_BUG'); ?> <a href="<?php echo JRoute::_('index.php?option=com_support&controller=tickets&task=new'); ?>"><?php echo JText::_('COM_KB_TROUBLE_TELL_US'); ?></a>
				</p>
			</div><!-- / .container -->
		<?php } ?>
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</div><!-- / .main section -->
