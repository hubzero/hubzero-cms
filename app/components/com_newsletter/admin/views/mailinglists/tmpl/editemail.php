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

// No direct access
defined('_HZEXEC_') or die();

//set title
$text = ($this->task == 'editEmail' ? Lang::txt('Edit') : Lang::txt('New'));

Toolbar::title(Lang::txt('Newsletter Mailing List Email') . ': ' . $text, 'list');
Toolbar::save('saveemail');
Toolbar::cancel('cancelemail');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('%s Mailing List Email', $text); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key" width="200px"><?php echo Lang::txt('Mailing List'); ?>:</td>
							<td><strong><?php echo $this->escape($this->list->name); ?></strong></td>
						</tr>
						<tr>
							<td class="key"><?php echo Lang::txt('Email'); ?>:</td>
							<td><input type="text" name="fields[email]" value="<?php echo $this->escape($this->email->email); ?>" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('Date Added'); ?>:</th>
						<td><?php echo gmdate("F d, Y @ g:ia", strtotime($this->email->date_added)); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('Confirmed?'); ?></th>
						<td><?php echo ($this->email->confirmed) ? Lang::txt('JYes') : Lang::txt('JNo'); ?></td>
					</tr>
					<?php if ($this->email->confirmed) : ?>
						<tr>
							<th><?php echo Lang::txt('Date Confirmed'); ?>:</th>
							<td><?php echo gmdate("F d, Y @ g:ia", strtotime($this->email->date_confirmed)); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="fields[mid]" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->email->id; ?>" />
	<input type="hidden" name="task" value="saveemail" />

	<?php echo Html::input('token'); ?>
</form>