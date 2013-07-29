<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Load asset if applicable
$id = JRequest::getInt('asset_id', null);
$asset = new CoursesModelAsset($id);

?>

<div class="wiki-edit">
	<h3>Create a wiki page</h3>

	<form action="/api/courses/asset/new" method="POST" class="edit-form">

		<div class="title-error error">Please provide a title first</div>
		<p>
			<label for="title">Title: </label><span class="required">*required</span>
			<input type="text" name="title" class="wiki-title" placeholder="Wiki page title" value="<?php echo $asset->get('title') ?>" />
		</p>

		<label for="content">Content: </label>
<?php
		ximport('Hubzero_Wiki_Editor');
		$editor =& Hubzero_Wiki_Editor::getInstance();

		echo $editor->display('content', 'content', $asset->get('content'), '', '35', '10');
?>

<?php // @TODO: implement asset insertion to wiki body! ?>

<!--		<div class="wiki-include-assets">
			<div class="wiki-assets-inner">
				<p class="help">Drag an asset from below, to the text box above to include it in your wiki.</p>
				<ul>
-->
<?php
					$assetgroups = array();
					foreach ($this->course->offering()->units() as $unit) :
						foreach($unit->assetgroups() as $agt) :
							foreach($agt->children() as $ag) :
								$assetgroups[] = array('id'=>$ag->get('id'), 'title'=>$ag->get('title'));
								if ($ag->assets()->total()) :
									foreach ($ag->assets() as $a) :
										//echo "<li>" . $a->get('title') . "</li>";
									endforeach;
								endif;
							endforeach;
						endforeach;
					endforeach;
?>
<!--
				</ul>
			</div>
		</div>
-->

		<div class="wiki-files">
			<div class="wiki-files-upload-wrapper">
				<div class="wiki-files-upload">
					<p>Click or drop file</p>
				</div>
				<input type="file" name="files[]" class="fileupload" multiple />
			</div>
			<div class="wiki-files-available-wrapper">
				<div class="wiki-files-available">
					<?php
					$path = $asset->path($this->course->get('id'));
						if ($path && is_dir(JPATH_ROOT . $path))
						{
							$files = array_diff(scandir(JPATH_ROOT . $asset->path($this->course->get('id'))), array('..', '.', '.DS_Store'));
							echo '<ul class="wiki-files-list">';
							foreach ($files as $file)
							{
								echo '<li class="wiki-file">';
								echo $file;
								echo "</li>";
							}
							echo "</ul>";
						}
						else
						{
							echo '<p>No files found</p>';
						}
					?>
				</div>
			</div>
		</div>

		<p>
			<label for="scope_id">Attach to:</label>
			<select name="scope_id">
				<?php foreach ($assetgroups as $assetgroup) : ?>
					<?php $selected = ($assetgroup['id'] == $this->scope_id) ? 'selected' : ''; ?>
					<option value="<?php echo $assetgroup['id'] ?>" <?php echo $selected ?>><?php echo $assetgroup['title'] ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<input type="hidden" name="original_scope_id" value="<?= $this->scope_id ?>" />
		<input type="hidden" name="course_id" value="<?= $this->course->get('id') ?>" />
		<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
		<input type="hidden" name="id" id="asset_id" value="<?= $id ?>" />
		<input type="hidden" name="type" value="wiki" />

		<input type="submit" value="Submit" class="wiki-submit" />
		<input type="button" value="Cancel" class="cancel" />

	</form>
</div>