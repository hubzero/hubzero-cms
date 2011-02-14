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
<h3 class="section-header"><a name="resources"></a><?php echo JText::_('PLG_GROUPS_RESOURCES'); ?></h3>
<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=resources'); ?>">
	<div class="aside">
		<fieldset>
			<label>
				<?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY'); ?>
				<select name="sort">
<?php
		$config =& JComponentHelper::getParams( 'com_resources' );
		if ($config->get('show_ranking')) {
?>
					<option value="ranking"<?php if ($this->sort == 'ranking') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_RANKING'); ?></option>
<?php
		} else {
?>
					<option value="rating"<?php if ($this->sort == 'rating') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_RATING'); ?></option>
<?php
		}
?>
					<option value="date"<?php if ($this->sort == 'date') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_DATE'); ?></option>
					<option value="title"<?php if ($this->sort == 'title') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_TITLE'); ?></option>
				</select>
			</label>
			<label>
				<?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS'); ?>
				<select name="access">
					<option value="all"<?php if ($this->access == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_ALL'); ?></option>
					<option value="public"<?php if ($this->access == 'public') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_PUBLIC'); ?></option>
					<option value="protected"<?php if ($this->access == 'protected') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_PROTECTED'); ?></option>
<?php if ($this->authorized) { ?>
					<option value="private"<?php if ($this->access == 'private') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_PRIVATE'); ?></option>
<?php } ?>
				</select>
			</label>
			<input type="submit" value="<?php echo JText::_('PLG_GROUPS_RESOURCES_GO'); ?>" />
		</fieldset>
<?php
// An array for storing all the links we make
$links = array();
$html = '';

// Loop through each category
foreach ($this->cats as $cat) 
{
	// Only show categories that have returned search results
	if ($cat['total'] > 0) {
		// Is this the active category?
		$a = '';
		if ($cat['category'] == $this->active) {
			$a = ' class="active"';
		}
		// If we have a specific category, prepend it to the search term
		$blob = '';
		if ($cat['category']) {
			$blob = $cat['category'];
		}
		// Build the HTML
		$l = "\t".'<li'.$a.'><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=resources&area='. urlencode(stripslashes($blob))) .'">' . $cat['title'] . ' ('.$cat['total'].')</a>';
		// Are there sub-categories?
		if (isset($cat['_sub']) && is_array($cat['_sub'])) {
			// An array for storing the HTML we make
			$k = array();
			// Loop through each sub-category
			foreach ($cat['_sub'] as $subcat) 
			{
				// Only show sub-categories that returned search results
				if ($subcat['total'] > 0) {
					// Is this the active category?
					$a = '';
					if ($subcat['category'] == $this->active) {
						$a = ' class="active"';
					}
					// If we have a specific category, prepend it to the search term
					$blob = '';
					if ($subcat['category']) {
						$blob = $subcat['category'];
					}
					// Build the HTML
					$k[] = "\t\t\t".'<li'.$a.'><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=resources&area='. urlencode(stripslashes($blob))) .'">' . $subcat['title'] . ' ('.$subcat['total'].')</a></li>';
				}
			}
			// Do we actually have any links?
			// NOTE: this method prevents returning empty list tags "<ul></ul>"
			if (count($k) > 0) {
				$l .= "\t\t".'<ul>'."\n";
				$l .= implode( "\n", $k );
				$l .= "\t\t".'</ul>'."\n";
			}
		}
		$l .= '</li>';
		$links[] = $l;
	}
}
// Do we actually have any links?
// NOTE: this method prevents returning empty list tags "<ul></ul>"
if (count($links) > 0) {
	// Yes - output the necessary HTML
	$html .= '<ul class="sub-nav">'."\n";
	$html .= implode( "\n", $links );
	$html .= '</ul>'."\n";
}
$html .= "\t".'<p class="add"><a href="'.JRoute::_('index.php?option=com_contribute&task=start').'">'.JText::_('PLG_GROUPS_RESOURCES_START_A_CONTRIBUTION').'</a></p>'."\n";
$html .= "\t".'<input type="hidden" name="area" value="'.$this->active.'" />'."\n";
echo $html;
?>
</div><!-- / .aside -->
<div class="subject">
<?php
ximport('Hubzero_View_Helper_Html');

