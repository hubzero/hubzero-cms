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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('resource');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') . ' #' . $this->row->id : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . $text, 'resources');
if ($canDo->get('core.edit'))
{
	Toolbar::spacer();
	Toolbar::save();
}
Toolbar::cancel();

if ($this->row->standalone == 1)
{
	$database = App::get('db');

	$type = $this->row->type;

	$data = $this->row->fields(); /*array();
	preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->row->fulltxt, $matches, PREG_SET_ORDER);
	if (count($matches) > 0)
	{
		foreach ($matches as $match)
		{
			$data[$match[1]] = stripslashes($match[2]);
		}
	}
	$this->row->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->row->fulltxt);
	$this->row->fulltxt = trim($this->row->fulltxt);
	$this->row->fulltxt = ($this->row->fulltxt) ? trim(stripslashes($this->row->fulltxt)): trim(stripslashes($this->row->introtext));*/
}

// Build the path for uploading files
$path = \Components\Resources\Helpers\Html::dateToPath($this->row->created);
if ($this->row->id) {
	$dir_id = \Components\Resources\Helpers\Html::niceidformat($this->row->id);
} else {
	$dir_id = time() . rand(0, 10000);
}

$time = $this->row->attribs->get('timeof', '');
$time = strtotime($time) === false ? null : $time;

?>

