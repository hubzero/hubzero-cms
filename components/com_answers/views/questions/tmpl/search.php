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
$jconfig =& JFactory::getConfig();

if (!$this->filters['filterby']) 
{
	$this->filters['filterby'] = 'all';
}
if (!$this->filters['filterby'] == 'none') 
{
	$this->filters['filterby'] = 'all';
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>" class="icon-add add btn"><span><?php echo JText::_('COM_ANSWERS_NEW_QUESTION'); ?></span></a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>

<div class="main section">
	<div class="aside">
		<div class="container">
			<h3><?php echo JText::_('COM_ANSWERS_NEED_AN_ANSWER'); ?></h3>
			<p>
				<?php echo JText::sprintf('COM_ANSWERS_CANT_FIND_ANSWER', '<a href="' . JRoute::_('index.php?option=com_kb') . '">' . JText::_('COM_ANSWERS_KNOWLEDGE_BASE') . '</a>', $jconfig->getValue('config.sitename')); ?>
			</p>
		</div><!-- / .container -->
	<?php if ($this->config->get('banking')) { ?>
		<div class="container">
			<h3><?php echo JText::_('COM_ANSWERS_EARN_POINTS'); ?></h3>
			<p>
				<?php echo JText::_('COM_ANSWERS_START_EARNING_POINTS'); ?> <a href="<?php echo $this->config->get('infolink'); ?>"><?php echo JText::_('COM_ANSWERS_LEARN_MORE'); ?></a>.
			</p>
		</div><!-- / .container -->
	<?php } ?>
	</div><!-- / .aside -->
	<div class="subject">
		<form method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_ANSWERS_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<input type="text" name="q" value="<?php echo $this->escape($this->filters['q']); ?>" placeholder="<?php echo JText::_('COM_ANSWERS_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

					<input type="hidden" name="area" value="<?php echo $this->escape($this->filters['area']); ?>" />

					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="filterby" value="<?php echo $this->escape($this->filters['filterby']); ?>" />
					<input type="hidden" name="task" value="<?php echo $this->escape($this->task); ?>" />
				</fieldset>
			</div><!-- / .container -->

		<?php if (!$juser->get('guest')) { ?>
			<ul class="entries-menu user-options">
				<li>
					<a<?php echo ($this->filters['area'] == '') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
						<?php echo JText::_('COM_ANSWERS_FILTER_EVERYTHING'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['area'] == 'mine') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=mine&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
						<?php echo JText::_('COM_ANSWERS_QUESTIONS_I_ASKED'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['area'] == 'assigned') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=assigned&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
						<?php echo JText::_('COM_ANSWERS_QUESTIONS_RELATED_TO_CONTRIBUTIONS'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['area'] == 'interest') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=interest&filterby=' . urlencode($this->filters['filterby']).'&sortby=' . urlencode($this->filters['sortby'])); ?>">
						<?php echo JText::_('COM_ANSWERS_QUESTIONS_TAGGED_WITH_MY_INTERESTS'); ?>
					</a>
				</li>
			</ul>
		<?php } ?>

			<div class="container">
				<ul class="entries-menu order-options">
				<?php if ($this->config->get('banking')) { ?>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'rewards') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=' . urlencode($this->filters['filterby']).'&sortby=rewards'); ?>" title="<?php echo JText::_('COM_ANSWERS_SORT_REWARDS_TITLE'); ?>">
							<?php echo JText::_('COM_ANSWERS_SORT_REWARDS'); ?>
						</a>
					</li>
				<?php } ?>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'votes') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=' . urlencode($this->filters['filterby']).'&sortby=votes'); ?>" title="<?php echo JText::_('COM_ANSWERS_SORT_POPULAR_TITLE'); ?>">
							<?php echo JText::_('COM_ANSWERS_SORT_POPULAR'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'date') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=' . urlencode($this->filters['filterby']).'&sortby=date'); ?>" title="<?php echo JText::_('COM_ANSWERS_SORT_RECENT_TITLE'); ?>">
							<?php echo JText::_('COM_ANSWERS_SORT_RECENT'); ?>
						</a>
					</li>
				</ul>

				<ul class="entries-menu filter-options">
					<li>
						<a<?php echo ($this->filters['filterby'] == 'all' || $this->filters['filterby'] == '') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=all&sortby=' . urlencode($this->filters['sortby'])); ?>" title="<?php echo JText::_('COM_ANSWERS_FILTER_ALL_TITLE'); ?>">
							<?php echo JText::_('COM_ANSWERS_FILTER_ALL'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->filters['filterby'] == 'open') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=open&sortby=' . urlencode($this->filters['sortby'])); ?>" title="<?php echo JText::_('COM_ANSWERS_FILTER_OPEN_TITLE'); ?>">
							<?php echo JText::_('COM_ANSWERS_FILTER_OPEN'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->filters['filterby'] == 'closed') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search&area=' . urlencode($this->filters['area']).'&filterby=closed&sortby=' . urlencode($this->filters['sortby'])); ?>" title="<?php echo JText::_('COM_ANSWERS_FILTER_CLOSED_TITLE'); ?>">
							<?php echo JText::_('COM_ANSWERS_FILTER_CLOSED'); ?>
						</a>
					</li>
				</ul>

				<table class="questions entries" summary="<?php echo JText::_('COM_ANSWERS_RESULTS_SUMMARY'); ?>">
					<caption>
						<?php
							$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
							$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

							if ($this->filters['q'] != '') 
							{
								echo JText::sprintf('COM_ANSWERS_SEARCH_FOR', $this->escape($this->filters['q']), JText::_('COM_ANSWERS_FILTER_' . strtoupper($this->filters['filterby'])));
							} 
							else 
							{
								echo JText::_('COM_ANSWERS_FILTER_' . strtoupper($this->filters['filterby']));
							}
						?>
						<span>(<?php echo JText::sprintf('COM_ANSWERS_RESULTS_TOTAL', $s, $e, $this->total); ?>)</span>
					</caption>
					<tbody>
			<?php if (count($this->results) > 0) { ?>
				<?php
				foreach ($this->results as $row)
				{
					// author name
					$name = JText::_('COM_ANSWERS_ANONYMOUS');
					if (!$row->get('anonymous')) 
					{
						$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->creator('id')) . '">' . $this->escape(stripslashes($row->get('name'))) . '</a>';
					}

					$cls  = ($row->isclosed())   ? 'answered' : '';
					$cls  = ($row->isReported()) ? 'flagged'  : $cls;
					$cls .= ($row->get('created_by') == $juser->get('username')) ? ' mine' : '';
				?>
						<tr<?php echo ($cls) ? ' class="'.$cls.'"' : ''; ?>>
							<th>
								<span class="entry-id"><?php echo $row->get('id'); ?></span>
							</th>
							<td>
							<?php if (!$row->isReported()) { ?>
								<a class="entry-title" href="<?php echo JRoute::_($row->link()); ?>">
									<?php echo $this->escape(stripslashes($row->get('subject'))); ?>
								</a><br />
							<?php } else { ?>
								<span class="entry-title">
									<?php echo JText::_('COM_ANSWERS_QUESTION_UNDER_REVIEW'); ?>
								</span><br />
							<?php } ?>
								<span class="entry-details">
									<?php echo JText::sprintf('COM_ANSWERS_ASKED_BY', $name); ?> <span class="entry-date-at"><?php echo JText::_('COM_ANSWERS_DATETIME_AT'); ?></span> 
									<span class="entry-time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span> <span class="entry-date-on"><?php echo JText::_('COM_ANSWERS_DATETIME_ON'); ?></span> 
									<span class="entry-date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
									<span class="entry-details-divider">&bull;</span>
									<span class="entry-state">
										<?php echo ($row->get('state')==1) ? JText::_('COM_ANSWERS_STATE_CLOSED') : JText::_('COM_ANSWERS_STATE_OPEN'); ?>
									</span>
									<span class="entry-details-divider">&bull;</span>
									<span class="entry-comments">
										<a href="<?php echo JRoute::_($row->link() . '#answers'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_RESPONSES_TO_THIS_QUESTION', $row->get('rcount')); ?>">
											<?php echo $row->get('rcount'); ?>
										</a>
									</span>
								</span>
							</td>
						<?php if ($this->config->get('banking')) { ?>
							<td class="reward">
							<?php if ($row->get('reward') == 1) { ?>
								<span class="entry-reward">
									<?php echo $row->get('points'); ?> 
									<a href="<?php echo $this->config->get('infolink'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->get('points')); ?>">
										<?php echo JText::_('COM_ANSWERS_POINTS'); ?>
									</a>
								</span>
							<?php } ?>
							</td>
						<?php } ?>
							<td class="voting">
								<span class="vote-like">
								<?php if ($juser->get('guest')) { ?>
									<span class="vote-button <?php echo ($row->get('helpful') > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo JText::_('COM_ANSWERS_VOTE_LIKE_LOGIN'); ?>">
										<?php echo $row->get('helpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
									</span>
								<?php } else { ?>
									<a class="vote-button <?php echo ($row->get('helpful') > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo JRoute::_('index.php?option=com_answers&task=vote&id=' . $row->get('id') . '&vote=1'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_VOTE_LIKE_TITLE', $row->get('helpful')); ?>">
										<?php echo $row->get('helpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
									</a>
								<?php } ?>
								</span>
							</td>
						</tr>
				<?php } // end foreach ?>
			<?php } else { ?>
						<tr class="noresults">
							<td>
								<?php echo JText::_('COM_ANSWERS_NO_RESULTS'); ?>
							</td>
						</tr>
			<?php } // end if (count($this->results) > 0) { ?>
					</tbody>
				</table>
				<?php 
				$this->pageNav->setAdditionalUrlParam('q', $this->filters['q']);
				$this->pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
				$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
				$this->pageNav->setAdditionalUrlParam('area', $this->filters['area']);
				echo $this->pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</form>
	</div><!-- / .subject -->
</div><!-- / .main section -->

