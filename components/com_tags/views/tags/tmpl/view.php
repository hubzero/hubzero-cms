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

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-tag tag btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
				<?php echo JText::_('COM_TAGS_MORE_TAGS'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); //.'&tag='.$this->tagstring); ?>" method="get">
		
	<div class="aside">
		<div class="container">
		<h3><?php echo JText::_('Categories'); ?></h3>
		<?php
		// Add the "all" category
		$all = array(
			'category' => '',
			'title'    => JText::_('COM_TAGS_ALL_CATEGORIES'),
			'total'    => $this->total
		);
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

				$url  = 'index.php?option=' . $this->option . '&tag=' . $this->tagstring;
				$url .= ($blob) ? '&area=' . stripslashes($blob) : '';
				$url .= ($this->filters['sort']) ? '&sort=' . $this->filters['sort'] : '';
				$sef = JRoute::_($url);
				$sef = str_replace('%20',',',$sef);
				$sef = str_replace(' ',',',$sef);
				$sef = str_replace('+',',',$sef);

				// Is this the active category?
				$a = '';
				if ($cat['category'] == $this->active) {
					$a = ' class="active"';

					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat['title'],'index.php?option=' . $this->option . '&tag=' . $this->tagstring . '&area=' . stripslashes($blob) . '&sort=' . $this->filters['sort']);
				}

				// Build the HTML
				$l = "\t".'<li><a' . $a . ' href="' . $sef . '">' . $this->escape(stripslashes($cat['title'])) . ' <span class="item-count">' . $cat['total'] . '</span></a>';
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
								$pathway->addItem($subcat['title'],'index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='. stripslashes($blob).'&sort='.$this->filters['sort']);
							}

							// Build the HTML
							$sef = JRoute::_('index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='. stripslashes($blob).'&sort='.$this->filters['sort']);
							$sef = str_replace('%20',',',$sef);
							$sef = str_replace(' ',',',$sef);
							$sef = str_replace('+',',',$sef);
							$k[] = "\t\t\t".'<li><a'.$a.' href="'.$sef.'">' . $this->escape(stripslashes($subcat['title'])) . ' <span class="item-count">'.$subcat['total'].'</span></a></li>';
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
			$html  = '<ul>'."\n";
			$html .= implode( "\n", $links );
			$html .= '</ul>'."\n";
		} else {
			// No - nothing to output
			$html = '';
		}
		$html .= "\t" . '<input type="hidden" name="area" value="' . $this->escape($this->active) . '" />' . "\n";

		echo $html;
		?>
			<p>
				<strong>Note:</strong>  <?php echo JText::_('Results do not include pending, unpublished, and some private items.'); ?>
			</p>
		</div>
	</div><!-- / .aside -->
	<div class="subject">
		
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="Search" />
			<fieldset class="entry-search">
<?php
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher =& JDispatcher::getInstance();
			$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tag', 'actags','',$this->search)) );
?>
				<label for="actags">
					<?php echo JText::_('COM_TAGS_SEARCH_WITH_TAGS'); ?>
				</label>
<?php if (count($tf) > 0) {
						echo $tf[0];
} else { ?>
				<input type="text" name="tag" id="actags" value="<?php echo $this->search; ?>" />
<?php } ?>
			</fieldset>
		</div><!-- / .container -->
		
		<?php
		if (count($this->tags) == 1) {
			$tagobj = $this->tags[0];
			if ($tagobj->description != '') {
				//$tagobj->description = Hubzero_View_Helper_Html::xhtml($tagobj->description);
		?>
		<div class="container">
			<div class="container-block">
				<h4><?php echo JText::_('COM_TAGS_DESCRIPTION'); ?></h4>
				<div class="tag-description">
					<?php echo stripslashes($tagobj->description); ?>
					<div class="clearfix"></div>
				</div>
			</div>
		</div><!-- / .container -->
		<?php
			}
		}
		?>
		<div class="container">
			<ul class="entries-menu">
				<li>
					<a<?php echo ($this->filters['sort'] == 'title') ? ' class="active"' : ''; ?> href="<?php 
						$sef = JRoute::_('index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='.$this->active.'&sort=title');
						$sef = str_replace('%20',',',$sef);
						$sef = str_replace(' ',',',$sef);
						$sef = str_replace('+',',',$sef);
						echo $sef;
					?>" title="Sort by title">
						&darr; <?php echo JText::_('COM_TAGS_OPT_TITLE'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['sort'] == 'date' || $this->filters['sort'] == '') ? ' class="active"' : ''; ?> href="<?php 
						$sef = JRoute::_('index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='.$this->active.'&sort=date');
						$sef = str_replace('%20',',',$sef);
						$sef = str_replace(' ',',',$sef);
						$sef = str_replace('+',',',$sef);
						echo $sef;
					?>" title="Sort by newest to oldest">
						&darr; <?php echo JText::_('COM_TAGS_OPT_DATE'); ?>
					</a>
				</li>
