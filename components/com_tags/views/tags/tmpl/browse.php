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

$this->filters['sort'] = '';
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-tag tag btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_TAGS_MORE_TAGS'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get">
	<section class="main section">
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<label for="entry-search-text"><?php echo JText::_('COM_TAGS_SEARCH_TAGS'); ?></label>
					<input type="text" name="search" id="entry-search-text" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_TAGS_SEARCH_ENTER_TAGS'); ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<ul class="entries-menu sort-options">
					<li>
						<?php
							$cls = ($this->filters['sortby'] == 'total') ? ' class="active"' : '';
							$url = JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=total&search='.urlencode($this->filters['search']).'&limit='.JRequest::getVar("limit", 25).'&limitstart='.JRequest::getVar("limitstart", 0));
						?>
						<a <?php echo $cls; ?> href="<?php echo $url; ?>" title="<?php echo JText::_('COM_TAGS_BROWSE_SORT_POPULARITY_TITLE'); ?>">
							<?php echo JText::_('COM_TAGS_BROWSE_SORT_POPULARITY'); ?>
						</a>
					</li>
					<li>
						<?php
							$cls = ($this->filters['sortby'] == '' || $this->filters['sortby'] == 'raw_tag') ? ' class="active"' : '';
							$url = JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=raw_tag&search='.urlencode($this->filters['search']).'&limit='.JRequest::getVar("limit", 25).'&limitstart='.JRequest::getVar("limitstart", 0));
						?>
						<a<?php echo $cls; ?> href="<?php echo $url; ?>" title="<?php echo JText::_('COM_TAGS_BROWSE_SORT_ALPHA_TITLE'); ?>">
							<?php echo JText::_('COM_TAGS_BROWSE_SORT_ALPHA'); ?>
						</a>
					</li>
				</ul>

				<table class="entries" id="taglist">
					<caption>
						<?php
						if (!$this->filters['limit'])
						{
							$this->filters['limit'] = $this->total;
						}
						$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '') {
							echo JText::sprintf('COM_TAGS_BROWSE_SEARCH_FOR_IN', $this->escape($this->filters['search']), JText::_('COM_TAGS'));
						} else {
							echo JText::_('COM_TAGS');
						}
						?>
						<span>(<?php echo $s . '-' . $e; ?> of <?php echo $this->total; ?>)</span>
					</caption>
					<thead>
						<tr>
							<th scope="col">
								<?php echo JText::_('COM_TAGS_TAG'); ?>
							</th>
							<th scope="col">
								<?php echo JText::_('COM_TAGS_COL_ALIAS'); ?>
							</th>
						<?php if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
							<th scope="col" colspan="2">
								<?php echo JText::_('COM_TAGS_COL_ACTION'); ?>
							</th>
						<?php } ?>
						</tr>
					</thead>
					<tbody>
				<?php
				if ($this->rows->total())
				{
					$cls = 'even';
					foreach ($this->rows as $row)
					{
						$cls = ($cls == 'even' ? 'odd' : 'even');
				?>
						<tr class="<?php echo $cls . ($row->get('admin') ? ' admin' : ''); ?>">
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&tag=' . $row->get('tag')); ?>">
									<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>
								</a>
							</td>
							<td>
								<?php echo $row->get('substitutes') ? $this->escape(stripslashes($row->substitutes('string'))) : '<span>' . JText::_('COM_TAGS_NONE') . '</span>'; ?>
							</td>
					<?php if ($this->config->get('access-edit-tag') || $this->config->get('access-delete-tag')) { ?>
							<td>
						<?php if ($this->config->get('access-delete-tag')) { ?>
								<a class="icon-delete delete delete-tag" data-confirm="<?php echo JText::_('COM_TAGS_CONFIRM_DELETE'); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=delete&id[]=' . $row->get('id') . '&search=' . urlencode($this->filters['search']) . '&sortby=' . $this->filters['sortby'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
									<?php echo JText::_('COM_TAGS_DELETE_TAG'); ?>
								</a>
						<?php } ?>
							</td>
							<td>
						<?php if ($this->config->get('access-edit-tag')) { ?>
								<a class="icon-edit edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . $row->get('id') . '&search=' . urlencode($this->filters['search']) . '&sortby=' . $this->filters['sortby'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>" title="<?php echo JText::_('COM_TAGS_EDIT_TAG'); ?> &quot;<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>&quot;">
									<?php echo JText::_('COM_TAGS_EDIT'); ?>
								</a>
						<?php } ?>
							</td>
					<?php } ?>
						</tr>
				<?php
					}
				}
				?>
					</tbody>
				</table>
				<?php
					$this->pageNav->setAdditionalUrlParam('search', $this->filters['search']);
					$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
					echo $this->pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
				<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
			</div><!-- / .container -->
		</div><!-- / .main subject -->
		<aside class="aside">
			<div class="container">
				<p>
					<?php echo JText::_('COM_TAGS_BROWSE_EXPLANATION'); ?>
				</p>
				<p class="help">
					<strong><?php echo JText::_('COM_TAGS_WHATS_AN_ALIAS'); ?></strong>
					<br /><?php echo JText::_('COM_TAGS_ALIAS_EXPLANATION'); ?>
				</p>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</section><!-- / .main section -->
</form>
