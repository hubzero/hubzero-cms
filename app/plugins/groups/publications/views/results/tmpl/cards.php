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
     ->css('cards.css', 'com_publications');
$config = Component::params('com_publications');
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
			$l = "\t" . '<li' . $a . '><a href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area='. urlencode(stripslashes($blob))) . '">' . $this->escape(stripslashes($cat['title'])) . ' <span class="item-count">' . $cat['total'] . '</span></a>';
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
						$k[] = "\t\t\t" . '<li' . $a . '><a href="' . Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=publications&area='. urlencode(stripslashes($blob))) . '">' . $this->escape(stripslashes($subcat['title'])) . ' <span class="item-count">' . $subcat['total'] . '</span></a></li>';
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

<section class="section">
	<div class="card-container">
	<?php
	$html = '';
	$k = 0;
	foreach ($this->results as $category)
	{
		$amt = count($category);
		if ($amt > 0)
		{
			foreach ($category as $row)
			{
				$k++;
				$html .= $this->view('_card') //calling _card view here
				    ->set('row', $row)
				    ->set('authorized', $this->authorized)
				    ->loadTemplate();
			}
		}
	}
	echo $html;
	if (!$k)
	{
		echo '<p class="warning">' . Lang::txt('PLG_GROUPS_PUBLICATIONS_NONE') . '</p>';
	}
	?>
	</div>
</section>