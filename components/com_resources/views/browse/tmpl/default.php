<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

$database = JFactory::getDBO();

$sortbys = array();
if ($this->config->get('show_ranking')) {
	$sortbys['ranking'] = JText::_('COM_RESOURCES_RANKING');
}
$sortbys['date'] = JText::_('COM_RESOURCES_DATE_PUBLISHED');
$sortbys['date_modified'] = JText::_('COM_RESOURCES_DATE_MODIFIED');
$sortbys['title'] = JText::_('COM_RESOURCES_TITLE');

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">
				<?php echo JText::_('Submit a resource'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" id="resourcesform" method="get">
	<section class="main section">
		<div class="subject">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search for Courses'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="type" value="<?php echo $this->escape($this->filters['type']); ?>" />
					<input type="hidden" name="tag" value="<?php echo $this->escape($this->filters['tag']); ?>" />
				</fieldset>
				<?php if ($this->filters['tag']) { ?>
					<fieldset class="applied-tags">
						<ol class="tags">
						<?php
						$url  = 'index.php?option=' . $this->option . '&task=browse';
						$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
						$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
						$url .= ($this->filters['type']   ? '&type=' . $this->escape($this->filters['type'])     : '');

						$tags = $rt->parseTopTags($this->filters['tag']);
						foreach ($tags as $tag)
						{
							?>
							<li>
								<a href="<?php echo JRoute::_($url . '&tag=' . implode(',', $rt->parseTopTags($this->filters['tag'], $tag))); ?>">
									<?php echo $this->escape(stripslashes($tag)); ?>
									<span class="remove">x</a>
								</a>
							</li>
							<?php
						}
						?>
						</ol>
					</fieldset>
				<?php } ?>
			</div><!-- / .container -->

			<div class="container">
				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				$qs .= ($this->filters['type']   ? '&type=' . $this->escape($this->filters['type'])     : '');
				$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
				?>
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=title' . $qs); ?>" title="Sort by title">&darr; Title</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'date') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=date' . $qs); ?>" title="Sort by date published">&darr; Published</a></li>
					<?php if ($this->config->get('show_ranking')) { ?>
					<li><a<?php echo ($this->filters['sortby'] == 'ranking') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=ranking' . $qs); ?>" title="Sort by date published">&darr; Ranking</a></li>
					<?php } ?>
				</ul>

				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				$qs .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
				$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
				?>
				<?php if (count($this->types) > 0) { ?>
					<ul class="entries-menu filter-options">
						<li>
							<a<?php echo (!$this->filters['type']) ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&type=' . $qs); ?>"><?php echo JText::_('All'); ?></a>
						</li>
					<?php foreach ($this->types as $item) { ?>
						<li>
							<a<?php echo ($this->filters['type'] == $item->id) ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&type=' . $item->alias . $qs); ?>"><?php echo $this->escape(stripslashes($item->type)); ?></a>
						</li>
					<?php } ?>
					</ul>
				<?php } ?>

				<div class="clearfix"></div>

				<div class="container-block">
					<?php if ($this->results) { ?>
						<?php
						$this->view('_list', 'browse')
						     ->set('lines', $this->results)
						     ->set('show_edit', $this->authorized)
						     ->display();
						?>
						<div class="clear"></div>
					<?php } else { ?>
						<p class="warning"><?php echo JText::_('COM_RESOURCES_NO_RESULTS'); ?></p>
					<?php } ?>
				</div>
				<?php
				$this->pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
				$this->pageNav->setAdditionalUrlParam('type', $this->filters['type']);
				$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

				echo $this->pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3>Finding a resource</h3>
				<p>Use the sorting or filtering options to sort results and/or narrow down the list of resources.</p>
				<p>Use the 'Search' to find specific resources by title or description.</p>
			</div><!-- / .container -->
			<div class="container">
				<h3>Popular Tags</h3>
				<?php
				$rt = new ResourcesTags($database);
				echo $rt->getTopTagCloud(20, $this->filters['tag']);
				?>
				<p>Click a tag to see only resources with that tag.</p>
			</div>
		</aside><!-- / .aside -->
	</section><!-- / .main section -->
</form>