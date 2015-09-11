<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();
$i = 1;

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_DELETE_TEAM_MEMBERS'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">' . $this->getError() . '</p>');
}
$self = in_array($this->aid, $this->checked) ? 1 : 0;
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="get" action="<?php echo Route::url($this->model->link('team') . '&action=deleteit'); ?>">
	<fieldset>
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="deleteit" />
		<input type="hidden" name="controller" value="projects" />
		<input type="hidden" name="active" value="team" />
		<input type="hidden" name="ajax" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p class="anote"><?php echo Lang::txt('PLG_PROJECTS_TEAM_DELETE_TEAM_MEMBERS_NOTE'); ?></p>
		<p><?php echo Lang::txt('PLG_PROJECTS_TEAM_DELETE_TEAM_MEMBERS_CONFIRM'); ?></p>
		<p class="prominent"><?php foreach ($this->selected as $owner) {
			echo trim($owner->fullname) ? $owner->fullname : $owner->invited_email;
			echo ($i < count($this->selected)) ? ', ': '';
			$i++;
			echo '<input type="hidden" name="owner[]" value="' . $owner->id . '" />';
		} ?></p>
		<?php if ($self) { ?><p class="warning"><?php echo Lang::txt('PLG_PROJECTS_TEAM_WARNING_SELF_DELETE'); ?></p><?php } ?>
		<p class="submitarea">
			<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_DELETE'); ?>" class="btn" />
			<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?>" />
		</p>
	</fieldset>
</form>
<?php } ?>
</div>