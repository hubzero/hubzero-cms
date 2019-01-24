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
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$params = $params =  Component::params('com_groups');

$allowEmailResponses = $params->get('email_comment_processing');

// Be sure to update this if you add more options
$atLeastOneOption = false;
if ($allowEmailResponses)
{
	$atLeastOneOption = true;
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=memberoptions'); ?>" method="post" id="memberoptionform">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="action" value="savememberoptions" />
	<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID;?>" />

	<div class="group-content-header">
		<h3><?php echo Lang::txt('GROUP_MEMBEROPTIONS'); ?></h3>
	</div>

	<p><?php echo Lang::txt('GROUP_MEMBEROPTIONS_DESC'); ?></p>

	<?php if ($allowEmailResponses) { ?>
		<div class="input-wrap">
			<input type="checkbox" id="recvpostemail" value="1" name="recvpostemail" <?php if ($this->recvEmailOptionValue == 1) { echo 'checked="checked"'; } ?> />
			<label for="recvpostemail"><?php echo Lang::txt('GROUP_RECEIVE_EMAILS_DISCUSSION_POSTS'); ?></label>
		</div>
	<?php } ?>

	<?php if ($atLeastOneOption) { ?>
		<div class="submit">
			<input type="submit" class="btn" value="<?php echo Lang::txt('Save'); ?>" />
		</div>
	<?php } else { ?>
		<?php echo Lang::txt('GROUP_MEMBEROPTIONS_NONE'); ?>
	<?php } ?>
</form>
