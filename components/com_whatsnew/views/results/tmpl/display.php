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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="get">
		<div class="aside">
			<fieldset>
				<legend><?php echo JText::_('COM_WHATSNEW_FILTER'); ?></legend>
				<label for="period">
					<?php echo JText::_('COM_WHATSNEW_TIME_PERIOD'); ?>
					<?php echo JHTMLSelect::genericlist($this->periodlist, 'period', '', 'value', 'text', $this->period); ?>
				</label>
				<p class="submit"><input type="submit" value="<?php echo JText::_('COM_WHATSNEW_GO'); ?>" /></p>
			</fieldset>
<?php
// Add the "all" category
$all = array(
	'category' => '',
	'title'    => JText::_('COM_WHATSNEW_ALL_CATEGORIES'),
	'total'    => $this->total
);

array_unshift($this->cats, $all);

// An array for storing all the links we make
$links = array();

// Loop through each category
foreach ($this->cats as $cat)
{
	// Only show categories that have returned search results
	if ($cat['total'] > 0) 
	{
		// If we have a specific category, prepend it to the search term
		if ($cat['category']) 
		{
			$blob = $cat['category'] . ':' . $this->period;
		} 
		else 
		{
			$blob = $this->period;
		}

		// Is this the active category?
		$a = '';
		if ($cat['category'] == $this->active) 
		{
			$a = ' class="active"';

			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			$pathway->addItem($cat['title'],'index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob)));
		}

		// Build the HTML
		$l = "\t" . '<li' . $a . '><a href="'.JRoute::_('index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob))) . '">' . $this->escape($cat['title']) . ' <span class="item-count">' . $cat['total'] . '</span></a>';
		// Are there sub-categories?
		if (isset($cat['_sub']) && is_array($cat['_sub'])) 
		{
			// An array for storing the HTML we make
			$k = array();

			$pathway =& JFactory::getApplication()->getPathway();

			// Loop through each sub-category
			foreach ($cat['_sub'] as $subcat)
			{
				// Only show sub-categories that returned search results
				if ($subcat['total'] > 0) 
				{
					// If we have a specific category, prepend it to the search term
					if ($subcat['category']) 
					{
						$blob = $subcat['category'] . ':' . $this->period;
					} 
					else 
					{
						$blob = $this->period;
					}

					// Is this the active category?
					$a = '';
					if ($subcat['category'] == $this->active) 
					{
						$a = ' class="active"';

						$pathway->addItem(
							$subcat['title'], 
							'index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob))
						);
					}

					// Build the HTML
					$k[] = "\t\t\t" . '<li' . $a . '><a href="' . JRoute::_('index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob))) . '">' . $this->escape($subcat['title']) . ' <span class="item-count">' . $subcat['total'] . '</span></a></li>';
				}
			}
			// Do we actually have any links?
			// NOTE: this method prevents returning empty list tags "<ul></ul>"
			if (count($k) > 0) 
			{
				$l .= "\t\t" . '<ul>' . "\n";
				$l .= implode("\n", $k);
				$l .= "\t\t" . '</ul>' . "\n";
			}
		}
		$l .= '</li>';
		$links[] = $l;
	}
}
// Do we actually have any links?
// NOTE: this method prevents returning empty list tags "<ul></ul>"
if (count($links) > 0) { ?>
			<div class="container">
				<ul class="sub-nav">
					<?php echo implode( "\n", $links ); ?>
				</ul>
			</div>
<?php } ?>
			<input type="hidden" name="category" value="<?php echo $this->escape($this->active); ?>" />
		</div><!-- / .aside -->
		<div class="subject">
			<div class="container">
<?php
$jconfig =& JFactory::getConfig();
$juri =& JURI::getInstance();
$foundresults = false;
$dopaging = false;
$html = '';
$k = 1;

foreach ($this->results as $category)
{
	$amt = count($category);

	if ($amt > 0) 
	{
		$foundresults = true;

		// Is this category the active category?
		if (!$this->active || $this->active == $this->cats[$k]['category']) 
		{
			// It is - get some needed info
			$name  = $this->cats[$k]['title'];
			$total = $this->cats[$k]['total'];
			$divid = 'search' . $this->cats[$k]['category'];

			if ($this->active == $this->cats[$k]['category']) 
			{
				$dopaging = true;
			}
		} 
		else 
		{
			// It is not - does this category have sub-categories?
			if (isset($this->cats[$k]['_sub']) && is_array($this->cats[$k]['_sub'])) 
			{
				// It does - loop through them and see if one is the active category
				foreach ($this->cats[$k]['_sub'] as $sub)
				{
					if ($this->active == $sub['category']) 
					{
						// Found an active category
						$name  = $sub['title'];
						$total = $sub['total'];
						$divid = 'search' . $sub['category'];

						$dopaging = true;
						break;
					}
				}
			}
		}

		$num = ($total > 1) ? JText::sprintf('COM_WHATSNEW_RESULTS', $total) : JText::sprintf('COM_WHATSNEW_RESULT', $total);
		$this->total = $num;

		// A function for category specific items that may be needed
		// Check if a function exist (using old style plugins)
		$f = 'plgWhatsnew' . ucfirst($this->cats[$k]['category']) . 'Doc';
		if (function_exists($f)) 
		{
			$f();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgWhatsnew' . ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'documents')) 
		{
			$html .= call_user_func(array($obj, 'documents'));
		}

		$act = ($this->active) ? $this->active : $this->cats[$k]['category'];

		$feed = JRoute::_('index.php?option=' . $this->option . '&task=feed.rss&period=' . urlencode(strToLower($act) . ':' . stripslashes($this->period)));
		if (substr($feed, 0, 4) != 'http') 
		{
			if (substr($feed, 0, 1) != DS) 
			{
				$feed = DS . $feed;
			}
			$jconfig =& JFactory::getConfig();
			$live_site = rtrim(JURI::base(),'/');
				
			$feed = $live_site . $feed;
		}
		$feed = str_replace('https:://', 'http://', $feed);

		// Build the category HTML
		$html .= '<div class="container-block" id="' . $divid . '">' . "\n";
		$html .= '<h3 id="rel-' . $divid . '">' . $this->escape($name) . ' <a class="icon-feed feed" href="' . $feed . '">' . JText::_('COM_WHATSNEW_FEED') . '</a></h3>' . "\n";

		// Does this category have custom output?
		// Check if a function exist (using old style plugins)
		$func = 'plgWhatsnew'.ucfirst($this->cats[$k]['category']).'Before';
		if (function_exists($func)) 
		{
			$html .= $func($this->period);
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgWhatsnew'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'before')) 
		{
			$html .= call_user_func(array($obj,'before'), $this->period);
		}

		$html .= '<ol class="entries">'."\n";
		foreach ($category as $row)
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);

			// Does this category have a unique output display?
			$func = 'plgWhatsnew' . ucfirst($row->section) . 'Out';
			// Check if a method exist (using JPlugin style)
			$obj = 'plgWhatsnew' . ucfirst($this->cats[$k]['category']);

			if (function_exists($func)) 
			{
				$html .= $func($row, $this->period);
			} 
			elseif (method_exists($obj, 'out')) 
			{
				$html .= call_user_func(array($obj,'out'), $row, $this->period);
			} 
			else 
			{
				if (strstr( $row->href, 'index.php' )) 
				{
					$row->href = JRoute::_($row->href);
				}
				if (substr($row->href,0,1) == '/') 
				{
					$row->href = substr($row->href, 1, strlen($row->href));
				}

				$html .= "\t" . '<li>' . "\n";
				$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
				if ($row->text) {
					$html .= "\t\t" . '<p>' . Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->text)), 200, 0) . '</p>' . "\n";
				}
				$html .= "\t\t" . '<p class="href">' . $juri->base() . $row->href . '</p>' . "\n";
				$html .= "\t" . '</li>' . "\n";
			}
		}
		$html .= '</ol>' . "\n";

		// Initiate paging if we we're displaying an active category
		if ($dopaging) 
		{
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(
				$this->total, 
				$this->start, 
				$this->limit
			);

			$pageNav->setAdditionalUrlParam('category', urlencode(strToLower($this->active)));
			$pageNav->setAdditionalUrlParam('period', $this->period);

			$html .= $pageNav->getListFooter();
			$html .= '<div class="clearfix"></div>';
		} 
		else 
		{
			$html .= '<p class="moreresults">' . JText::sprintf('COM_WHATSNEW_TOP_SHOWN', $amt);
			// Add a "more" link if necessary
			$ttl = 0;
			if (isset($this->totals[$k])) 
			{
				if (is_array($this->totals[$k])) 
				{
					foreach ($this->totals[$k] as $t)
					{
						$ttl += $t;
					}
				} 
				else 
				{
					$ttl = $this->totals[$k];
				}
			}
			if ($ttl > 5) 
			{
				$html .= ' | <a href="' . JRoute::_('index.php?option=' . $this->option . '&period=' . urlencode(strToLower($this->cats[$k]['category']) . ':' . stripslashes($this->period))) . '">' . JText::_('COM_WHATSNEW_SEE_MORE_RESULTS') . '</a>';
			}
			$html .= '</p>' . "\n\n";
		}

		// Does this category have custom output?
		// Check if a function exist (using old style plugins)
		$func = 'plgWhatsnew' . ucfirst($this->cats[$k]['category']) . 'After';
		if (function_exists($func)) 
		{
			$html .= $func($this->period);
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgWhatsnew' . ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'after')) 
		{
			$html .= call_user_func(array($obj,'after'), $this->period);
		}

		$html .= '</div><!-- / #' . $divid . ' -->' . "\n";
	}
	$k++;
}
echo $html;
?>
<?php if (!$foundresults) { ?>
				<p class="warning"><?php echo JText::_('COM_WHATSNEW_NO_RESULTS'); ?></p>
<?php } ?>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->

