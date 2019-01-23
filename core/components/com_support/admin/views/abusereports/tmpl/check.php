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

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_CHECK'), 'support.png');
Toolbar::custom('check', 'purge', '', 'COM_SUPPORT_CHECK', false);

Html::behavior('framework');

$this->view('_submenu')->display();

$this->css('
.spam {
	color:red;
}
.ham {
	color:green;
}
');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=check'); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-sample"><?php echo Lang::txt('COM_SUPPORT_ABUSE_SAMPLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<textarea name="sample" id="field-sample" cols="35" rows="20"><?php echo $this->escape($this->sample); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<?php if ($this->results) { ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_SUPPORT_ABUSE_SPAM_REPORT'); ?></span></legend>
					<table>
						<tbody>
							<?php
							foreach ($this->results as $result)
							{
								if (strstr($result['service'], '\\'))
								{
									$parts = explode('\\', $result['service']);
									$result['service'] = (isset($parts[2]) ? $parts[2] : $result['service']);
								}
								?>
								<tr>
									<th><?php echo $result['service']; ?></th>
									<td><?php echo $result['is_spam'] ? '<span class="spam">spam</span>' : '<span class="ham">ham</span>'; ?></td>
									<td><?php echo $result['message'] ? '<span class="detector-message">' . $result['message'] . '</span>' : ''; ?></td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</fieldset>
			<?php } else { ?>
				<p class="info"><?php echo Lang::txt('COM_SUPPORT_ABUSE_CHECK_ABOUT'); ?></p>
			<?php } ?>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="check" />

	<?php echo Html::input('token'); ?>
</form>
