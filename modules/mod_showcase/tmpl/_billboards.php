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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Initialize boards from new collection
$collection = $item["content"];
if (!array_key_exists($collection, $boards)) { 
	$boards[$collection] = $this->_getBillboards($collection);
}

// Make sure we don't ask for too much
$n = min($item["n"], count($boards[$collection]));
if ($n < $item["n"]) {
	echo 'Showcase Module Error: Not enough billboards left in collection "' . $collection . '"!';
}

if ($item["ordering"] === "ordered") {
	// Pulls billboards based on their ordering
	$item_boards = array_slice($boards[$collection], 0, $n);
	$boards[$collection] = array_slice($boards[$collection], $n, count($boards[$collection]));
} elseif ($item["ordering"] === "random") {
	// Pulls billboards randomly
	$rind = array_flip(array_rand($boards[$collection], $n));
	$item_boards = array_intersect_key($boards[$collection], $rind);
	shuffle($item_boards);
	$boards[$collection] = array_diff_key($boards[$collection], $rind);
} elseif ($item["ordering"] === "indexed") {
	// Pulls billboards based on id - this should be everything
	$item_boards = array();
	$remove_keys = array();
	foreach ($boards[$collection] as $key => $board)
	{
		if (in_array($board->ordering, $item["indices"]))
		{
			$item_boards[] = $board;
			$remove_keys[] = $key;
		}
	}
	$boards[$collection] = array_diff_key($boards[$collection], array_flip($remove_keys));
} else {
	echo 'Showcase Module Error: Unknown ordering "' . $item["ordering"] . '".  Possible values include "ordered" or "random".';
}

// Display individual boards
foreach ($item_boards as $board) { ?>
	<div class="<?php echo $item['class'] ?> billboard">
		<div class="billboard-image">
			<?php
			if (!empty($board->learn_more_target))
			{
				echo '<a href="' . $board->learn_more_target . '">';
			}
			?>
			<img src="<?php echo $this->image_location; ?><?php echo $board->background_img; ?>"/>
			<?php
			if (!empty($board->learn_more_target))
			{
				echo '</a>';
			}
			?>
		</div>
		<?php if ($item['tag']): ?>
			<div class="billboard-tag">
				<?php if ($item['tag-target']): ?>
					<a href="<?php echo $item['tag-target']; ?>">
				<?php endif; ?>
					<span><?php echo $item['tag']; ?></span>
				<?php if ($item['tag-target']): ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="billboard-header">
			<?php
			if (!empty($board->learn_more_target))
			{
				echo '<a href="' . $board->learn_more_target . '">';
			}
			?>
			<span><?php echo $board->header; ?></span>
			<?php
			if (!empty($board->learn_more_target))
			{
				echo '</a>';
			}
			?>
		</div>
	</div>
<?php } ?>