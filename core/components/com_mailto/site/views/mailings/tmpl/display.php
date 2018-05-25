<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('keepalive');

$data = $this->get('data');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.getElementById('mailtoForm');

		// do field validation
		if (form.mailto.value == "" || form.from.value == "") {
			alert('<?php echo Lang::txt('COM_MAILTO_EMAIL_ERR_NOINFO'); ?>');
			return false;
		}
		form.submit();
	}
</script>

<div id="mailto-window">
	<h2>
		<?php echo Lang::txt('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?>
	</h2>

	<div class="mailto-close">
		<a href="javascript: void window.close()" title="<?php echo Lang::txt('COM_MAILTO_CLOSE_WINDOW'); ?>">
			<span><?php echo Lang::txt('COM_MAILTO_CLOSE_WINDOW'); ?></span>
		</a>
	</div>

	<form action="<?php echo Request::base() ?>index.php" id="mailtoForm" method="post">
		<div class="formelm">
			<label for="mailto_field"><?php echo Lang::txt('COM_MAILTO_EMAIL_TO'); ?></label>
			<input type="text" id="mailto_field" name="mailto" class="inputbox" size="25" value="<?php echo $this->escape($data->mailto); ?>"/>
		</div>
		<div class="formelm">
			<label for="sender_field">
			<?php echo Lang::txt('COM_MAILTO_SENDER'); ?></label>
			<input type="text" id="sender_field" name="sender" class="inputbox" value="<?php echo $this->escape($data->sender); ?>" size="25" />
		</div>
		<div class="formelm">
			<label for="from_field">
			<?php echo Lang::txt('COM_MAILTO_YOUR_EMAIL'); ?></label>
			<input type="text" id="from_field" name="from" class="inputbox" value="<?php echo $this->escape($data->from); ?>" size="25" />
		</div>
		<div class="formelm">
			<label for="subject_field">
			<?php echo Lang::txt('COM_MAILTO_SUBJECT'); ?></label>
			<input type="text" id="subject_field" name="subject" class="inputbox" value="<?php echo $this->escape($data->subject); ?>" size="25" />
		</div>
		<p>
			<button class="button" onclick="return Joomla.submitbutton('send');">
				<?php echo Lang::txt('COM_MAILTO_SEND'); ?>
			</button>
			<button class="button" onclick="window.close();return false;">
				<?php echo Lang::txt('COM_MAILTO_CANCEL'); ?>
			</button>
		</p>
		<input type="hidden" name="layout" value="<?php echo $this->getLayout();?>" />
		<input type="hidden" name="option" value="com_mailto" />
		<input type="hidden" name="task" value="send" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="link" value="<?php echo $data->link; ?>" />
		<?php echo Html::input('token'); ?>
	</form>
</div>
