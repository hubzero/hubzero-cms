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

// No direct access
defined('_HZEXEC_') or die();

$this->css();
$this->js();

$site = str_replace('/administrator', '', rtrim(Request::base(), DS));

$canDo = \Components\Publications\Helpers\Permissions::getActions('item');

$text = ($this->task == 'edit'
	? Lang::txt('JACTION_EDIT') . ' #' . $this->model->get('id') . ' (v.' . $this->model->get('version_label') . ')'
	: Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

$database = App::get('db');

// Get pub category
$rt = $this->model->category();

// Parse data
$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->model->get('metadata'), $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = $match[2];
	}
}

$customFields = $rt->customFields && $rt->customFields != '{"fields":[]}' ? $rt->customFields : '{"fields":[{"default":"","name":"citations","label":"Citations","type":"textarea","required":"0"}]}';

$customFields = $this->model->_curationModel->getMetaSchema();

include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'elements.php');

$elements = new \Components\Publications\Models\Elements($data, $customFields);
$fields   = $elements->render();
$schema   = $elements->getSchema();

$canedit = 1;
$now     = Date::toSql();
$status  = $this->model->getStatusName();

$rating = $this->model->get('master_rating') == 9.9 ? 0.0 : $this->model->get('master_rating') ;
$params = $this->model->params;

// Available panels and default config
$typeParams = $this->model->masterType()->_params;
$panels = array(
	'authors'   => $typeParams->get('show_authors', 2),
	'audience'  => $typeParams->get('show_audience', 0),
	'gallery'   => $typeParams->get('show_gallery', 1),
	'tags'      => $typeParams->get('show_tags', 1),
	'license'   => $typeParams->get('show_license', 2),
	'notes'     => $typeParams->get('show_notes', 1),
	'metadata'  => $typeParams->get('show_metadata', 1),
	'submitter' => $typeParams->get('show_submitter', 0)
);

