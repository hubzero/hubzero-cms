<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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