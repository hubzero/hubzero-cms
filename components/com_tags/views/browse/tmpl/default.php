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

$this->filters['sort'] = '';
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" method="post">
	<div class="main section">
		<div class="aside">
			<p class="help"><strong><?php echo JText::_('COM_TAGS_WHATS_AN_ALIAS'); ?></strong><br /><?php echo JText::_('COM_TAGS_ALIAS_EXPLANATION'); ?></p>
		</div><!-- / .aside -->
		<div class="subject">
			
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<label for="entry-search-text">
						<?php echo JText::_('COM_TAGS_SEARCH_TAGS'); ?>
					</label>
					<input type="text" name="search" id="entry-search-text" value="<?php echo htmlentities($this->filters['search'],ENT_COMPAT,'UTF-8'); ?>" />					
				</fieldset>
			</div><!-- / .container -->
			
			<div class="container">
				<ul class="entries-menu">
					<li><a<?php echo ($this->filters['sortby'] == 'total') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=total&search='.urlencode($this->filters['search'])); ?>" title="Sort by popularity">&darr; <?php echo JText::_('Popular'); ?></a></li>
					<li><a<?php echo ($this->filters['sortby'] == '') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&search='.urlencode($this->filters['search'])); ?>" title="Sort by title">&darr; <?php echo JText::_('Alphabetically'); ?></a></li>
				</ul>
				
				<table class="entries" id="taglist" summary="<?php echo JText::_('COM_TAGS_TABLE_SUMMARY'); ?>">
<?php
					$s = $this->filters['start']+1;
					$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
					//$e = ($this->filters['limit'] > $this->total) ? $this->filters['start'] + $this->filters['limit'] : $this->filters['start'] + $this->filters['limit'];
?>

					<thead>
						<tr>
<?php if ($this->authorized) { ?>
							<th colspan="2"><?php echo JText::_('COM_TAGS_COL_ACTION'); ?></th>
<?php } ?>
							<th>
								<?php
													if ($this->filters['search'] != '') {
														echo 'Search for "'.$this->filters['search'].'" in ';
													}
								?>
								<?php echo JText::_('COM_TAGS'); ?>
								<span>(<?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?>)</span>
							</th>
							<th><?php echo JText::_('COM_TAGS_COL_ALIAS'); ?></th>
							<th><?php echo JText::_('COM_TAGS_COL_NUMBER_TAGGED'); ?></th>
						</tr>
					</thead>
					
					<tbody>
<?php
if ($this->rows) {
	$database =& JFactory::getDBO();
	$to = new TagsObject( $database );

	$k = 0;
	$cls = 'even';
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
	{
		$row = &$this->rows[$i];
		$now = date( "Y-m-d H:i:s" );

		//$total = $to->getCount( $row->id );

		//$cls = ($cls == 'even') ? 'odd' : 'even';
?>
						<tr<?php if ($row->admin) { echo ' class="admin"'; } ?>>
<?php if ($this->authorized) { ?>
							<td><a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete&id[]='.$row->id); ?>"><?php echo JText::_('COM_TAGS_DELETE_TAG'); ?></a></td>
							<td><a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$row->id); ?>" title="<?php echo JText::_('COM_TAGS_EDIT_TAG'); ?> &quot;<?php echo stripslashes($row->raw_tag); ?>&quot;"><?php echo JText::_('COM_TAGS_EDIT'); ?></a></td>
<?php } ?>
							<td><a class="entry-title" href="<?php echo JRoute::_('index.php?option='.$this->option.'&tag='.$row->tag); ?>"><?php echo stripslashes($row->raw_tag); ?></a></td>
							<td><?php echo $row->alias; ?></td>
							<td><?php echo $row->total; ?></td>
						</tr>
<?php
		$k = 1 - $k;
	}
}
?>
					</tbody>
				</table>
				<?php
					$pn = $this->pageNav->getListFooter();
					$pn = str_replace('/?','/?task=browse&amp;',$pn);
					$pn = str_replace('task=browse&amp;task=browse','task=browse',$pn);
					$pn = str_replace('&amp;&amp;','&amp;',$pn);
					$pn = str_replace('view=browse','task=browse',$pn);
					$pn = str_replace('/?task=browse&amp;','/browse?search='.urlencode($this->filters['search']).'&amp;',$pn);
					$pn = str_replace('?search='.urlencode($this->filters['search']).'&amp;search='.urlencode($this->filters['search']).'&amp;','?search='.urlencode($this->filters['search']).'&amp;',$pn);
					
					echo $pn;
				?>
				<div class="clearfix"></div>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="browse" />
			</div><!-- / .container -->
		</div><!-- / .main subject -->
		<div class="clear"></div>
	</div><!-- / .main section -->
</form>