?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'resetrating') {
		if (confirm('<?php echo Lang::txt('COM_PUBLICATIONS_CONFIRM_RATINGS_RESET'); ?>')) {
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if (pressbutton == 'saveorder') {
		submitform( 'saveauthororder' );
		return;
	}

	if (pressbutton == 'publish') {
		form.admin_action.value = 'publish';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'revert') {
		form.admin_action.value = 'revert';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'message') {
		form.admin_action.value = 'message';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'unpublish') {
		form.admin_action.value = 'unpublish';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'republish') {
		form.admin_action.value = 'republish';
		submitform( 'save' );
		return;
	}

	// do field validation
	if (form.title.value == '') {
		alert('<?php echo Lang::txt('COM_PUBLICATIONS_ERROR_MISSING_TITLE'); ?>');
	}
	else {
		submitform( pressbutton );
	}
}

function popratings()
{
	window.open("<?php echo Route::url('index.php?option=' . $this->option . '&task=ratings&id=' . $this->model->id . '&no_html=1'); ?>", 'ratings', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no');
	return false;
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="title" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>" />
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php
					// Draw category list
					$this->view('_selectcategory')
					     ->set('categories', $this->model->category()->getContribCategories())
					     ->set('value', $this->model->get('category'))
					     ->set('name', 'category')
					     ->set('showNone', '')
					     ->display();
					?>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="alias" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->model->get('alias'))); ?>" />
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_SYNOPSIS'); ?>:</label>
					<textarea name="abstract" id="pub-abstract" cols="40" rows="3" class="pubinput"><?php echo preg_replace("/\r\n/", "\r", trim($this->model->get('abstract'))); ?></textarea>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DESCRIPTION'); ?>:</label>
					<?php
						echo $this->editor('description', $this->escape(stripslashes($this->model->get('description'))), '40', '10', 'pub_description');
					?>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_METADATA'); ?></span></legend>
				<div class="input-wrap">
					<?php echo $fields ? $fields : '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_METADATA_FIELDS') . '</p>'; ?>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_NOTES'); ?></span></legend>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_NOTES'); ?>:</label>
					<?php echo $this->editor('release_notes', $this->escape(stripslashes($this->model->get('release_notes'))), '20', '10', 'notes', array('class' => 'minimal no-footer')); ?>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELDSET_AUTHORS'); ?></span> <span class="sidenote add"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=addauthor&pid=' . $this->model->get('id') . '&vid=' . $this->model->get('version_id') ); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_ADD_AUTHOR'); ?></a></span></legend>

				<fieldset>
				<div class="input-wrap" id="publiction-authors">
					<?php
					// Draw author list
					$this->view('_selectauthors')
					     ->set('authNames', $this->model->authors())
					     ->set('option', $this->option)
					     ->display();
					?>
				</div>
				</fieldset>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELDSET_TAGS'); ?></span></legend>

				<div class="input-wrap">
					<?php
					$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->model->getTagsForEditing(0, 0, true))) );
					if (count($tf) > 0) {
						echo $tf[0];
					} else { ?>
						<input type="text" name="tags" id="actags" value="<?php echo $this->model->getTagsForEditing(); ?>" />
					<?php } ?>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELDSET_LICENSE'); ?></span></legend>

				<div class="input-wrap">
					<label for="license_type"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_LICENSE_TYPE'); ?>:</label>
					<?php // Draw license selector
					$this->view('_selectlicense')
					     ->set('licenses', $this->licenses)
					     ->set('selected', $this->model->license())
					     ->display();
					?>
				</div>
				<div class="input-wrap">
					<label for="license_text"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_LICENSE_TEXT'); ?>:</label>
					<textarea name="license_text" id="license_text" cols="40" rows="5" class="pubinput"><?php echo preg_replace("/\r\n/", "\r", trim($this->model->get('license_text'))); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID'); ?></th>
						<td><?php echo $this->model->get('id'); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CREATED'); ?></th>
						<td>
							<?php echo $this->model->created('date'); ?>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CREATOR'); ?></th>
						<td>
							<?php echo $this->model->creator()->get('name', Lang::txt('(unknown)')); ?>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_PROJECT'); ?></th>
						<td>
							<?php echo $this->model->project()->get('title'); ?>
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TYPE'); ?></th>
						<td>
							<?php echo $this->model->_type->type; ?>
						</td>
					</tr>
					<?php if ($this->model->isPublished() || $this->model->isUnpublished()) { ?>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_RANKING'); ?>:</th>
							<td><?php echo $this->model->get('master_ranking'); ?>/10
								<?php if ($this->model->get('master_ranking') != '0') { ?>
									<input type="button" name="reset_ranking" id="reset_ranking" value="Reset ranking" onclick="submitbutton('resetranking');" />
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_RATING'); ?>:</th>
							<td><?php echo $rating . '/5.0 (' . $this->model->get('master_times_rated') . ' reviews)'; ?>
							<?php if ( $rating != '0.0' ) { ?>
								<input type="button" name="reset_rating" id="reset_rating" value="Reset rating" onclick="submitbutton('resetrating');" />
							<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELDSET_VERSION'); ?></span></legend>
				<table>
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_VERSION_ID'); ?></th>
							<td>
								<?php echo $this->model->get('version_id'); ?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_VERSION'); ?></th>
							<td>
								<?php echo $this->model->get('version_label') . ' (' . $status . ')'; ?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_URL'); ?></th>
							<td><a href="<?php echo trim($site, DS) . DS . 'publications' . DS . $this->model->get('id') . DS . $this->model->get('version_number'); ?>" target="_blank"><?php echo trim($site, DS) . DS . 'publications' . DS . $this->model->get('id') . DS . $this->model->get('version_number'); ?></a></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_MODIFIED'); ?></th>
							<td><?php echo $this->model->modified('date'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_MODIFIED_BY'); ?></th>
							<td><?php echo $this->model->modifier()->get('name', Lang::txt('(unknown)')); ?></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELDSET_PUBLISHING'); ?></span></legend>
				<div class="input-wrap">
					<label for="field-published"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_STATUS'); ?>:</label><br />
					<select name="state" id="field-published">
						<option value="3"<?php echo ($this->model->get('state') == 3) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT'); ?></option>
						<option value="4"<?php echo ($this->model->get('state') == 4) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_READY'); ?></option>
						<option value="5"<?php echo ($this->model->get('state') == 5) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PENDING'); ?></option>
						<option value="7"<?php echo ($this->model->get('state') == 7) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_WIP'); ?></option>
						<option value="1"<?php echo ($this->model->get('state') == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED'); ?></option>
						<option value="0"<?php echo ($this->model->get('state') == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED'); ?></option>
						<option value="2"<?php echo ($this->model->get('state') == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_DELETED'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="access"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ACCESS'); ?>:</label>
					<?php
					// Draw access select list
					$this->view('_selectaccess')
					     ->set('as', 'Public,Registered,Private')
					     ->set('value', $this->model->get('master_access'))
					     ->display();
					?>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_GROUP_OWNER'); ?>:</label>
					<?php
					// Draw group selector
					$this->view('_selectgroup')
					     ->set('groups', $this->groups)
					     ->set('groupOwner', $this->model->project()->groupOwner())
					     ->set('value', $this->model->groupOwner()->gidNumber)
					     ->display(); ?>
				</div>
				<div class="input-wrap">
					<label for="publish_up"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_PUBLISH_DATE'); ?>:</label><br />
					<?php echo Html::input('calendar', 'published_up', ($this->model->version->published_up != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->model->version->published_up)->toLocal('Y-m-d H:i:s')) : '')); ?>
				</div>
				<div class="input-wrap">
					<label for="publish_down"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_UNPUBLISH_DATE'); ?>:</label><br />
					<?php
						$down = 'Never';
						if (strtolower($this->model->version->published_down) != Lang::txt('COM_PUBLICATIONS_NEVER'))
						{
							$down = $this->model->version->published_down != '0000-00-00 00:00:00'
								? Date::of($this->model->version->published_down)->toLocal('Y-m-d H:i:s') : NULL;
						}
					?>
					<?php echo Html::input('calendar', 'published_down', $down); ?>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DOI'); ?>:</label>
					<input type="text" id="doi" name="doi" value="<?php echo $this->model->doi; ?>" />
				</div>

				<div class="input-wrap">
					<table class="admintable">
						<tbody>
							<?php if ($this->model->submitter()) { ?>
								<tr>
									<td class="paramlist_key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTER'); ?>:</td>
									<td><?php echo $this->model->submitter()->name; ?></td>
								</tr>
							<?php } ?>
							<?php if ($this->model->isPending()) { ?>
								<tr>
									<td class="paramlist_key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTED'); ?>:</td>
									<td><?php echo $this->model->submitted; ?></td>
								</tr>
							<?php } else if ($this->model->isPublished() || $this->model->isUnpublished())  { ?>
								<?php if ($this->model->submitted()) { ?>
										<tr>
											<td class="paramlist_key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTED'); ?></td>
											<td><?php echo $this->model->submitted('datetime'); ?></td>
										</tr>
								<?php } ?>
								<?php if ($this->model->accepted()) { ?>
										<tr>
											<td class="paramlist_key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ACCEPTED'); ?></td>
											<td><?php echo $this->model->accepted('datetime'); ?></td>
										</tr>
								<?php } ?>
							<?php }  ?>
							<tr>
								<td class="paramlist_key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_BUNDLE'); ?></td>
								<td>
									<?php if (file_exists($this->model->bundlePath())) { ?>
										<a href="<?php echo trim($site, DS) . '/publications/' . $this->model->get('id') . DS . 'serve' . DS . $this->model->get('version_number') . '/?render=archive'; ?>" class="archival"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_BUNDLE'); ?></a> &nbsp;&nbsp;<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=archive&pid=' . $this->model->get('id') . '&vid=' . $this->model->get('version_id') . '&version=' . $this->model->versionAlias(), false); ?>">[<?php echo Lang::txt('COM_PUBLICATIONS_REPACKAGE'); ?>]</a>
									<?php  }  else { ?>
									<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=archive&pid=' . $this->model->get('id') . '&vid=' . $this->model->get('version_id') . '&version=' . $this->model->versionAlias(), false); ?>" class="archival"><?php echo Lang::txt('COM_PUBLICATIONS_PRODUCE_ARCHIVAL'); ?></a>
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_MANAGEMENT_OPTIONS'); ?></span></legend>
				<div class="input-wrap">
					<textarea name="message" id="message" rows="5" cols="50"></textarea>
					<input type="hidden" name="admin_action" value="" />
					<input type="submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_ACTION_SEND_MESSAGE'); ?>" class="btn" id="do-message" onclick="submitbutton('message')" />
					<?php if ($this->model->isPublished()) { ?>
						<input type="submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_ACTION_UNPUBLISH_VERSION'); ?>" class="btn" id="do-unpublish" onclick="submitbutton('unpublish')" />
					<?php } else if ($this->model->isUnpublished()) { ?>
						<input type="submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_ACTION_REPUBLISH_VERSION'); ?>" class="btn" id="do-republish" onclick="submitbutton('republish')" />
					<?php } else if ($this->model->isPending()) { ?>
						<input type="submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_ACTION_APPROVE_AND_PUBLISH'); ?>" class="btn" id="do-publish" onclick="submitbutton('publish')" />
						<input type="submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_ACTION_REVERT_TO_DRAFT'); ?>" class="btn" id="do-revert" onclick="submitbutton('revert')" />
					<?php } ?>
				</div>
			</fieldset>

			<?php
			echo Html::sliders('start', 'content-panel');
			echo Html::sliders('panel', Lang::txt('COM_PUBLICATIONS_FIELDSET_PARAMETERS'), 'params-page');
			?>
			<table class="admintable">
				<tbody>
					<?php
						foreach ($panels as $panel => $val)
						{
							?>
							<tr>
								<td class="key"><?php echo ucfirst($panel); ?>:</td>
								<td>
									<select name="params[show_<?php echo $panel; ?>]">
										<option value="0" <?php echo ($params->get('show_' . $panel, $val) == 0) ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_HIDE'); ?></option>
										<option value="1" <?php echo ($params->get('show_' . $panel, $val) > 0) ? ' selected="selected"':''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_SHOW'); ?></option>
									</select>
								</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
			<?php echo Html::sliders('end'); ?>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELDSET_CONTENT'); ?></span></legend>
				<fieldset>
					<div class="input-wrap">
						<?php
						// Draw content
						$this->view('_selectcontent')
						     ->set('pub', $this->model)
						     ->set('option', $this->option)
						     ->display();
						?>
					</div>
				</fieldset>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
	<input type="hidden" name="version" value="<?php echo $this->model->versionAlias(); ?>" />
	<input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>
