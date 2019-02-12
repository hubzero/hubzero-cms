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
Toolbar::title(Lang::txt('COM_NEWSLETTER_TEST_SENDING') . ': ' . $this->newsletter->name, 'newsletter.png');

//add buttons to toolbar
Toolbar::custom('dosendtest', 'send','', 'COM_NEWSLETTER_TOOLBAR_SEND_TEST', false);
Toolbar::cancel();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm">
	<div class="col width-100">
		<?php if ($this->newsletter->id != null) : ?>
			<a name="distribution"></a>
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_NEWSLETTER_TEST_SENDING'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER'); ?>:</th>
							<td>
								<?php echo $this->escape($this->newsletter->name); ?>
							</td>
						</tr>
						<tr>
							<th>
								<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS'); ?>:<br />
								<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_HINT'); ?></span>
							</th>
							<td>
								<input type="text" name="emails" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_PLACEHOLDER'); ?>" autocomplete="off" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="dosendtest" />
	<input type="hidden" name="nid" value="<?php echo $this->newsletter->id; ?>" />

	<?php echo Html::input('token'); ?>
</form>