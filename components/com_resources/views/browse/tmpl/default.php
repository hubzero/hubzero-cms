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
if ($this->config->get('show_ranking'))
{
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
				<?php echo JText::_('COM_RESOURCES_SUBMIT_A_RESOURCE'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" id="resourcesform" method="get">
	<section class="main section">
		<div class="subject">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_RESOURCES_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('COM_RESOURCES_FIND_RESOURCE'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('COM_RESOURCES_SEARCH_LABEL'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_RESOURCES_SEARCH_LABEL'); ?>" />
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
						$url .= ($this->filters['type']   ? '&type=' . $this->escape($this->filters['type'])     : '');

						$rt = new ResourcesTags(0);
						$tags = $rt->parseTopTags($this->filters['tag']);
						foreach ($tags as $tag)
						{
							?>
							<li>
								<a href="<?php echo JRoute::_($url . '&tag=' . implode(',', $rt->parseTopTags($this->filters['tag'], $tag))); ?>">
									<?php echo $this->escape(stripslashes($tag)); ?>
									<span class="remove" title="<?php echo JText::_('COM_RESOURCES_REMOVE_TAG'); ?>">x</a>
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
					<p><?php echo JText::_('COM_RESOURCES_SEARCH_TAG_LIMIT_REACHED'); ?></p>
					<ol class="tags">
					<?php
					$url  = 'index.php?option=' . $this->option . '&task=browse';
					$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
					$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
					$url .= ($this->filters['type']   ? '&type=' . $this->escape($this->filters['type'])     : '');

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
				$qs .= ($this->filters['type']   ? '&type=' . $this->escape($this->filters['type'])     : '');
				$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
				?>
				<ul class="entries-menu order-options">
					<li>
						<a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&sortby=title' . $qs); ?>" title="<?php echo JText::_('COM_RESOURCES_SORT_BY_TITLE'); ?>"><?php echo JText::_('COM_RESOURCES_SORT_TITLE'); ?></a>
					</li>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'date') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&sortby=date' . $qs); ?>" title="<?php echo JText::_('COM_RESOURCES_SORT_BY_PUBLISHED'); ?>"><?php echo JText::_('COM_RESOURCES_SORT_PUBLISHED'); ?></a>
					</li>
					<?php if ($this->config->get('show_ranking')) { ?>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'ranking') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&sortby=ranking' . $qs); ?>" title="<?php echo JText::_('COM_RESOURCES_SORT_BY_RANKING'); ?>"><?php echo JText::_('COM_RESOURCES_SORT_RANKING'); ?></a>
					</li>
					<?php } ?>
				</ul>

				<?php if (count($this->types) > 0) { ?>
					<ul class="entries-menu filter-options">
						<li>
							<select name="type" id="filter-type">
								<option value="" <?php echo (!$this->filters['type']) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_ALL_TYPES'); ?></option>
								<?php foreach ($this->types as $item) { ?>
									<?php
									if ($item->id == 7 && !JComponentHelper::isEnabled('com_tools', true))
									{
										continue;
									}
									?>
									<option value="<?php echo $item->id; ?>"<?php echo ($this->filters['type'] == $item->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($item->type)); ?></option>
								<?php } ?>
							</select>
						</li>
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
				<h3><?php echo JText::_('COM_RESOURCES_FIND_RESOURCE'); ?></h3>
				<p><?php echo JText::_('COM_RESOURCES_FIND_RESOURCE_DETAILS'); ?></p>
			</div><!-- / .container -->
			<div class="container">
				<h3><?php echo JText::_('COM_RESOURCES_POPULAR_TAGS'); ?></h3>
				<?php
				$rt = new ResourcesTags(0);
				echo $rt->getTopTagCloud(20, $this->filters['tag']);
				?>
				<p><?php echo JText::_('COM_RESOURCES_POPULAR_TAGS_HINT'); ?></p>
			</div>
		</aside><!-- / .aside -->
	</section><!-- / .main section -->
</form>