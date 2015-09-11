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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('providers.css', 'com_users')
     ->js()
     ->js('jquery.hoverIntent', 'system');
?>

<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN'); ?></h3>

<?php if (isset($this->notifications) && count($this->notifications) > 0) {
	foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } // close foreach
} // close if count ?>

<div id="members-account-section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option .
										'&id=' . $this->id .
										'&active=account' .
										'&task=confirmtoken'); ?>" method="post">
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN'); ?></legend>
			<div class="fieldset-grouping">
				<label for="token"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_TOKEN'); ?>:</label>
				<input id="token" name="token" type="text" class="required" size="36" />
			</div>
		</fieldset>

		<div class="clear"></div>

		<p class="submit">
			<input name="change" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_SUBMIT'); ?>" />
			<input type="reset" class="cancel" value="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_CANCEL'); ?>" />
		</p>

		<?php echo Html::input('token'); ?>
	</form>
</div>
<div class="clear"></div>