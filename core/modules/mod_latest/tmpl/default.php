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

defined('_HZEXEC_') or die();

?>
<table class="adminlist">
	<thead>
		<tr>
			<th scope="col">
				<?php echo Lang::txt('MOD_LATEST_LATEST_ITEMS'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('JSTATUS'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('MOD_LATEST_CREATED'); ?>
			</th>
			<th scope="col">
				<?php echo Lang::txt('MOD_LATEST_CREATED_BY');?>
			</th>
		</tr>
	</thead>
<?php if (count($list)) : ?>
	<tbody>
	<?php foreach ($list as $i => $item) : ?>
		<tr>
			<th scope="row">
				<?php if ($item->checked_out) : ?>
					<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time); ?>
				<?php endif; ?>

				<?php if ($item->link) : ?>
					<a href="<?php echo $item->link; ?>">
						<?php echo $this->escape($item->title); ?>
					</a>
				<?php else :
					echo $this->escape($item->title);
				endif; ?>
			</th>
			<td class="center">
				<?php echo Html::grid('published', $item->state, $i, '', false); ?>
			</td>
			<td class="center">
				<time datetime="<?php echo $item->created; ?>"><?php echo Date::of($item->created)->toLocal('Y-m-d H:i:s'); ?></time>
			</td>
			<td class="center">
				<?php echo $item->author_name; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
<?php else : ?>
	<tbody>
		<tr>
			<td colspan="4">
				<p class="noresults"><?php echo Lang::txt('MOD_LATEST_NO_MATCHING_RESULTS'); ?></p>
			</td>
		</tr>
	</tbody>
<?php endif; ?>
</table>
