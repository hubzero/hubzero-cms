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

\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect.min');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');

// Get our asset model
$asset = new \Components\Courses\Models\Asset(Request::getInt('asset_id', null));
$asset->set('section_id', $this->course->offering()->section()->get('id'));

// Get the asset groups
$assetgroups = array();
$assets      = array();
foreach ($this->course->offering()->units() as $unit) :
	foreach ($unit->assetgroups() as $agt) :
		foreach ($agt->children() as $ag) :
			$assetgroups[] = array('id'=>$ag->get('id'), 'title'=>$ag->get('title'));
			foreach ($ag->assets() as $a) :
				if ($a->isPublished()) :
					$a->set('longTitle', $unit->get('title') . ' - ' . $ag->get('title') . ' - ' . $a->get('title'));
					$assets[] = $a;
				endif;
			endforeach;
		endforeach;
	endforeach;
endforeach;

require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'tool.php');

$tools     = \Components\Tools\Models\Tool::getMyTools();
$config    = Component::params('com_courses');
$tool_path = $config->get('tool_path');
?>

<div class="edit-asset">
	<h3>Edit Asset</h3>

	<form action="<?php echo Request::base(true); ?>/api/courses/asset/save" method="POST" class="edit-form">

		<p>
			<label for="title">Title:</label>
			<input type="text" name="title" value="<?php echo $asset->get('title') ?>" placeholder="Asset Title" />
		</p>
		<?php if ($asset->get('type') != 'form') : ?>
		<p>
			<label for="title">URL:</label>
			<input type="text" name="url" value="<?php echo $asset->get('url') ?>" placeholder="Asset URL" />
		</p>
		<?php endif; ?>
		<p>
			<label for="type">Type:</label>
			<select name="type">
				<option value="video"<?php if ($asset->get('type') == 'video') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Video'); ?></option>
				<option value="file"<?php if ($asset->get('type') == 'file') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('File'); ?></option>
				<option value="form"<?php if ($asset->get('type') == 'form') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Form'); ?></option>
				<option value="text"<?php if ($asset->get('type') == 'text') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Text'); ?></option>
				<option value="url"<?php if ($asset->get('type') == 'url') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('URL'); ?></option>
			</select>
		</p>
		<p>
			<label for="subtype">Subtype:</label>
			<select name="subtype">
				<option value="video"<?php if ($asset->get('subtype') == 'video') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Video'); ?></option>
				<option value="embedded"<?php if ($asset->get('subtype') == 'embedded') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Embedded'); ?></option>
				<option value="file"<?php if ($asset->get('subtype') == 'file') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('File'); ?></option>
				<option value="exam"<?php if ($asset->get('subtype') == 'exam') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Exam'); ?></option>
				<option value="quiz"<?php if ($asset->get('subtype') == 'quiz') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Quiz'); ?></option>
				<option value="homework"<?php if ($asset->get('subtype') == 'homework') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Homework'); ?></option>
				<option value="note"<?php if ($asset->get('subtype') == 'note') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Note'); ?></option>
				<option value="wiki"<?php if ($asset->get('subtype') == 'wiki') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Wiki'); ?></option>
				<option value="link"<?php if ($asset->get('subtype') == 'link') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Link'); ?></option>
				<option value="tool"<?php if ($asset->get('subtype') == 'tool') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Tool'); ?></option>
			</select>
		</p>
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

		<?php if ($tool_path
				&& $tools
				&& count($tools) > 0
				&& (($asset->get('type') == 'file' && $asset->get('subtype') == 'file')
					|| ($asset->get('type') == 'url' && $asset->get('subtype') == 'tool'))) : ?>
			<p>
				<label for="tool_param">Launch a tool with this file?</label>
				<input name="tool_param" class="tool-param" type="checkbox" value="1" <?php echo ($asset->get('type') == 'url' && $asset->get('subtype') == 'tool') ? 'checked="checked"' : ''; ?>/>
				<input type="hidden" name="edit_tool_param" value="1" />

				<select class="tool-list" name="tool_alias">
					<?php foreach ($tools as $tool) : ?>
						<?php preg_match('/\/tools\/([0-9a-z]+)\//', $asset->get('url'), $substr); ?>
						<?php $selected = ($substr && isset($substr[1]) && $substr[1] == $tool->alias) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo $tool->alias ?>" <?php echo $selected ?>><?php echo $tool->title ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php endif; ?>

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

		<input type="hidden" name="course_id" value="<?php echo $this->course->get('id') ?>" />
		<input type="hidden" name="original_scope_id" value="<?php echo $this->scope_id ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
		<input type="hidden" name="section_id" value="<?php echo $this->course->offering()->section()->get('id'); ?>" />
		<input type="hidden" name="id" value="<?php echo $asset->get('id') ?>" />

		<input type="button" value="Cancel" class="cancel" />
		<input type="submit" value="Submit" class="submit" />
	</form>
</div>