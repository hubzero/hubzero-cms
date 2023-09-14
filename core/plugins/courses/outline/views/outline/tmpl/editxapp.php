<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('core');

// Load asset if applicable
$id = Request::getInt('asset_id', null);
$asset = new \Components\Courses\Models\Asset($id);
$asset->set('section_id', $this->course->offering()->section()->get('id'));
$xappAsset = $asset->loadHandler();
$assets = array();
include_once Component::path('com_projects') . '/models/orm/owner.php';
include_once Component::path('com_projects') . '/models/orm/description/field.php';
include_once Component::path('com_tools') . '/models/tool.php';
$projects = \Components\Projects\Models\Orm\Owner::all()
	->join('#__projects', 'projectid', '#__projects.id')
	->whereEquals('userid', User::get('id'))
	->rows();
//$xapps = \Components\Xapps\Models\Xapp::getMyXapps();
$xapps = array();
$config = Component::params('com_courses');
$xapp_path = $config->get('xapp_path');
?>

<div class="xapp-edit edit-form">
	<form action="<?php echo Request::base(true); ?>/api/courses/asset/new" method="POST" class="edit-form">
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
		<p>
			<label for="title">Title: </label><span class="required">*required</span>
			<input type="text" name="title" class="xapp-title" placeholder="External App Title" value="<?php echo $asset->get('title') ?>" />
		</p>
		<p>
			<label for="xapp-alias">External App:</label>
			<select class="xapp-list" id="xapp-alias" name="xapp-alias">
				<option value="">Select an External App...</option>
				<?php foreach ($xapps as $xapp) : ?>
					<?php preg_match('/\/xapps\/([0-9a-z]+)\//', $asset->get('url'), $substr); ?>
					<?php $selected = ($substr && isset($substr[1]) && $substr[1] == $xapp->alias) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo $xapp->alias ?>" <?php echo $selected ?>><?php echo $xapp->title ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<label for="xapp-alias">Add Files:</label>
		<div class="xapp-files">
			<div class="xapp-files-upload-wrapper">
				<div class="xapp-files-upload">
					<p>Click or drop file</p>
				</div>
				<input type="file" name="files[]" class="fileupload" multiple />
			</div>
			<div class="xapp-files-available-wrapper">
				<label for="project-selector">Select Files from a Project:</label>
				<select id="project-selector">
					<option value="">Select a Project...</option>
					<?php foreach ($projects as $project): ?>
						<option value="<?php echo $project->get('alias');?>">
							<?php echo $project->get('title');?>	
						</option>
					<?php endforeach; ?>
				</select>
				<button id="b-filesave">Add selected</button>
			</div>
		</div>
		<div class="xapp-files-available">
			<?php $files = $xappAsset ? $xappAsset->files($asset) : array(); ?>
			<?php if (!empty($files)) : ?>
				<ul class="xapp-files-list">
					<?php foreach ($files as $file) : ?>
						<li class="xapp-file">
							<span class="xapp-files-filename"><?php echo $file; ?></span>
							<div class="xapp-files-delete"></div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p>No Files Added</p>
			<?php endif; ?>
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
		<input type="hidden" name="type" value="xapp" />
		<input type="hidden" name="subtype" value="xapp" />

		<input type="submit" value="Submit" class="xapp-submit submit" />
		<input type="button" value="Cancel" class="cancel" data-new="<?php echo (isset($id)) ? 'false' : 'true'; ?>" />
	</form>
</div>
