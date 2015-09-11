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

if ($this->row instanceof \Components\Collections\Models\Collection)
{
	$collection = $this->row;
}
else
{
	$collection = \Components\Collections\Models\Collection::getInstance($this->row->item()->get('object_id'));
	if ($this->row->get('description'))
	{
		$collection->set('description', $this->row->get('description'));
	}
}
?>
		<h4<?php if ($collection->get('access', 0) == 4) { echo ' class="private"'; } ?>>
			<a href="<?php echo Route::url($collection->link()); ?>">
				<?php echo $this->escape(stripslashes($collection->get('title'))); ?>
			</a>
		</h4>
		<div class="description">
			<?php echo $collection->description('parsed'); ?>
		</div>
		<?php /* <table>
			<tbody>
				<tr>
					<td>
						<strong><?php echo $collection->count('file'); ?></strong> <span class="post-type file"><?php echo Lang::txt('files'); ?></span>
					</td>
					<td>
						<strong><?php echo $collection->count('collection'); ?></strong> <span class="post-type collection"><?php echo Lang::txt('collections'); ?></span>
					</td>
					<td>
						<strong><?php echo $collection->count('link'); ?></strong> <span class="post-type link"><?php echo Lang::txt('links'); ?></span>
					</td>
				</tr>
			</tbody>
		</table> */ ?>