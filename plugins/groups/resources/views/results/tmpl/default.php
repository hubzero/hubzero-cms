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

$config =& JComponentHelper::getParams( 'com_resources' );

// An array for storing all the links we make
$links = array();
$html = '';

if ($this->cats)
{
	// Loop through each category
	foreach ($this->cats as $cat)
	{
		// Only show categories that have returned search results
		if ($cat['total'] > 0) 
		{
			// Is this the active category?
			$a = ($cat['category'] == $this->active) ? ' class="active"' : '';

			// If we have a specific category, prepend it to the search term
			$blob = ($cat['category']) ? $cat['category'] : '';

			// Build the HTML
			$l = "\t" . '<li' . $a . '><a href="' . JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area='. urlencode(stripslashes($blob))) . '">' . $this->escape(stripslashes($cat['title'])) . ' <span class="item-count">' . $cat['total'] . '</span></a>';

			// Are there sub-categories?
			if (isset($cat['_sub']) && is_array($cat['_sub'])) 
			{
				// An array for storing the HTML we make
				$k = array();
				// Loop through each sub-category
				foreach ($cat['_sub'] as $subcat)
				{
					// Only show sub-categories that returned search results
					if ($subcat['total'] > 0) 
					{
						// Is this the active category?
						$a = ($subcat['category'] == $this->active) ? ' class="active"' : '';

						// If we have a specific category, prepend it to the search term
						$blob = ($subcat['category']) ? $subcat['category'] : '';

						// Build the HTML
						$k[] = "\t\t\t" . '<li' . $a . '><a href="' . JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area='. urlencode(stripslashes($blob))) . '">' . $this->escape(stripslashes($subcat['title'])) . ' <span class="item-count">' . $subcat['total'] . '</span></a></li>';
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
}

?>
<h3 class="section-header">
	<a name="resources"></a>
	<?php echo JText::_('PLG_GROUPS_RESOURCES'); ?>
</h3>

<ul id="page_options">
	<li>
		<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=com_resources&task=draft&group=' . $this->group->get('cn')); ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_START_A_CONTRIBUTION'); ?></a>
	</li>
</ul>

<div class="section">
	<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources'); ?>">

	<input type="hidden" name="area" value="<?php echo $this->escape($this->active); ?>" />

	<div class="container">
		
		<ul class="entries-menu filter-options">
<?php if (count($links) > 0) { ?>
			<li class="filter-categories">
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=' . $this->active); ?>"><?php echo JText::_('Categories'); ?></a>
				<ul>
					<?php echo implode("\n", $links); ?>
				</ul>
			</li>
<?php } ?>
			<li>
				<a<?php echo ($this->access == 'all') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=all'); ?>">
					<?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_ALL'); ?>
				</a>
			</li>
			<li>
				<a<?php echo ($this->access == 'public') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=public'); ?>">
					<?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_PUBLIC'); ?>
				</a>
			</li>
			<li>
				<a<?php echo ($this->access == 'protected') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=protected'); ?>">
					<?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_PROTECTED'); ?>
				</a>
			</li>
			<li>
				<a<?php echo ($this->access == 'private') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=' . $this->sort . '&access=private'); ?>">
					<?php echo JText::_('PLG_GROUPS_RESOURCES_ACCESS_PRIVATE'); ?>
				</a>
			</li>
		</ul>
		
		<ul class="entries-menu">
			<li><a<?php echo ($this->sort == 'date') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=date&access=' . $this->access); ?>" title="Sort by newest to oldest">&darr; <?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_DATE'); ?></a></li>
			<li><a<?php echo ($this->sort == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=title&access=' . $this->access); ?>" title="Sort by title">&darr; <?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_TITLE'); ?></a></li>
<?php if ($config->get('show_ranking')) { ?>
			<li><a<?php echo ($this->sort == 'ranking') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=ranking&access=' . $this->access); ?>" title="Sort by popularity">&darr; <?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_RANKING'); ?></a></li>
<?php } else { ?>
			<li><a<?php echo ($this->sort == 'rating') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources&area=' . urlencode(stripslashes($this->active)) . '&sort=rating&access=' . $this->access); ?>" title="Sort by popularity">&darr; <?php echo JText::_('PLG_GROUPS_RESOURCES_SORT_BY_RATING'); ?></a></li>
<?php } ?>
		</ul>

		<div class="clearfix"></div>
		
		<div class="container-block">
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
		$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.$name.' <span>('.$num.')</span></h4>'."\n";
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
				$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.$this->escape(stripslashes($row->title)).'</a></p>'."\n";
				if ($row->text) {
					$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(stripslashes($row->text))."\n";
				}
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
		// Initiate paging if we we're displaying an active category
		if (!$dopaging) 
		{
			$html .= '<p class="moreresults">'.JText::sprintf('PLG_GROUPS_RESOURCES_NUMBER_SHOWN', $amt);
			// Ad a "more" link if necessary
			if ($totals[$k] > 5) {
				$qs = 'area='.urlencode(strToLower($this->cats[$k]['category']));
				$seff = JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=resources');
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
	echo '<p class="warning">' . JText::_('PLG_GROUPS_RESOURCES_NONE') . '</p>';
}
?>
			</div><!-- / .container-block -->
<?php
jimport('joomla.html.pagination');
$pageNav = new JPagination( $this->total, $this->limitstart, $this->limit );

$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
$pageNav->setAdditionalUrlParam('active', 'resources');
$pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($this->active)));
$pageNav->setAdditionalUrlParam('sort', $this->sort);
$pageNav->setAdditionalUrlParam('access', $this->access);
echo $pageNav->getListFooter();
?>
		<div class="clearfix"></div>
	</div><!-- / .container -->
</form>
</div>
