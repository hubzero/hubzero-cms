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
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title(Lang::txt('COM_GROUPS'), 'groups.png');
Toolbar::custom('display', 'back', 'back', 'COM_GROUPS_BACK', false);
Toolbar::spacer();
Toolbar::custom('doupdate', 'merge', '', 'COM_GROUPS_MERGE_CODE', false);

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<?php if (!empty($this->success)) : ?>
		<table class="adminlist success">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_GROUPS_FETCH_SUCCESS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->success as $success) : ?>
					<tr>
						<td class="merge-success">
							<?php
								$group = \Hubzero\User\Group::getInstance($success['group']);
								echo '<strong>' . $group->get('description') . ' (' . $group->get('cn') . ')</strong>';
								echo '<p>' . Lang::txt('COM_GROUPS_FETCH_SUCCESS_DESC') . '</p>';
							?>
							<hr />
							<code><?php echo implode('<br>', $success['message']); ?></code>

							<?php if ($success['message'][0] != Lang::txt('COM_GROUPS_FETCH_CODE_UP_TO_DATE')
										&& !preg_match('/ineligible/', $success['message'][0])) : ?>
								<label class="merge">
									<?php echo Lang::txt('COM_GROUPS_MERGE'); ?>
									<input type="checkbox" name="id[]" checked="checked" value="<?php echo $group->get('gidNumber'); ?>" />
								</label>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<br /><br />

	<?php if (!empty($this->failed)) : ?>
		<table class="adminlist failed">
			<thead>
			 	<tr>
					<th scope="col"><?php echo Lang::txt('COM_GROUPS_FETCH_FAIL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->failed as $failed) : ?>
					<tr>
						<td>
							<?php
								$group = \Hubzero\User\Group::getInstance($failed['group']);
								echo '<strong>' . $group->get('description') . ' (' . $group->get('cn') . ')</strong>';
							?>
							<br />
							<br />
							<pre><?php echo $failed['message']; ?></pre>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="doupdate" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>