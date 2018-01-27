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

// No direct access
// new LimitIterator($fruits, 0, 3)
defined('_HZEXEC_') or die();
?>

<?php 
$offset = array();
foreach ($this->items as $item) {
	if ($item["type"] === "static") {
		$collection = $item["content"];
		if (!array_key_exists($collection, $offset)) { 
			$offset[$collection] = 0; 
		}

		$boards = $this->_getBillboards($collection);
		$start = $offset[$collection];
		$n = min($item["n"], count($boards)-$start);
		foreach (array_slice($boards, $start, $n) as $board) { ?>
			<div class="<?php echo $item['class'] ?>">
				<div class="billboard-image">
					<img src="<?php echo $this->image_location; ?><?php echo $board->background_img; ?>"/>
				</div>
			</div>
		<?php }
		$offset[$collection] += $n;
	}
}
?>