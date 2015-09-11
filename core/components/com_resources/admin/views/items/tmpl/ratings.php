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

// No direct access.
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<p><?php echo $this->getError(); ?></p>
<?php } else { ?>
	<table class="adminform">
		<thead>
			<tr>
				<th colspan="3"><?php echo Lang::txt('COM_RESOURCES_RATINGS_TITLE'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->rows as $row)
		{
			if (intval($row->created) <> 0)
			{
				$thedate = Date::of($row->created)->toLocal();
			}
			$user = User::getInstance($row->user_id);
		?>
			<tr>
				<th><?php echo Lang::txt('COM_RESOURCES_RATING_USER'); ?>:</th>
				<td><?php echo $this->escape($user->get('name')); ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('COM_RESOURCES_RATING_VALUE'); ?>:</th>
				<td><?php echo \Components\Resources\Helpers\Html::writeRating($row->rating); ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('COM_RESOURCES_RATING_CREATED'); ?>:</th>
				<td><?php echo $thedate; ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('COM_RESOURCES_RATING_COMMENT'); ?>:</th>
				<td class="aLeft"><?php
					if ($row->comment) {
						echo $this->escape(stripslashes($row->comment));
					} else {
						echo Lang::txt('COM_RESOURCES_NONE');
					}
					?></td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } ?>