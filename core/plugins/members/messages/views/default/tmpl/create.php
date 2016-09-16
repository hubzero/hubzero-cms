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

//is the autocompleter disabled
$disabled = ($this->tos) ? true : false;

//get autocompleter
$tos = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'mbrs', 'members', '', $this->tos, '', $disabled)));

$this->css();
?>
<form action="<?php echo Route::url($this->member->link() . '&active=messages'); ?>" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>">
	<fieldset class="hub-mail">
		<div class="cont">
			<h3><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_COMPOSE_MESSAGE'); ?></h3>
			<label<?php if ($this->no_html) { echo ' class="width-65"'; } ?>>
				<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_TO'); ?>
				<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<?php
					if (count($tos) > 0)
					{
						echo $tos[0];
					}
					else
					{
						echo '<input type="text" name="mbrs" id="members" value="" />';
					}
				?>
			</label>
			<label>
				<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT'); ?>
				<input type="text" name="subject" id="msg-subject" value="<?php echo $this->escape(Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT_MESSAGE')); ?>"  />
			</label>
			<label>
				<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MESSAGE'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<textarea name="message" id="msg-message" rows="12" cols="50"></textarea>
			</label>
			<p class="submit">
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SEND'); ?>" />
			</p>
		</div>
	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="active" value="messages" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="action" value="send" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<?php echo Html::input('token'); ?>
</form>