<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// Sorting and paging
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$whatsleft  = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];

// URL
$route 	= 'index.php?option=' . $this->option . a . '&controller=curation';

$pa = new PublicationAuthor( $this->database );

$this->css()
     ->js()
	 ->css('jquery.fancybox.css', 'system')
	 ->css('curation.css')
	 ->js('curation.js');

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div class="main section curation">
<?php if (count($this->rows) > 0 ) { ?>
	<p><?php echo JText::_('COM_PUBLICATIONS_CURATION_LIST_INSTRUCT'); ?></p>
<div class="publist">
	<table class="listing">
		<thead>
			<tr>
				<th class="thtype<?php if ($this->filters['sortby'] == 'id') { echo ' activesort'; } ?>"><a href="<?php echo JRoute::_($route) . '/?t_sortby=id'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . JText::_('COM_PUBLICATIONS_CURATION_ID'); ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_ID'); ?></a></th>
				<th></th>
				<th<?php if ($this->filters['sortby'] == 'title') { echo ' class="activesort"'; } ?>><a href="<?php echo JRoute::_($route) . '/?t_sortby=title'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . JText::_('COM_PUBLICATIONS_CURATION_TITLE'); ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_TITLE'); ?></a></th>
				<th></th>
				<th class="thtype<?php if ($this->filters['sortby'] == 'type') { echo ' activesort'; } ?>"><a href="<?php echo JRoute::_($route) . '/?t_sortby=type'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . JText::_('COM_PUBLICATIONS_CURATION_CONTENT_TYPE'); ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_CONTENT_TYPE'); ?></a></th>
				<th><?php echo JText::_('COM_PUBLICATIONS_CURATION_SUBMITTED'); ?></th>
				<th<?php if ($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?>><a href="<?php echo JRoute::_($route) . '/?t_sortby=status'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_SORT_BY') . ' ' . JText::_('COM_PUBLICATIONS_CURATION_STATUS'); ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_STATUS'); ?></a></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($this->rows as $row)
				{
					$submitted  = $row->reviewed && $row->state == 5
								? strtolower(JText::_('COM_PUBLICATIONS_CURATION_RESUBMITTED'))
								: strtolower(JText::_('COM_PUBLICATIONS_CURATION_SUBMITTED'));
					$submitted .= ' <span class="prominent">' . JHTML::_('date', $row->submitted, 'M d, Y') . '</span> ';

					// Get submitter
					$submitter = $pa->getSubmitter($row->id, $row->created_by);
					$submitted .= ' <span class="block">' . JText::_('COM_PUBLICATIONS_CURATION_BY')
						. ' ' . $submitter->name . '</span>';

					if ($row->state == 7)
					{
						$reviewed  = strtolower(JText::_('COM_PUBLICATIONS_CURATION_REVIEWED'))
							.' <span class="prominent">' . JHTML::_('date', $row->reviewed, 'M d, Y') . '</span> ';

						$reviewer  = \Hubzero\User\Profile::getInstance($row->reviewed_by);
						$reviewed .= $reviewer ? ' <span class="block">' . JText::_('COM_PUBLICATIONS_CURATION_BY')
							. ' ' . $reviewer->get('name') . '</span>' : NULL;
					}

					$class = $row->state == 5 ? 'status-pending' : 'status-wip';

					$abstract  = $row->abstract ? stripslashes($row->abstract) : '';
					?>
					<tr class="mline mini faded" id="tr_<?php echo $row->id; ?>">
						<td><?php echo $row->id; ?></td>
						<td class="pub-image"><img src="<?php echo JRoute::_('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb'; ?>" alt="" /></td>
						<td><?php if ($row->state == 5) { ?><a href="<?php echo JRoute::_($route . '&id=' . $row->id); ?>" <?php if ($abstract) { echo 'title="'.$abstract.'"'; } ?>><?php } ?><?php echo $row->title; ?><?php if ($row->state == 5) { ?></a><?php } ?></td>
						<td>v.<?php echo $row->version_label; ?></td>
						<td><span class="icon <?php echo $row->base; ?>">&nbsp;</span><?php echo $row->base; ?></td>
						<td>
							<span class="block"><?php echo $submitted; ?></span>
							<?php if ($row->reviewed && $row->state == 5 ) { ?>
								<span class="item-updated"></span>
							<?php } ?>
						</td>
						<td><span class="status-icon <?php echo $class; ?>"></span> <span class="status-label"><?php echo $row->state == 5 ? JText::_('COM_PUBLICATIONS_CURATION_STATUS_PENDING') : JText::_('COM_PUBLICATIONS_CURATION_PENDING_AUTHOR_CHANGES'); ?></span></td>
						<td><?php if ($row->state == 5) { ?><a href="<?php echo JRoute::_($route . '&id=' . $row->id); ?>" class="btn icon-next btn-secondary btn-primary" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_OVER_REVIEW'); ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_REVIEW'); ?></a><?php } ?>
							<?php if ($row->state == 7) { echo $reviewed; } ?>
							<a href="<?php echo JRoute::_($route . '&id=' . $row->id . '&task=history') . '?ajax=1&no_html=1'; ?>" class="btn btn-secondary icon-history fancybox" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_OVER_HISTORY'); ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_HISTORY'); ?></a>
							<a href="<?php echo JRoute::_('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number); ?>" class="public-page" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_VIEW_PUB_PAGE'); ?>">&nbsp;</a>
						</td>
					</tr>
					<?php
				}
			?>
		</tbody>
		</table>
</div>
<?php
	$pn = $this->pageNav->getListFooter();
	$pn = str_replace('/?/&amp;','/?',$pn);
	$f = 'task=display';
	foreach ($this->filters as $k=>$v)
	{
		$f .= ($v && ($k == 'tag' || $k == 'category')) ? '&amp;'.$k.'='.$v : '';
	}
	$pn = str_replace('?','?'.$f.'&amp;',$pn);
	echo $pn;
?>
<?php } else { ?>
	<p class="noresults"><?php echo JText::_('COM_PUBLICATIONS_CURATION_NO_RESULTS'); ?></p>
<?php } ?>
</div>
