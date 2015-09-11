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

// Directory path breadcrumbs
$bc = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent, false);

$bcEnd = $this->type == 'folder' ? '<span class="folder">' . $this->item . '</span>' : '<span class="file">' . $this->item . '</span>';
?>

<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME') . ' ' . $this->type . ' ' . $bc . ' ' . $bcEnd; ?></h3>
<?php
// Display error
if ($this->getError()) {
	echo ('<p class="witherror">' . $this->getError() . '</p>');
}
else {
?>
	<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
		<fieldset>
			<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
			<input type="hidden" name="repo" value="<?php echo $this->repo->get('name'); ?>" />
			<input type="hidden" name="action" value="renameit" />
			<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
			<input type="hidden" name="oldname" value="<?php echo $this->item; ?>" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_FILES_NEW_NAME'); ?></h5>
			<label>
				<input type="text" name="newname" maxlength="250" value="<?php echo $this->item; ?>" />
			</label>
			<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_SAVE'); ?>" />
			<input type="reset" class="btn btn-cancel" id="cancel-action" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?>" />
		</fieldset>
	</form>
<?php } ?>
</div>