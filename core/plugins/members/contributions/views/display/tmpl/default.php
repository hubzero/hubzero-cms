<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('resources', 'com_resources');

// Add the "all" category
$all = array(
	'category' => '',
	'title'    => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_ALL_CATEGORIES'),
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
		$l = "\t" . '<li' . $a . '><a href="'.Route::url($this->member->getLink() . '&active=contributions&area=' . urlencode(stripslashes($blob)) . '&sort=' . $this->sort) . '">' . $this->escape(stripslashes($cat['title'])) . ' <span class="item-count">' . $this->escape($cat['total']) . '</span></a>';

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
					$k[] = "\t\t\t" . '<li' . $a . '><a href="' . Route::url($this->member->getLink() . '&active=contributions&area=' . urlencode(stripslashes($blob)) . '&sort=' . $this->sort) . '">' . $this->escape(stripslashes($subcat['title'])) . ' <span class="item-count">' . $this->escape($subcat['total']) . '</span></a></li>';
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
	<?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS'); ?>
</h3>

<form method="get" action="<?php Route::url($this->member->getLink() . '&active=contributions'); ?>">
	<input type="hidden" name="area" value="<?php echo $this->escape($this->active) ?>" />

	<div class="container">
		<nav class="entries-filters">
			<?php if (count($links) > 0) { ?>
				<ul class="entries-menu filter-options">
					<li>
						<a href="<?php echo Route::url($this->member->getLink() . '&active=contributions&sort=date'); ?>"><?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_CATEGORIES'); ?></a>
						<ul>
							<?php echo implode("\n", $links); ?>
						</ul>
					</li>
				</ul>
			<?php } ?>

			<ul class="entries-menu order-options">
				<li><a<?php echo ($this->sort == 'date') ? ' class="active"' : ''; ?> href="<?php echo Route::url($this->member->getLink() . '&active=contributions&area=' . urlencode(stripslashes($this->active)) . '&sort=date'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_DATE'); ?>"><?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_DATE'); ?></a></li>
				<li><a<?php echo ($this->sort == 'title') ? ' class="active"' : ''; ?> href="<?php echo Route::url($this->member->getLink() . '&active=contributions&area=' . urlencode(stripslashes($this->active)) . '&sort=title'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_TITLE'); ?>"><?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_TITLE'); ?></a></li>
				<li><a<?php echo ($this->sort == 'usage') ? ' class="active"' : ''; ?> href="<?php echo Route::url($this->member->getLink() . '&active=contributions&area=' . urlencode(stripslashes($this->active)) . '&sort=usage'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_POPULARITY'); ?>"><?php echo Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_POPULARITY'); ?></a></li>
			</ul>
		</nav>

		<div class="container-block">
<?php
$foundresults = false;
$dopaging = false;
$html = '';
$k = 1;

foreach ($this->results as $category)
{
	$amt = count($category);

	if ($amt > 0 && isset($this->cats[$k]))
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
			$html .= '<a href="' . Route::url($this->member->getLink() . '&active=contributions&area='. urlencode(stripslashes($this->cats[$k]['category']))) . '">';
		}
		$html .= $name.' <span>('.$num.$total.')</span>';
		if (!$dopaging)
		{
			$html .= '<span class="more">' . Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_MORE') . '</span></a> ';
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
			$html .= '<p class="moreresults">'.Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_NUMBER_SHOWN', $amt);
			// Ad a "more" link if necessary
			//if ($totals[$k] > 5) {
			if ($this->cats[$k]['total'] > 5)
			{
				$html .= ' | <a href="' . Route::url($this->member->getLink() . '&active=contributions&area='.urlencode(strToLower($this->cats[$k]['category']))) . '">'.Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_MORE').'</a>';
			}
			$html .= '</p>'."\n\n";
		}
		$html .= '</div><!-- / #'.$divid.' -->'."\n";
	}
	$k++;
}
echo $html;
if (!$foundresults) {
	echo '<p class="warning">' . Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_NONE') . '</p>';
}
?>
		</div><!-- / .container-block -->
		<?php
		if ($dopaging)
		{
			$pageNav = $this->pagination($total, $this->start, $this->limit);

			$pageNav->setAdditionalUrlParam('id', $this->member->get('uidNumber'));
			$pageNav->setAdditionalUrlParam('active', 'contributions');
			$pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($this->active)));
			$pageNav->setAdditionalUrlParam('sort', $this->sort);
			echo $pageNav->render();
		}
		?>
		<div class="clearfix"></div>
	</div><!-- / .container -->
</form>
