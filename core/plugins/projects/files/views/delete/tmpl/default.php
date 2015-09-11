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

$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" class="" action="<?php echo $this->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="removeit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="repo" value="<?php echo $this->repo->get('name'); ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

		<p><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_FILES_CONFIRM'); ?></p>

		<ul class="sample">
		<?php foreach ($this->items as $file)
		{
			// Display list item with file data
			$this->view('default', 'selected')
			     ->set('skip', false)
			     ->set('file', $file)
			     ->set('action', 'delete')
			     ->set('multi', 'multi')
			     ->display();
		} ?>
		</ul>

		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE'); ?>" id="submit-ajaxform" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?>" />
			<?php } else {  ?>
					<a id="cancel-action" href="<?php echo $this->url . '?a=1' . $subdirlink; ?>" class="btn btn-cancel"><?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?></a>
			<?php } ?>
		</p>
	</fieldset>
</form>
<?php } ?>
</div>