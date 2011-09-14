<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$searchword = ($this->active && $this->active != 'all') ? $this->active.':'.$this->keyword : $this->keyword;
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="get">
		<div class="aside">
			<fieldset>
				<legend><?php echo JText::_('COM_XSEARCH_SEARCH'); ?></legend>
				<label>
					<?php echo JText::_('COM_XSEARCH_KEYWORDS'); ?>
					<input type="text" name="searchword" size="25" value="<?php echo htmlentities(utf8_encode(stripslashes($searchword)),ENT_COMPAT,'UTF-8'); ?>" />
				</label>
				<input type="submit" value="<?php echo JText::_('COM_XSEARCH_SEARCH_AGAIN'); ?>" />
			</fieldset>
<?php
// Add the "all" category
$all = array('category'=>'all','title'=>JText::_('COM_XSEARCH_ALL_CATEGORIES'),'total'=>$this->total);

array_unshift($this->cats, $all);

// An array for storing all the links we make
$links = array();

// Loop through each category
foreach ($this->cats as $cat)
{
	// Only show categories that have returned search results
	if ($cat['total'] > 0) {
		// If we have a specific category, prepend it to the search term
		if ($cat['category'] && $cat['category'] != 'all') {
			$blob = $cat['category'] .':'. $this->keyword;
		} else {
			$blob = $this->keyword;
		}

		// Is this the active category?
		$a = '';
		if ($cat['category'] == $this->active) {
			$a = ' class="active"';

			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			$pathway->addItem($cat['title'],'index.php?option='.$this->option.'&searchword='. urlencode(stripslashes($blob)));
		}

		// Build the HTML
		$l = "\t".'<li'.$a.'><a href="/search/?searchword='. urlencode(stripslashes($blob)) .'">' . JText::_($cat['title']) . ' ('.$cat['total'].')</a>';
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
					if ($subcat['category']) {
						$blob = $subcat['category'] .':'. $this->keyword;
					} else {
						$blob = $searchword;
					}

					// Is this the active category?
					$a = '';
					if ($subcat['category'] == $this->active) {
						$a = ' class="active"';

						$app =& JFactory::getApplication();
						$pathway =& $app->getPathway();
						$pathway->addItem($subcat['title'],'index.php?option='.$this->option.'&searchword='. urlencode(stripslashes($blob)));
					}

					// Build the HTML
					$k[] = "\t\t\t".'<li'.$a.'><a href="/search/?searchword='. urlencode(stripslashes($blob)) .'">' . JText::_($subcat['title']) . ' ('.$subcat['total'].')</a></li>';
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
echo $html;
?>
		</div><!-- / .aside -->
		<div class="subject">
			<h3><?php echo JText::_('COM_XSEARCH_SEARCH_FOR').' '.$this->keyword; ?></h3>
<?php
$juri =& JURI::getInstance();
$foundresults = false;
$dopaging = true;
$name = '';
$divid = '';
$html = '';
$k = 1;
foreach ($this->results as $category)
{
	$amt = count($category);

	if ($amt > 0) {
		$foundresults = true;

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
			$total = $this->cats[$k]['total'];

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

		$name  = ($name) ? $name : JText::_('COM_XSEARCH_ALL_CATEGORIES');
		$divid = ($divid) ? $divid : 'searchall';

		$num = ($total > 1) ? JText::sprintf('COM_XSEARCH_RESULTS', $total) : JText::sprintf('COM_XSEARCH_RESULT', $total);

		// A function for category specific items that may be needed
		// Check if a function exist (using old style plugins)
		$f = 'plgXSearch'.ucfirst($this->cats[$k]['category']).'Doc';
		if (function_exists($f)) {
			$f();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgXSearch'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'documents')) {
			$html .= call_user_func( array($obj,'documents') );
		}

		// Build the category HTML
		$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.JText::_($name).' <small>'.$num.'</small></h4>'."\n";
		$html .= '<div class="category-wrap" id="'.$divid.'">'."\n";

		// Does this category have custom output?
		// Check if a function exist (using old style plugins)
		$func = 'plgXSearch'.ucfirst($this->cats[$k]['category']).'Before';
		if (function_exists($func)) {
			$html .= $func( $this->keyword );
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgXSearch'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'before')) {
			$html .= call_user_func( array($obj,'before'), $this->keyword );
		}

		$html .= '<ol class="search results">'."\n";
		foreach ($category as $row)
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);

			// Does this category have a unique output display?
			$func = 'plgXSearch'.ucfirst($row->section).'Out';
			// Check if a method exist (using JPlugin style)
			$obj = 'plgXSearch'.ucfirst($row->section); //ucfirst($cats[$k]['category']);

			if (function_exists($func)) {
				$html .= $func( $row, $this->keyword );
			} elseif (method_exists($obj, 'out')) {
				$html .= call_user_func( array($obj,'out'), $row, $this->keyword );
			} else {
				if (strstr( $row->href, 'index.php' )) {
					$row->href = JRoute::_($row->href);
				}
				if (substr($row->href,0,1) == '/') {
					$row->href = substr($row->href,1,strlen($row->href));
				}

				$html .= "\t".'<li>'."\n";
				$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
				if ($row->itext) {
					$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
				} else if ($row->ftext) {
					$html .= "\t\t".'<p>&#133; '.stripslashes($row->ftext).' &#133;</p>'."\n";
				}
				$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
		// Initiate paging if we we're displaying an active category
		if ($dopaging) {
			jimport('joomla.html.pagination');
			$pageNav = new JPagination( $total, $this->start, $this->limit );

			$pgn = $pageNav->getListFooter();

			$qs_bad = 'searchword='.urlencode(strToLower($this->active).':').'&';
			$qs_good = 'searchword='.urlencode(strToLower($this->active).':'.$this->keyword).'&';

			$qs_bad2 = 'searchword='.urlencode(strToLower($this->active).':').'"';
			$qs_good2 = 'searchword='.urlencode(strToLower($this->active).':'.$this->keyword).'"';

			$pgn = str_replace('%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C%5C', '',$pgn);
			$pgn = str_replace($qs_bad,$qs_good,$pgn);
			$pgn = str_replace($qs_bad2,$qs_good2,$pgn);
			$pgn = str_replace('&amp;&amp;','&amp;',$pgn);
			$pgn = str_replace('/xsearch/','/search/',$pgn);
			$html .= $pgn;
		} else {
			$html .= '<p class="moreresults">'.JText::sprintf('COM_XSEARCH_TOP_SHOWN', $amt);
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
				$sef = JRoute::_( 'index.php?option='.$this->option );

				$qs = 'searchword='.urlencode(strToLower($this->cats[$k]['category']).':'.stripslashes($this->keyword));
				if (strstr( $sef, 'index' )) {
					$sef .= '&amp;'.$qs;
				} else {
					$sef .= '?'.$qs;
				}
				$html .= ' | <a href="'.$sef.'">'.JText::_('COM_XSEARCH_SEE_MORE_RESULTS').'</a>';
			}
			$html .= '</p>'."\n\n";
		}

		// Does this category have custom output?
		// Check if a function exist (using old style plugins)
		$func = 'plgXSearch'.ucfirst($this->cats[$k]['category']).'After';
		if (function_exists($func)) {
			$html .= $func( $this->keyword );
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgXSearch'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'after')) {
			$html .= call_user_func( array($obj,'after'), $this->keyword );
		}

		$html .= '</div><!-- / #'.$divid.' -->'."\n";
	}
	$k++;
}
echo $html;
if (!$foundresults) {
	echo '<p class="warning">'. JText::_('COM_XSEARCH_NO_RESULTS') .'</p>';
}
?>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->

