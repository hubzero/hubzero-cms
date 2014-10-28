<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$database = JFactory::getDBO();

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" id="resourcesform" method="get">
	<section class="main section">
		<div class="subject">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="tag" value="<?php echo $this->escape($this->filters['tag']); ?>" />
				</fieldset>
				<?php if ($this->filters['tag']) { ?>
					<fieldset class="applied-tags">
						<ol class="tags">
						<?php
						$url  = 'index.php?option=' . $this->option . '&task=browse';
						$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
						$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
						$url .= ($this->filters['category']   ? '&category=' . $this->escape($this->filters['category'])     : '');

						$rt = new PublicationTags($database);
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
			<?php if (isset($this->filters['tag_ignored']) && count($this->filters['tag_ignored']) > 0) { ?>
				<div class="warning">
					<p><?php echo JText::_('Searching only allows up to 5 tags. The following tags were ignored:'); ?></p>
					<ol class="tags">
					<?php
					$url  = 'index.php?option=' . $this->option . '&task=browse';
					$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
					$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
					$url .= ($this->filters['type']   ? '&category=' . $this->escape($this->filters['category'])     : '');

					foreach ($this->filters['tag_ignored'] as $tag)
					{
						?>
						<li>
							<a href="<?php echo JRoute::_($url . '&tag=' . $tag); ?>">
								<?php echo $this->escape(stripslashes($tag)); ?>
							</a>
						</li>
						<?php
					}
					?>
					</ol>
				</div>
			<?php } ?>
			<div class="container">
				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				$qs .= ($this->filters['category']   ? '&category=' . $this->escape($this->filters['category'])     : '');
				$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
				?>
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=title' . $qs); ?>" title="Sort by title">&darr; Title</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'date') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=date' . $qs); ?>" title="Sort by date published">&darr; Published</a></li>
					<?php if ($this->config->get('show_ranking')) { ?>
					<li><a<?php echo ($this->filters['sortby'] == 'ranking') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=ranking' . $qs); ?>" title="Sort by date published">&darr; Ranking</a></li>
					<?php } ?>
				</ul>
				<?php if (count($this->categories) > 0) { ?>
					<ul class="entries-menu filter-options">
						<li>
							<select name="category" id="filter-type">
								<option value="" <?php echo (!$this->filters['category']) ? ' selected="selected"' : ''; ?>><?php echo JText::_('All Categories'); ?></a>
								<?php foreach ($this->categories as $item) { ?>
									<option value="<?php echo $item->id; ?>"<?php echo ($this->filters['category'] == $item->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($item->name)); ?></option>
								<?php } ?>
							</select>
						</li>
					</ul>
				<?php } ?>
			<div class="clearfix"></div>
			<div class="container-block">
			<?php
			if ($this->results) {
				switch ($this->filters['sortby'])
				{
					case 'date_created': $show_date = 1; break;
					case 'date_modified': $show_date = 2; break;
					case 'date':
					default: $show_date = 3; break;
				}
				echo PublicationsHtml::writeResults( $database, $this->results, $this->filters, $show_date );
				echo '<div class="clear"></div>';
			} else { ?>
				<p class="warning"><?php echo JText::_('COM_PUBLICATIONS_NO_RESULTS'); ?></p>
			<?php } ?>
			</div>
			<?php

			$this->pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
			$this->pageNav->setAdditionalUrlParam('category', $this->filters['category']);
			$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

			echo $this->pageNav->getListFooter();
			?>
			<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="aside">
			<div class="container">
				<h3>Popular Tags</h3>
				<?php
				$rt = new PublicationTags($database);
				echo $rt->getTopTagCloud(12, $this->filters['tag']);
				?>
				<p>Click a tag to see only publications with that tag.</p>
			</div>
		</div><!-- / .aside -->
	</section><!-- / .main section -->
</form>