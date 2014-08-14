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
$id     = JRequest::getInt('asset_id', null);
$asset  = new CoursesModelAsset($id);
$asset->set('section_id', $this->course->offering()->section()->get('id'));
$assets = array();

?>

<div class="wiki-edit">
	<h3>Create a wiki page</h3>

	<form action="<?php echo JURI::base(true); ?>/api/courses/asset/new" method="POST" class="edit-form">
		<div class="title-error error">Please provide a title first</div>

		<p>
			<label for="title">Title: </label><span class="required">*required</span>
			<input type="text" name="title" class="wiki-title" placeholder="Wiki page title" value="<?php echo $asset->get('title') ?>" />
		</p>

		<label for="content">Content: </label>
		<?php
		$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $asset->get('content'));
		echo \JFactory::getEditor()->display('content', $content, '', '', 35, 10, false, 'content', null, null, array('class' => 'minimal no-footer images'));
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
						foreach ($unit->assetgroups() as $agt) :
							foreach ($agt->children() as $ag) :
								$assetgroups[] = array('id'=>$ag->get('id'), 'title'=>$ag->get('title'));
								if ($ag->assets()->total()) :
									foreach ($ag->assets() as $a) :
										if ($a->isPublished()) :
											$assets[] = $a;
											//echo "<li>" . $a->get('title') . "</li>";
										endif;
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
			<p class="hint">Use the <code>Image</code> or <code>File</code> macro to include uploaded files. Example: <code>[[Image(mypicture.png)]]</code></p>
			<div class="wiki-files-upload-wrapper">
				<div class="wiki-files-upload">
					<p>Click or drop file</p>
				</div>
				<input type="file" name="files[]" class="fileupload" multiple />
			</div>
			<div class="wiki-files-available-wrapper">
				<div class="wiki-files-available">
					<?php $path = $asset->path($this->course->get('id')); ?>
					<?php if ($path && is_dir(JPATH_ROOT . $path)) : ?>
						<?php $files = array_diff(scandir(JPATH_ROOT . $asset->path($this->course->get('id'))), array('..', '.', '.DS_Store')); ?>
						<ul class="wiki-files-list">
							<?php foreach ($files as $file) : ?>
								<li class="wiki-file">
									<span class="wiki-files-filename"><?php echo $file; ?></span>
									<div class="wiki-files-delete"></div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p>No files found</p>
					<?php endif; ?>
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

		<p>
			<label for="graded">Create a gradebook entry for this item?</label>
			<input name="graded" type="checkbox" value="1" <?php echo ($asset->get('graded')) ? 'checked="checked"' : ''; ?>/>
			<input type="hidden" name="edit_graded" value="1" />
		</p>

		<p>
			<label for="progress_factors">Include this item in the progress calculation?</label>
			<input name="progress_factors" type="checkbox" value="1" <?php echo ($asset->get('progress_factors.asset_id')) ? 'checked="checked"' : ''; ?>/>
			<input type="hidden" name="edit_progress_factors" value="1" />
		</p>

		<?php if ($asset->get('id')) : ?>
			<div class="prerequisites">
				<?php
					usort($assets, function($a, $b) {
						return strnatcasecmp($a->get('title'), $b->get('title'));
					});

					$this->view('_prerequisites')
					     ->set('scope', 'asset')
					     ->set('scope_id', $asset->get('id'))
					     ->set('section_id', $this->course->offering()->section()->get('id'))
					     ->set('items', $assets)
					     ->set('includeForm', false)
					     ->display();
				?>
			</div>
		<?php endif; ?>

		<input type="hidden" name="original_scope_id" value="<?php echo $this->scope_id ?>" />
		<input type="hidden" name="course_id" value="<?php echo $this->course->get('id') ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
		<input type="hidden" name="section_id" value="<?php echo $this->course->offering()->section()->get('id'); ?>" />
		<input type="hidden" name="id" id="asset_id" value="<?php echo $id ?>" />
		<input type="hidden" name="type" value="wiki" />

		<input type="submit" value="Submit" class="wiki-submit submit" />
		<input type="button" value="Cancel" class="cancel" data-new="<?php echo (isset($id)) ? 'false' : 'true'; ?>" />
	</form>
</div>