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

// no direct access
defined('_HZEXEC_') or die;
?>
<span class="breadcrumbs<?php echo $moduleclass_sfx; ?> pathway<?php echo $moduleclass_sfx; ?>">
	<?php
	if ($params->get('showHere', 1))
	{
		echo '<span class="showHere">' . Lang::txt('MOD_BREADCRUMBS_HERE') . '</span>';
	}

	// Get rid of duplicated entries on trail including home page when using multilanguage
	for ($i = 0; $i < $count; $i ++)
	{
		if ($i == 1 && !empty($list[$i]->link) && !empty($list[$i-1]->link) && $list[$i]->link == $list[$i-1]->link)
		{
			unset($list[$i]);
		}
	}

	// Find last and penultimate items in breadcrumbs list
	end($list);
	$last_item_key = key($list);
	prev($list);
	$penult_item_key = key($list);

	// Generate the trail
	foreach ($list as $key => $item) :
		// Make a link if not the last item in the breadcrumbs
		$show_last = $params->get('showLast', 1);
		if ($key != $last_item_key)
		{
			// Render all but last item - along with separator
			if (!empty($item->link))
			{
				echo '<a href="' . $item->link . '" class="pathway">' . html_entity_decode($item->name) . '</a>';
			}
			else
			{
				echo '<span>' . $item->name . '</span>';
			}

			if (($key != $penult_item_key) || $show_last)
			{
				echo ' <span class="sep">' . $separator . '</span> ';
			}

		}
		elseif ($show_last)
		{
			// Render last item if reqd.
			echo '<span>' . $item->name . '</span>';
		}
	endforeach;
	?>
</span>
