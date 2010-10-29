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
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="tag" href="<?php echo JRoute::_('index.php?option='.$this->option); ?>"><?php echo JText::_('COM_TAGS_MORE_TAGS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=view'); //.'&tag='.$this->tagstring); ?>" method="get">
<?php
if (count($this->tags) == 1) {
	$tagobj = $this->tags[0];
	if ($tagobj->description != '') {
		//$tagobj->description = Hubzero_View_Helper_Html::xhtml($tagobj->description);
?>
		<h3><?php echo JText::_('COM_TAGS_DESCRIPTION'); ?></h3>
		<div class="tag-description">
			<?php echo stripslashes($tagobj->description); ?>
			<div class="clear"></div>
		</div>
<?php
	}
}
?>
	<div class="aside">
		<fieldset>
<?php
JPluginHelper::importPlugin( 'tageditor' );
$dispatcher =& JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tag','actags','',$this->search,'')) );
?>
			<label>
				<?php echo JText::_('COM_TAGS_SEARCH_WITH_TAGS'); ?>
<?php if (count($tf) > 0) {
			echo $tf[0];
} else { ?>
				<input type="text" name="tag" value="<?php echo $this->search; ?>" />
<?php } ?>
			</label>
			
			<label>
				<?php echo JText::_('COM_TAGS_SORT_BY'); ?>
				<select name="sort">
					<option value=""<?php if ($this->sort == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_TAGS_OPT_SELECT'); ?></option>
					<option value="title"<?php if ($this->sort == 'title') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_TAGS_OPT_TITLE'); ?></option>
					<option value="date"<?php if ($this->sort == 'date') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_TAGS_OPT_DATE'); ?></option>
				</select>
			</label>
			<input type="submit" value="<?php echo JText::_('COM_TAGS_GO'); ?>" />
		</fieldset>
		<?php
		// Add the "all" category
		$all = array('category'=>'','title'=>JText::_('COM_TAGS_ALL_CATEGORIES'),'total'=>$this->total);
		$cats = $this->cats;
		array_unshift($cats, $all);

		// An array for storing all the links we make
		$links = array();

		// Loop through each category
		foreach ($cats as $cat) 
		{
			// Only show categories that have returned search results
			if ($cat['total'] > 0) {
				// If we have a specific category, prepend it to the search term
				$blob = '';
				if ($cat['category']) {
					$blob = $cat['category'];
				}
				
				$url  = 'index.php?option='.$this->option.'&tag='.$this->tagstring;
				$url .= ($blob) ? '&area='. stripslashes($blob) : '';
				$sef = JRoute::_($url);
				$sef = str_replace('%20','+',$sef);
				
				// Is this the active category?
				$a = '';
				if ($cat['category'] == $this->active) {
					$a = ' class="active"';
					
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat['title'],'index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='. stripslashes($blob));
				}
				
				// Build the HTML
				$l = "\t".'<li'.$a.'><a href="'.$sef.'">' . $cat['title'] . ' ('.$cat['total'].')</a>';
				// Are there sub-categories?
				if (isset($cat['_sub']) && is_array($cat['_sub'])) {
					// An array for storing the HTML we make
					$k = array();
					// Loop through each sub-category
					foreach ($cat['_sub'] as $subcat) 
					{
						// Only show sub-categories that returned search results
						if ($subcat['total'] > 0) {
							// If we have a specific category, prepend it to the search term
							$blob = '';
							if ($subcat['category']) {
								$blob = $subcat['category'];
							}
							
							// Is this the active category?
							$a = '';
							if ($subcat['category'] == $this->active) {
								$a = ' class="active"';
								
								$app =& JFactory::getApplication();
								$pathway =& $app->getPathway();
								$pathway->addItem($subcat['title'],'index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='. stripslashes($blob));
							}
							
							// Build the HTML
							$sef = JRoute::_('index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='. stripslashes($blob));
							$sef = str_replace('%20','+',$sef);
							$k[] = "\t\t\t".'<li'.$a.'><a href="'.$sef.'">' . $subcat['title'] . ' ('.$subcat['total'].')</a></li>';
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
			$html  = '<ul class="sub-nav">'."\n";
			$html .= implode( "\n", $links );
			$html .= '</ul>'."\n";
		} else {
			// No - nothing to output
			$html = '';
		}
		$html .= "\t".'<input type="hidden" name="area" value="'.$this->active.'" />'."\n";

		echo $html;
		?>
	</div><!-- / .aside -->
	<div class="subject">
<?php
$juri =& JURI::getInstance();
$foundresults = false;
$dopaging = false;
$cats = $this->cats;
$jconfig =& JFactory::getConfig();
$html = '';
$k = 0;
foreach ($this->results as $category)
{
	$amt = count($category);
	
	if ($amt > 0) {
		$foundresults = true;
		
		$name  = $cats[$k]['title'];
		$total = $cats[$k]['total'];
		$divid = 'search'.$cats[$k]['category'];
		
		// Is this category the active category?
		if (!$this->active || $this->active == $cats[$k]['category']) {
			// It is - get some needed info
			$name  = $cats[$k]['title'];
			$total = $cats[$k]['total'];
			$divid = 'search'.$cats[$k]['category'];
			
			if ($this->active == $cats[$k]['category']) {
				$dopaging = true;
			}
		} else {
			// It is not - does this category have sub-categories?
			if (isset($cats[$k]['_sub']) && is_array($cats[$k]['_sub'])) {
				// It does - loop through them and see if one is the active category
				foreach ($cats[$k]['_sub'] as $sub) 
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
		
		$num  = $total .' ';
		$num .= ($total > 1) ? JText::_('COM_TAGS_RESULTS') : JText::_('COM_TAGS_RESULT');
	
		// A function for category specific items that may be needed
		$f = 'plgTags'.ucfirst($cats[$k]['category']).'Doc';
		if (function_exists($f)) {
			$f();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgTags'.ucfirst($cats[$k]['category']);
		if (method_exists($obj, 'documents')) {
			$html .= call_user_func( array($obj,'documents') );
		}
	
		$feed = JRoute::_('index.php?option='.$this->option.'&task=feed.rss&tag='.$this->tagstring.'&area='.$cats[$k]['category']);
		if (substr($feed, 0, 4) != 'http') {
			if (substr($feed, 0, 1) != DS) {
				$feed = DS.$feed;
			}
			$feed = $jconfig->getValue('config.live_site').$feed;
		}
		$feed = str_replace('https:://','http://',$feed);
	
		// Build the category HTML
		$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.$name.' <small>'.$num.' (<a class="feed" href="'.$feed.'">'.JText::_('COM_TAGS_FEED').'</a>)</small></h4>'."\n";
		$html .= '<div class="category-wrap" id="'.$divid.'">'."\n";
		$html .= '<ol class="search results">'."\n";			
		foreach ($category as $row) 
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);
			
			// Does this category have a unique output display?
			$func = 'plgTags'.ucfirst($row->section).'Out';
			// Check if a method exist (using JPlugin style)
			$obj = 'plgTags'.ucfirst($cats[$k]['category']);
			
			if (function_exists($func)) {
				$html .= $func( $row );
			} elseif (method_exists($obj, 'out')) {
				$html .= call_user_func( array($obj,'out'), $row );
			} else {
				if (strstr( $row->href, 'index.php' )) {
					$row->href = JRoute::_($row->href);
				}
				if (substr($row->href,0,1) == '/') {
					$row->href = substr($row->href,1,strlen($row->href));
				}
				
				$html .= "\t".'<li>'."\n";
				$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.Hubzero_View_Helper_Html::purifyText($row->title).'</a></p>'."\n";
				if ($row->text) {
					$row->text = strip_tags($row->text);
					$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText($row->text), 200)."\n";
				}
				$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
		// Initiate paging if we we're displaying an active category
		if ($dopaging) {
			jimport('joomla.html.pagination');
			$pageNav = new JPagination( $this->total, $this->limitstart, $this->limit );

			//$html .= $pageNav->getListFooter();
			$pf = $pageNav->getListFooter();
			
			$nm = str_replace('com_','',$this->option);

			$pf = str_replace($nm.'/?',$nm.'/'.$this->tagstring.'/'.$this->active.'/?',$pf);
			$pf = str_replace('%20','+',$pf);
			$html .= $pf;
		} else {
			$html .= '<p class="moreresults">'.JText::sprintf('COM_TAGS_TOTAL_RESULTS_FOUND',$amt);
			// Add a "more" link if necessary
			$ttl = 0;
			if (is_array($this->totals[$k])) {
				foreach ($this->totals[$k] as $t) 
				{
					$ttl += $t;
				}
			} else {
				$ttl = $this->totals[$k];
			}
			if ($ttl > 5) {
				$sef = JRoute::_('index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='. urlencode(stripslashes($cats[$k]['category'])));
				$html .= ' | <a href="'.$sef.'">'.JText::_('COM_TAGS_SEE_MORE_RESULTS').'</a>';
			}
		}
		$html .= '</p>'."\n\n";
		$html .= '</div><!-- / #'.$divid.' -->'."\n";
	}
	$k++;
}
if (!$foundresults) {
	$html .= Hubzero_View_Helper_Html::warning( JText::_('COM_TAGS_NO_RESULTS') );
}
echo $html;
?>
	</div><!-- / .subject -->
	<div class="clear"></div>
	</form>
</div><!-- / .main section -->