<?php /*				<li><a<?php echo ($this->filters['sort'] == '') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='.$this->active); ?>" title="Sort by popularity">&darr; <?php echo JText::_('Popular'); ?></a></li> */ ?>
			</ul>

			<div class="container-block">
<?php
$juri =& JURI::getInstance();
$foundresults = false;
$dopaging = true;
//$cats = $this->cats;
$jconfig =& JFactory::getConfig();
$html = '';
$k = 0;
if ($this->active) {
	$k++;
}
foreach ($this->results as $category)
{
	$amt = count($category);

	if ($amt > 0) {
		$foundresults = true;

		$name  = $cats[$k]['title'];
		$total = $cats[$k]['total'];
		$divid = 'search' . $cats[$k]['category'];

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
			$total = $this->cats[$k]['total'];

			// It is not - does this category have sub-categories?
			if (isset($cats[$k]['_sub']) && is_array($cats[$k]['_sub'])) {
				// It does - loop through them and see if one is the active category
				foreach ($cats[$k]['_sub'] as $sub)
				{
					if ($this->active == $sub['category']) {
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
		$this->total = $total;

		$name  = ($name) ? $name : JText::_('COM_TAGS_ALL_CATEGORIES');
		$divid = ($divid) ? $divid : 'searchall';

		//$num  = $total .' ';
		//$num .= ($total > 1) ? JText::_('COM_TAGS_RESULTS') : JText::_('COM_TAGS_RESULT');

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
			$jconfig =& JFactory::getConfig();
			$live_site = rtrim(JURI::base(),'/');
			$feed = $live_site.$feed;
		}
		$feed = str_replace('https:://','http://',$feed);

		$ttl = 0;
		if (isset($this->totals[$k]) && is_array($this->totals[$k])) {
			foreach ($this->totals[$k] as $t)
			{
				$ttl += $t;
			}
		} else {
			$ttl = (isset($this->totals[$k])) ? $this->totals[$k] : $ttl;
		}
		$ttl = ($ttl > 5) ? 5 : $ttl;

		if (!$dopaging) {
			$num = ($this->filters['start']+1).'-'.$ttl.' of ';
		} else {
			$ttl = ($total > ($this->filters['limit'] + $this->filters['start'])) ? ($this->filters['limit'] + $this->filters['start']) : $total;
			if ($total && !$ttl)
			{
				$ttl = $total;
			}
			$num = ($this->filters['start']+1).'-'.$ttl.' of ';
		}

		// Build the category HTML
		$html .= '<h3 id="rel-'.$divid.'">';
		if (!$dopaging) {
			$html .= '<a href="'.JRoute::_('index.php?option='.$this->option.'&tag='.$this->tagstring.'&area='.$cats[$k]['category']).'" title="'.JText::_('View all items in &quot;'.$name.'&quot;').'">';
		}
		$html .= $this->escape(stripslashes($name)).' <span>('.$num.$total.')</span> ';
		if (!$dopaging) {
			$html .= '<span class="more">&raquo;</span></a> ';
		}
		//if ($this->active) {
			//$html .= '<a class="feed" href="'.$feed.'" title="'.JText::_('COM_TAGS_FEED').'">'.JText::_('COM_TAGS_FEED').'</a>';
		//}
		$html .= '</h3>'."\n";
		$html .= '<div class="category-wrap" id="'.$divid.'">'."\n";
		$html .= '<ol class="search results">'."\n";
		foreach ($category as $row)
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);

			// Does this category have a unique output display?
			$func = 'plgTags'.ucfirst($row->section).'Out';
			// Check if a method exist (using JPlugin style)
			$obj = 'plgTags'.ucfirst($row->section); //ucfirst($cats[$k]['category']);

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
				if ($row->ftext) {
					$row->ftext = strip_tags($row->ftext);
					$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText($row->ftext), 200)."\n";
				}
				$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
		// Initiate paging if we we're displaying an active category
		if (!$dopaging) {
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
				$html .= ' <span>|</span> <a href="'.$sef.'">'.JText::_('COM_TAGS_SEE_MORE_RESULTS').' &rarr;</a>';
			}
		}
		$html .= '</p>'."\n\n";
		$html .= '</div><!-- / #'.$divid.' -->'."\n";
	}
	$k++;
}
if (!$foundresults) {
	$html .= '<p class="warning">' . JText::_('COM_TAGS_NO_RESULTS') . '</p>';
}
echo $html;
?>
			</div><!-- / .container-block -->
<?php
if ($dopaging) {
	jimport('joomla.html.pagination');
	$pageNav = new JPagination(
		$this->total, 
		$this->filters['start'], 
		$this->filters['limit']
	);

	$pageNav->setAdditionalUrlParam('task', 'view');
	$pageNav->setAdditionalUrlParam('tag', $this->tagstring);
	$pageNav->setAdditionalUrlParam('active', $this->active);
	$pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
	echo $pageNav->getListFooter() . '<div class="clearfix"></div>';
}
?>
		</div><!-- / .container -->
	</div><!-- / .subject -->
	<div class="clear"></div>
	<input type="hidden" name="task" value="view" />
	</form>
</div><!-- / .main section -->
