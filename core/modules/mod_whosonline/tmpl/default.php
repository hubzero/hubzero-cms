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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>

<div class="<?php echo $this->params->get('moduleclass_sfx', ''); ?>">
	<?php if ($this->params->get('showmode', 0) == 0 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN'); ?></th>
					<th scope="col"><?php echo Lang::txt('MOD_WHOSONLINE_GUESTS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo number_format($this->loggedInCount); ?></td>
					<td><?php echo number_format($this->guestCount); ?></td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<?php if ($this->params->get('showmode', 0) == 1 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th colspan="2"><?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN_NAME'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->loggedInList as $loggedin) : ?>
					<tr>
						<td><?php echo $loggedin->get('name'); ?></td>
						<td>
							<a href="<?php echo Route::url('index.php?option=com_members&id=' . $loggedin->get('id')); ?>">
								<?php echo Lang::txt('MOD_WHOSONLINE_LOGGEDIN_VIEW_PROFILE'); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<table>
		<tbody>
			<tr>
				<td>
					<a class="btn btn-secondary opposite icon-next" href="<?php echo Route::url('index.php?option=com_members&task=activity'); ?>">
						<?php echo Lang::txt('MOD_WHOSONLINE_VIEW_ALL_ACTIVITIY'); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>