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

$team = $this->model->team($filters = array('status' => 1));

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER'); ?></h3>
<?php if (empty($team)) { ?>
	<p class="error"><?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_ERROR_NO_TEAM'); ?></p>
</div>
<?php return; } ?>
<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->model->link()); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="changeowner" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="team" />
		<input type="hidden" name="confirm" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p class="anote"><?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_CHOICE'); ?></p>
		<p class="warning mini"><?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_NOTICE'); ?></p>

		<label for="owned_by_user">
			<?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_CHOOSE_INDIVIDUAL'); ?>:
			<select name="owned_by_user" class="block">
		<?php foreach ($team as $member) {  ?>
			<option value="<?php echo $member->userid; ?>" <?php if ($member->userid == $this->model->get('owned_by_user')) { echo 'selected="selected"'; } ?>><?php echo $member->fullname; ?> <?php if ($member->userid == $this->model->get('owned_by_user')) { echo '(' . Lang::txt('PLG_PROJECTS_TEAM_CURRENT_OWNER') . ')'; } ?></option>
		<?php } ?>
			</select>
		</label>
		<?php if (!empty($this->groups)) {
			$used = array();
			?>
		<label for="owned_by_group">
			<?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_CHOOSE_GROUP'); ?>:
			<select name="owned_by_group" class="block">
				<option value="0" <?php if (!$this->model->groupOwner()) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('PLG_PROJECTS_TEAM_NO_GROUP'); ?></option>
		<?php foreach ($this->groups as $g) {
			if (in_array($g->gidNumber, $used))
			{
				continue;
			}
			$used[] = $g->gidNumber; ?>
			<option value="<?php echo $g->gidNumber; ?>" <?php if ($g->gidNumber == $this->model->get('owned_by_group')) { echo 'selected="selected"'; } ?>><?php echo \Hubzero\Utility\String::truncate($g->description, 30) . ' (' . $g->cn . ')'; ?></option>
		<?php } ?>
			</select>
		</label>
		<?php } ?>
		<p class="submitarea">
			<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_SAVE_OWNER'); ?>" class="btn" />
			<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?>" />
		</p>
	</fieldset>
</form>
</div>