$foundresults = false;
$dopaging = false;
$html = '';
$k = 0;
foreach ($this->results as $category)
{
	$amt = count($category);
	
	if ($amt > 0) {
		$foundresults = true;
		
		$name  = $this->cats[$k]['title'];
		$total = $this->cats[$k]['total'];
		$divid = 'search'.$this->cats[$k]['category'];
		
		// Is this category the active category?
		if (!$this->active || $this->active == $this->cats[$k]['category']) {
			// It is - get some needed info
			$name  = $this->cats[$k]['title'];
			$total = $this->cats[$k]['total'];
			$divid = 'search'.$this->cats[$k]['category'];
			
			if ($this->active == $this->cats[$k]['category']) {
				$dopaging = true;
			}
		} else {
			// It is not - does this category have sub-categories?
			if (isset($this->cats[$k]['_sub']) && is_array($this->cats[$k]['_sub'])) {
				// It does - loop through them and see if one is the active category
				foreach ($this->cats[$k]['_sub'] as $sub) 
				{
					if ($this->active == $sub['category']) {
						// Found an active category
						$name  = $sub['title'];
						$total = $sub['total'];
						$divid = 'search'.$sub['category'];
						
						$dopaging = true;
						break;
					}
				}
			}
		}
		
		$num = ($total > 1) ? JText::sprintf('PLG_GROUPS_RESOURCES_RESULTS', $total) : JText::sprintf('PLG_GROUPS_RESOURCES_RESULT', $total);
	
		// A function for category specific items that may be needed
		// Check if a function exist (using old style plugins)
		$f = 'plgGroups'.ucfirst($this->cats[$k]['category']).'Doc';
		if (function_exists($f)) {
			$f();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgGroups'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'documents')) {
			$html .= call_user_func( array($obj,'documents') );
		}
	
		// Build the category HTML
		$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.$name.' <small>'.$num.'</small></h4>'."\n";
		$html .= '<div class="category-wrap" id="'.$divid.'">'."\n";
		
		// Does this category have custom output?
		// Check if a function exist (using old style plugins)
		$func = 'plgGroups'.ucfirst($this->cats[$k]['category']).'Before';
		if (function_exists($func)) {
			$html .= $func();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgGroups'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'before')) {
			$html .= call_user_func( array($obj,'before') );
		}
		
		$html .= '<ol class="search results">'."\n";			
		foreach ($category as $row) 
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);
			
			// Does this category have a unique output display?
			$func = 'plgGroups'.ucfirst($row->section).'Out';
			// Check if a method exist (using JPlugin style)
			$obj = 'plgGroups'.ucfirst($this->cats[$k]['category']);
			
			if (function_exists($func)) {
				$html .= $func( $row, $this->authorized );
			} elseif (method_exists($obj, 'out')) {
				$html .= call_user_func( array($obj,'out'), $row, $this->authorized );
			} else {
				$html .= "\t".'<li>'."\n";
				$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
				if ($row->text) {
					$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(stripslashes($row->text))."\n";
				}
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
		// Initiate paging if we we're displaying an active category
		if ($dopaging) {
			jimport('joomla.html.pagination');
			$pageNav = new JPagination( $this->total, $this->limitstart, $this->limit );

			$pf = $pageNav->getListFooter();
			
			$nm = str_replace('com_','',$this->option);

			$pf = str_replace($nm.'/?',$nm.'/'.$this->group->get('cn').'/'.$this->active.'/?',$pf);
			$html .= $pf;
		} else {
			$html .= '<p class="moreresults">'.JText::sprintf('PLG_GROUPS_RESOURCES_NUMBER_SHOWN', $amt);
			// Ad a "more" link if necessary
			if ($totals[$k] > 5) {
				$qs = 'area='.urlencode(strToLower($this->cats[$k]['category']));
				$seff = JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=resources');
				if (strstr( $seff, 'index' )) {
					$seff .= '&amp;'.$qs;
				} else {
					$seff .= '?'.$qs;
				}
				$html .= ' | <a href="'.$seff.'">'.JText::_('PLG_GROUPS_RESOURCES_MORE').'</a>';
			}
		}
		$html .= '</p>'."\n\n";
		$html .= '</div><!-- / #'.$divid.' -->'."\n";
	}
	$k++;
}
echo $html;
if (!$foundresults) {
	echo Hubzero_View_Helper_Html::warning( JText::_('PLG_GROUPS_RESOURCES_NONE') );
}
?>
</div><!-- / .subject -->
<div class="clear"></div>
</form>