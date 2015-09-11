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

Html::behavior('core');

// Load asset if applicable
$id     = Request::getInt('asset_id', null);
$asset  = new \Components\Courses\Models\Asset($id);
$asset->set('section_id', $this->course->offering()->section()->get('id'));
$assets = array();

?>

<div class="wiki-edit">
	<h3>Create a wiki page</h3>

	<form action="<?php echo Request::base(true); ?>/api/courses/asset/new" method="POST" class="edit-form">
		<div class="title-error error">Please provide a title first</div>

		<p>
			<label for="title">Title: </label><span class="required">*required</span>
			<input type="text" name="title" class="wiki-title" placeholder="Wiki page title" value="<?php echo $asset->get('title') ?>" />
		</p>

		<label for="content">Content: </label>
		<?php
		$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $asset->get('content'));
		echo $this->editor('content', $content, 35, 10, 'content'); //, null, null, array('class' => 'minimal no-footer images'));
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
											$a->set('longTitle', $unit->get('title') . ' - ' . $ag->get('title') . ' - ' . $a->get('title'));
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
					<?php if ($path && is_dir(PATH_APP . $path)) : ?>
						<?php $files = array_diff(scandir(PATH_APP . $asset->path($this->course->get('id'))), array('..', '.', '.DS_Store')); ?>
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