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

Html::behavior('tooltip');
?>
<div class="unotes">
	<h2 class="modal-title"><?php echo Lang::txt('COM_MEMBERS_NOTES_FOR_USER', $this->user->get('name'), $this->user->get('id')); ?></h2>
	<table class="adminlist">
		<tbody>
			<tr>
				<td>
<?php if (!$this->rows->count()) : ?>
	<?php echo Lang::txt('COM_MEMBERS_NO_NOTES'); ?>
<?php else : ?>
	<ol class="alternating">
	<?php foreach ($this->rows as $row) : ?>
		<li>
			<div class=" utitle">
				<?php if ($row->get('subject')) : ?>
					<h4><?php echo Lang::txt('COM_MEMBERS_NOTE_N_SUBJECT', (int) $row->get('id'), $this->escape($row->get('subject'))); ?></h4>
				<?php else : ?>
					<h4><?php echo Lang::txt('COM_MEMBERS_NOTE_N_SUBJECT', (int) $row->get('id'), Lang::txt('COM_MEMBERS_EMPTY_SUBJECT')); ?></h4>
				<?php endif; ?>
			</div>

			<div class="utitle">
				<?php echo Date::of($row->get('created_time'))->toLocal('D d M Y H:i'); ?>,
				<em><?php echo $this->escape($row->category->get('title')); ?></em>
			</div>

			<div class="ubody">
				<?php echo $row->get('body'); ?>
			</div>
		</li>
	<?php endforeach; ?>
	</ol>
<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