<?php
$this->view('_edit_script')
	->set('rconfig', $this->rconfig)
	->set('row', $this->row)
	->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

		<?php
		if ($this->row->type->isForTools())
		{
			$this->view('_edit_tool_fields')
				->set('row', $this->row)
				->display();
		}
		else
		{
			$this->view('_edit_non_tool_fields')
				->set('licenses', $this->licenses)
				->set('lists', $this->lists)
				->set('row', $this->row)
				->set('time', $time)
				->display();
		}
		?>
			</fieldset>

		<?php if ($this->row->standalone == 1 && !$this->row->type->isForTools()) { ?>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('Custom fields'); ?></span></legend>
				<div class="input-wrap" id="resource-custom-fields">
					<?php
					include_once Component::path('com_resources') . DS . 'models' . DS . 'elements.php';
					$elements = new \Components\Resources\Models\Elements($data, $type->customFields);
					echo $elements->render();
					?>
				</div>
			</fieldset>
		<?php } ?>
		</div>
		<div class="col span5">
		<?php if ($this->row->id) { ?>
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_ID'); ?></th>
						<td><?php echo $this->row->id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_CREATED'); ?></th>
						<td>
							<?php echo Date::of($this->row->created)->toLocal(Lang::txt('DATE_FORMAT_LC2')); ?>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_CREATOR'); ?></th>
						<td>
							<?php echo $this->escape(User::getInstance($this->row->created_by)->get('name')); ?>
							<input type="hidden" name="created_by_id" value="<?php echo $this->row->created_by; ?>" />
						</td>
					</tr>
				<?php if ($this->row->modified != '0000-00-00 00:00:00') { ?>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_MODIFIED'); ?></th>
						<td>
							<?php echo Date::of($this->row->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2')); ?>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_MODIFIER'); ?></th>
						<td>
							<?php echo $this->escape(User::getInstance($this->row->modified_by)->get('name')); ?>
							<input type="hidden" name="modified_by_id" value="<?php echo $this->row->modified_by; ?>" />
						</td>
					</tr>
				<?php } ?>
				<?php if ($this->row->standalone == 1) { ?>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_RANKING'); ?></th>
						<td>
							<?php echo $this->row->ranking; ?>/10
							<?php if ($this->row->ranking != '0') { ?>
								<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" />
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_RATING'); ?></th>
						<td>
							<?php echo $this->row->rating . '/5.0 (' . $this->row->times_rated . ')'; ?>
							<?php if ($this->row->rating != '0.0') { ?>
								<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" />
								<a onclick="popratings();" href="#"><?php echo Lang::txt('COM_RESOURCES_VIEW'); ?></a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
					<tr>
						<th><?php echo Lang::txt('COM_RESOURCES_FIELD_HITS'); ?></th>
						<td>
							<?php echo $this->row->hits; ?>
							<?php if ($this->row->hits) { ?>
								<input type="button" name="reset_hits" id="field-reset_hits" value="Reset Hit Count" onclick="submitbutton('resethits');" />
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php } ?>

		<?php if ($this->row->standalone == 1 && !$this->row->type->isForTools()) { ?>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_FIELDSET_CONTRIBUTORS'); ?></span></legend>

				<div class="input-wrap" id="resource-authors">
					<?php echo $this->lists['authors']; ?>
				</div>
			</fieldset>
		<?php } ?>

		<?php
			echo Html::sliders('start', 'content-pane');
			echo Html::sliders('panel', Lang::txt('COM_RESOURCES_FIELDSET_PUBLISHING'), 'publish-page');
		?>
			<div class="paramlist">
				<div class="input-wrap">
					<input type="checkbox" name="fields[standalone]" id="field-standalone" value="1" <?php echo ($this->row->standalone == 1) ? 'checked="checked"' : ''; ?> />
					<label for="field-standalone"><?php echo Lang::txt('COM_RESOURCES_FIELD_STANDALONE'); ?></label>
				</div>
				<div class="input-wrap">
					<label for="field-published"><?php echo Lang::txt('COM_RESOURCES_FIELD_STATUS'); ?>:</label><br />
					<select name="fields[published]" id="field-published">
						<option value="2"<?php echo ($this->row->published == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL'); ?></option>
						<option value="5"<?php echo ($this->row->published == 5) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_INTERNAL'); ?></option>
						<option value="3"<?php echo ($this->row->published == 3) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_PENDING'); ?></option>
						<option value="0"<?php echo ($this->row->published == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php echo ($this->row->published == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="4"<?php echo ($this->row->published == 4) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>
				<?php if ($this->row->standalone == 1) { ?>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_RESOURCES_FIELD_GROUP'); ?>:</label><br />
						<?php echo $this->lists['groups']; ?>
					</div>
				<?php } ?>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_RESOURCES_FIELD_ACCESS'); ?>:</label><br />
						<?php echo $this->lists['access']; ?>
					</div>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_RESOURCES_FIELD_CREATOR'); ?>:</label><br />
						<?php echo $this->lists['created_by']; ?>
					</div>
				<?php // if ($this->row->standalone == 1) { ?>
					<div class="input-wrap">
						<label for="publish_up"><?php echo Lang::txt('COM_RESOURCES_FIELD_PUBLISH_UP'); ?>:</label><br />
						<?php $up = ($this->row->publish_up && $this->row->publish_up != '0000-00-00 00:00:00' ? Date::of($this->row->publish_up)->toLocal('Y-m-d H:i:s') : ''); ?>
						<?php echo Html::input('calendar', 'fields[publish_up]', $up); ?>
					</div>
					<div class="input-wrap">
						<label for="publish_down"><?php echo Lang::txt('COM_RESOURCES_FIELD_PUBLISH_DOWN'); ?>:</label><br />
						<?php
							$down = Lang::txt('COM_RESOURCES_NEVER');
							if ($this->row->publish_down && $this->row->publish_down != '0000-00-00 00:00:00' && $this->row->publish_down != Lang::txt('COM_RESOURCES_NEVER'))
							{
								$down = Date::of($this->row->publish_down)->toLocal('Y-m-d H:i:s');
							}
						?>
						<?php echo Html::input('calendar', 'fields[publish_down]', $down); ?>
					</div>
				<?php // } ?>
			</div>
		<?php
			echo Html::sliders('panel', Lang::txt('COM_RESOURCES_FIELDSET_FILES'), 'file-page');
		?>
			<p>
				<label>
					<?php echo Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED'); ?>:
					<select name="fileoptions" id="fileoptions">
						<option value="2"><?php echo Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED_MAIN'); ?></option>
						<option value="3"><?php echo Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED_IMG'); ?></option>
						<option value="4"><?php echo Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED_LINKED'); ?></option>
					</select>
				</label>
				<input type="button" value="<?php echo Lang::txt('COM_RESOURCES_APPLY'); ?>" onclick="doFileoptions();" />
			</p>
			<iframe width="100%" height="400" name="filer" id="filer" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&tmpl=component&listdir=' . $path . DS . $dir_id); ?>"></iframe>
			<input type="hidden" name="tmpid" value="<?php echo $dir_id; ?>" />
		<?php
			if ($this->row->standalone == 1) {
				echo Html::sliders('panel', Lang::txt('COM_RESOURCES_FIELDSET_TAGS'), 'tags-page');
				?>
				<textarea name="tags" id="tags" cols="35" rows="6"><?php echo $this->lists['tags']; ?></textarea>
				<?php echo Html::sliders('panel', Lang::txt('COM_RESOURCES_FIELDSET_BADGES'), 'badges-page'); ?>
				<textarea name="badges" id="badges" cols="35" rows="6"><?php echo $this->lists['badges']; ?></textarea>
				<?php
				echo Html::sliders('panel', Lang::txt('COM_RESOURCES_FIELDSET_PARAMETERS'), 'params-page');
				echo '<fieldset class="paramlist">' . $this->params->render() . '</fieldset>';
			} else {
				echo Html::sliders('panel', Lang::txt('COM_RESOURCES_FIELDSET_PARAMETERS'), 'params-page');
				?>
				<div class="input-wrap">
					<label for="param-link_action"><?php echo Lang::txt('COM_RESOURCES_FIELD_LINK_ACTION_HINT'); ?>:</label><br />
					<select name="params[link_action]" id="param-link_action">
						<option value="0"<?php if (!$this->params->get('link_action')) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_DEFAULT'); ?></option>
						<option value="1"<?php if ($this->params->get('link_action') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_NEW_WINDOW'); ?></option>
						<option value="2"<?php if ($this->params->get('link_action') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_LIGHTBOX'); ?></option>
						<option value="3"<?php if ($this->params->get('link_action') == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_DOWNLOAD'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="param-restrict_direct_access"><?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_HINT'); ?>:</label><br />
					<select name="params[restrict_direct_access]" id="param-link_action">
						<option value="0"<?php if (!$this->params->get('restrict_direct_access')) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_DEFAULT'); ?></option>
						<option value="1"<?php if ($this->params->get('restrict_direct_access') == 1) { echo ' selected="selected"'; } ?>>
							<?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_NO'); ?>
						</option>
						<option value="2"<?php if ($this->params->get('restrict_direct_access') == 2) { echo ' selected="selected"'; } ?>>
							<?php echo Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_YES'); ?>
						</option>
					</select>
				</div>
				<?php
			}

			echo Html::sliders('end');
		?>

		</div>
	</div>

	<input type="hidden" name="id" id="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
	<input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>
