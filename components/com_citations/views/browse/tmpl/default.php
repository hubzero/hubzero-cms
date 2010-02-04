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

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<?php /*<div id="content-header-extra">
	<p><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add'); ?>"><?php echo JText::_('Add a citation'); ?></a></p>
</div><!-- / #content-header-extra -->*/ ?>

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" id="citeform" method="post">
		<div class="aside">
			<fieldset>
				<label>
					<?php echo JText::_('Type'); ?>
					<?php echo CitationsHtml::select('type',$this->types,$this->filters['type']); ?>
				</label>
				
				<label>
					<?php echo JText::_('FILTER'); ?>
					<?php echo CitationsHtml::select('filter',$this->filter,$this->filters['filter']); ?>
				</label>
				
				<label>
					<?php echo JText::_('FOR_YEAR'); ?>
					<select name="year">
						<option value=""<?php if ($this->filters['year'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('All'); ?></option>
<?php
	$y = date("Y");
	$y++;
	for ($i=1995, $n=$y; $i < $n; $i++) 
	{
?>
						<option value="<?php echo $i; ?>"<?php if ($this->filters['year'] == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
<?php
	}
?>
					</select>
				</label>
				
				<label>
					<?php echo JText::_('SORT_BY'); ?>
					<?php echo CitationsHtml::select('sort',$this->sorts,$this->filters['sort']); ?>
				</label>
				
				<label>
					<?php echo JText::_('SEARCH_TITLE'); ?>
					<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
				</label>
				
				<input type="submit" name="go" value="<?php echo JText::_('GO'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="browse" />
			</fieldset>
		</div><!-- / .aside -->
		<div class="subject">
<?php
	if (count($this->citations) > 0) {
		$formatter = new CitationsFormat;
		$formatter->setFormat($this->format);
		
		$html = '<ul class="citations results">'."\n";
		foreach ($this->citations as $cite) 
		{
			// Get the associations
			$assoc = new CitationsAssociation( $this->database );
			$assocs = $assoc->getRecords( array('cid'=>$cite->id) );
			
			$html .= ' <li>'."\n";
			//$html .= CitationsFormatter::formatReference( $cite, $cite->url );
			$html .= $formatter->formatReference($cite, $this->filters['search']);
			$html .= "\t".'<p class="details">'."\n";
			$html .= "\t\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&task=download&id='.$cite->id.'&format=bibtex&no_html=1').'" title="'.JText::_('DOWNLOAD_BIBTEX').'">BibTex</a> <span>|</span> '."\n";
			$html .= "\t\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&task=download&id='.$cite->id.'&format=endnote&no_html=1').'" title="'.JText::_('DOWNLOAD_ENDNOTE').'">EndNote</a>'."\n";
			if (count($assocs) > 0 || $cite->eprint) {
				if (count($assocs) > 0) {
					if (count($assocs) > 1) {
						$html .= "\t\t".' <span>|</span> '.JText::_('RESOURCES_CITED').': '."\n";
						$k = 0;
						$rrs = array();
						foreach ($assocs as $rid) 
						{
							if ($rid->table == 'resource') {
								$this->database->setQuery( "SELECT published FROM #__resources WHERE id=".$rid->oid );
								$state = $this->database->loadResult();
								if ($state == 1) {
									$k++;
									$rrs[] = '<a href="'.JRoute::_('index.php?option=com_resources&id='.$rid->oid).'">['.$k.']</a>';
								}
							}
						}
						$html .= "\t\t".implode(', ',$rrs)."\n";
					} else {
						if ($assocs[0]->table == 'resource') {
							$this->database->setQuery( "SELECT published FROM #__resources WHERE id=".$assocs[0]->oid );
							$state = $this->database->loadResult();
							if ($state == 1) {
								$html .= "\t\t".' <span>|</span> <a href="'.JRoute::_('index.php?option=com_resources&id='.$assocs[0]->oid).'">'.JText::_('RESOURCE_CITED').'</a>'."\n";
							}
						}
					}
				}
				if ($cite->eprint) {
					$html .= "\t\t".' <span>|</span> <a href="'.Hubzero_View_Helper_Html::ampReplace($cite->eprint).'">'.JText::_('ELECTRONIC_PAPER').'</a>'."\n";
				}
			}
			$html .= "\t".'</p>'."\n";
			$html .= ' </li>'."\n";
		}
		$html .= '</ul>'."\n";
		echo $html;
		
		$qs = '';
		foreach ($this->filters as $key=>$value) 
		{
			$qs .= ($key != 'limit' && $key != 'start') ? $key.'='.$value.'&' : '';
		}
		$paging = $this->pageNav->getListFooter();
		$paging = str_replace('citations/?','citations/browse?'.$qs,$paging);
		echo $paging;
	} else {
?>
			<p class="warning"><?php echo JText::_('NO_CITATIONS_FOUND'); ?></p>
<?php
	}
?>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->