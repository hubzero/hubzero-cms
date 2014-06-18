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
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js('resources', 'com_resources');

// Add the "all" category
$all = array(
	'category' => '',
	'title'    => JText::_('PLG_MEMBERS_CONTRIBUTIONS_ALL_CATEGORIES'),
	'total'    => $this->total
);

array_unshift($this->cats, $all);

// An array for storing all the links we make
$links = array();
$i = 0;
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
		$l = "\t" . '<li' . $a . '><a href="'.JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&area=' . urlencode(stripslashes($blob)) . '&sort=' . $this->sort) . '">' . $this->escape(stripslashes($cat['title'])) . ' <span class="item-count">' . $this->escape($cat['total']) . '</span></a>';

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
					$k[] = "\t\t\t" . '<li' . $a . '><a href="' . JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&area=' . urlencode(stripslashes($blob)) . '&sort=' . $this->sort) . '">' . $this->escape(stripslashes($subcat['title'])) . ' <span class="item-count">' . $this->escape($subcat['total']) . '</span></a></li>';
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
?>
<h3 class="section-header">
	<?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS'); ?>
</h3>

<form method="get" action="<?php JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions'); ?>">
	<input type="hidden" name="area" value="<?php echo $this->escape($this->active) ?>" />

	<div class="container">

		<?php if (count($links) > 0) { ?>
			<ul class="entries-menu filter-options">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&sort=date'); ?>"><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_CATEGORIES'); ?></a>
					<ul>
						<?php echo implode("\n", $links); ?>
					</ul>
				</li>
			</ul>
		<?php } ?>

		<ul class="entries-menu">
			<li><a<?php echo ($this->sort == 'date') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&area=' . urlencode(stripslashes($this->active)) . '&sort=date'); ?>" title="<?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_DATE'); ?>"><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_DATE'); ?></a></li>
			<li><a<?php echo ($this->sort == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&area=' . urlencode(stripslashes($this->active)) . '&sort=title'); ?>" title="<?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_TITLE'); ?>"><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_TITLE'); ?></a></li>
			<li><a<?php echo ($this->sort == 'usage') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&area=' . urlencode(stripslashes($this->active)) . '&sort=usage'); ?>" title="<?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_POPULARITY'); ?>"><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_POPULARITY'); ?></a></li>
		</ul>

		<div class="clearfix"></div>

		<div class="container-block">
<?php
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

		$name  = $this->cats[$k]['title'];
		$total = $this->cats[$k]['total'];
		$divid = 'search' . $this->cats[$k]['category'];

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
		$name = stripslashes($name);

		// A function for category specific items that may be needed
		// Check if a function exist (using old style plugins)
		$f = 'plgMembers' . ucfirst($this->cats[$k]['category']) . 'Doc';
		if (function_exists($f))
		{
			$f();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgMembers' . ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'documents'))
		{
			$html .= call_user_func(array($obj, 'documents'));
		}

		$ttl = ($total > 5) ? 5 : $total;
		if (!$dopaging)
		{
			$num = '1-' . $ttl . ' of ';
		}
		else
		{
			$stl = $this->start + 1;
			$ttl = ($total > $this->limit) ? $this->start + $this->limit : $this->start + $total;
			$ttl = ($total > $ttl) ? $ttl : $total;
			$num = $stl . '-' . $ttl . ' of ';
		}

		// Build the category HTML
		$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">';
		if (!$dopaging)
		{
			$html .= '<a href="'.JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&area='. urlencode(stripslashes($this->cats[$k]['category']))).'">';
		}
		$html .= $name.' <span>('.$num.$total.')</span>';
		if (!$dopaging && $total > 5)
		{
			$html .= '<span class="more">see more &raquo;</span></a> ';
		}
		$html .= '</h4>'."\n";
		$html .= '<div class="category-wrap" id="' . $divid . '">'."\n";

		// Does this category have custom output?
		// Check if a function exist (using old style plugins)
		$func = 'plgMembers' . ucfirst($this->cats[$k]['category']) . 'Before';
		if (function_exists($func))
		{
			$html .= $func();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgMembers' . ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'before'))
		{
			$html .= call_user_func(array($obj,'before'));
		}

		$html .= '<ol class="search results">' . "\n";
		foreach ($category as $row)
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);

			// Does this category have a unique output display?
			$func = 'plgMembers' . ucfirst($row->section) . 'Out';
			// Check if a method exist (using JPlugin style)
			$obj = 'plgMembers' . ucfirst($this->cats[$k]['category']);

			if (function_exists($func))
			{
				$html .= $func($row);
			}
			elseif (method_exists($obj, 'out'))
			{
				$html .= call_user_func(array($obj,'out'), $row);
			}
			else
			{
				$html .= "\t".'<li>'."\n";
				$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.$this->escape(stripslashes($row->title)).'</a></p>'."\n";
				if ($row->text) {
					$html .= "\t\t".\Hubzero\Utility\String::truncate(stripslashes($row->text))."\n";
				}
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
		// Initiate paging if we we're displaying an active category
		if (!$dopaging)
		{
			$html .= '<p class="moreresults">'.JText::sprintf('PLG_MEMBERS_CONTRIBUTIONS_NUMBER_SHOWN', $amt);
			// Ad a "more" link if necessary
			//if ($totals[$k] > 5) {
			if ($this->cats[$k]['total'] > 5)
			{
				$html .= ' | <a href="' . JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=contributions&area='.urlencode(strToLower($this->cats[$k]['category']))) . '">'.JText::_('PLG_MEMBERS_CONTRIBUTIONS_MORE').'</a>';
			}
			$html .= '</p>'."\n\n";
		}
		$html .= '</div><!-- / #'.$divid.' -->'."\n";
	}
	$k++;
}
echo $html;
if (!$foundresults) {
	echo '<p class="warning">' . JText::_('PLG_MEMBERS_CONTRIBUTIONS_NONE') . '</p>';
}
?>
		</div><!-- / .container-block -->
		<?php
		if ($dopaging)
		{
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($total, $this->start, $this->limit);

			$pageNav->setAdditionalUrlParam('id', $this->member->get('uidNumber'));
			$pageNav->setAdditionalUrlParam('active', 'contributions');
			$pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($this->active)));
			$pageNav->setAdditionalUrlParam('sort', $this->sort);
			echo $pageNav->getListFooter();
		}
		?>
		<div class="clearfix"></div>
	</div><!-- / .container -->
</form>
