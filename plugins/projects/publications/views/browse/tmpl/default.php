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
$route 	= 'index.php?option=' . $this->option . a . 'alias=' . $this->project->alias . a . '&active=publications';
$url 	= JRoute::_($route);

// Check used space against quota (percentage)
$inuse = round(($this->dirsize * 100 ) / $this->quota);
if ($inuse < 1) 
{
	$inuse = round((($this->dirsize * 100 ) / $this->quota), 1);
	if ($inuse < 0.1) 
	{
		$inuse = 0.0;
	}
}
$inuse = ($inuse > 100) ? 100 : $inuse;
$approachingQuota = $this->config->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

$showStats = false;

?>
<form action="<?php echo $url; ?>" method="post" id="plg-form" >	
	<div id="plg-header">
		<h3 class="publications"><?php echo $this->title; ?></h3>
	</div>
	<?php 
	if(count($this->rows) > 0 ) {
	?>
	<div class="list-editing"><p><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_SHOWING')); ?> <?php if($this->total <= count($this->rows)) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_ALL'); }?> <span class="prominent"> <?php echo count($this->rows); ?></span> <?php if($this->total > count($this->rows)) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_OUT_OF').' '.$this->total; }?> <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS_S'); ?>
		<span class="editlink addnew"><a href="<?php echo $url . '/?action=start'; ?>" ><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?></a></span></p>
		</div>
		<table id="filelist" class="listing">
			<thead>
				<tr>
					<th class="thtype<?php if($this->filters['sortby'] == 'category') { echo ' activesort'; } ?>"><a href="<?php echo  $url . '/?t_sortby=category' . a . 't_sortdir=' . $sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_CATEGORY'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CATEGORY'); ?></a></th>
					<th<?php if($this->filters['sortby'] == 'title') { echo ' class="activesort"'; } ?>><a href="<?php echo $url . '/?t_sortby=title'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?></a></th>
					<th class="thtype<?php if($this->filters['sortby'] == 'id') { echo ' activesort'; } ?>"><a href="<?php echo $url . '/?t_sortby=id'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('ID'); ?>"><?php echo JText::_('ID'); ?></a></th>
					<th class="thtype<?php if($this->filters['sortby'] == 'type') { echo ' activesort'; } ?>"><a href="<?php echo $url . '/?t_sortby=type'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_TYPE'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_TYPE'); ?></a></th>
					<th></th>
					<th<?php if($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?> colspan="2"><a href="<?php echo $url . '/?t_sortby=status'.a.'t_sortdir='.$sortbyDir; ?>" class="re_sort" title="<?php echo JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?></a></th>
					<th class="condensed centeralign"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_RELEASES'); ?></th>
				</tr>
			</thead>
			<tbody>
	<?php 
		foreach ($this->rows as $row) {	
				// What's the publication status?
				$status = PublicationHelper::getPubStateProperty($row, 'status', 0);
				$class 	= PublicationHelper::getPubStateProperty($row, 'class');
				$date 	= PublicationHelper::getPubStateProperty($row, 'date');
				
				// Normalize type title
				$cat_name = PublicationHelper::writePubCategory($row->cat_alias, $row->cat_name);
				
				$abstract = $row->abstract ? stripslashes($row->abstract) : '';
				
				if ($row->state == 1)
				{
					$showStats = true;
				}
				
			?>
			<tr class="mline" id="tr_<?php echo $row->id; ?>">
				<td class="restype"><?php echo $cat_name; ?></td>
				<td><a href="<?php echo $url . '/?pid='.$row->id; ?>" <?php if($abstract) { echo 'title="'.$abstract.'"'; } ?>><?php echo $row->title; ?></a></td>
				<td class="mini faded"><?php echo $row->id; ?></td>
				<td class="restype"><?php echo $row->base; ?></td>
				<td class="version_label">v.<?php echo $row->version_label; ?></td>
				<td class="showstatus">
					<span class="<?php echo $class; ?> major_status"><?php echo $status; ?></span>
					<span class="mini faded block"><?php echo $date; ?></span>
				</td>
				<td class="mini faded"><?php if($row->dev_version_label && $row->dev_version_label != $row->version_label) 
				{ echo '<a href="'. $url . '/?pid='.$row->id.a.'version=dev'
				.'">&raquo; '. JText::_('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_DRAFT')
				.' <strong>'.$row->dev_version_label.'</strong></a> '
				.JText::_('PLG_PROJECTS_PUBLICATIONS_IN_PROGRESS'); } ?></td>
				<td class="centeralign mini faded"><?php if ($row->versions > 0) { ?><a href="<?php echo $url . '/?pid='.$row->id.a.'action=versions'; ?>" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_VERSIONS'); ?>"><?php } ?><?php echo $row->versions; ?><?php if ($row->versions > 0) { ?></a><?php } ?></td>
			</tr>
			<?php 
		}
	?>
			</tbody>
		</table>
			<div class="nav_pager"><p>
				<?php 
				if($this->filters['start'] == 0) {	?>
					<span>&laquo; <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PREVIOUS'); ?></span>
				<?php	} else {  ?>
					<a href="<?php echo $url . '/?t_sortby='.$this->filters['sortby'].a.'limitstart='.$prev_start.a.'t_sortdir='.$this->filters['sortdir']; ?>" class="ajax_action">&laquo; <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PREVIOUS'); ?></a>
				<?php } ?><span>&nbsp; | &nbsp;</span>
				<?php 
				if( $whatsleft <= 0 or $this->filters['limit'] == 0 ) { ?>
					<span><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NEXT'); ?> &raquo;</span>
				<?php	} else { ?>
					<a href="<?php echo $url . '/?t_sortby='.$this->filters['sortby'].a.'limitstart='.$next_start.a.'t_sortdir='.$this->filters['sortdir']; ?>" class="ajax_action"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NEXT'); ?> &raquo;</a>
				<?php } ?></p>
			</div>
			
			<p class="extras">
				<span class="leftfloat">
				<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE'); ?>
				<a href="<?php echo $url . '/?action=diskspace'; ?>" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_TOOLTIP'); ?>"><span id="indicator-wrapper" <?php if ($warning) { echo 'class="quota-warning"'; } ?>><span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse.'% '.JText::_('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_USED'); ?></span></span></span></a>
					 <span class="show-quota"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_QUOTA') . ': ' . ProjectsHtml::formatSize($this->quota); ?></span>
				</span>
			</p>
			<?php if ($showStats) { ?>	
			<p class="viewallstats"><a href="<?php echo $url . '?action=stats'; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_USAGE_STATS'); ?> &raquo;</a></p>
			<?php } ?>
	<?php 
	}
	else {
		echo ('<p class="noresults">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NO_PUBS_FOUND').' <span class="addnew"><a href="'. $url .'/?action=start"  >'.JText::_('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION').'</a></span></p>');
		
			// Show intro banner with publication steps	
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'publications',
					'name'=>'browse',
					'layout'=>'intro'
				)
			);
			$view->option 	= $this->option;
			$view->project 	= $this->project;
			$view->choices 	= $this->choices;
			$view->goto 	= '&alias=' . $this->project->alias;
			echo $view->loadTemplate();
	} ?>
</form>