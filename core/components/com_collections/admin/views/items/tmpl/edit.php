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

$canDo = \Components\Collections\Helpers\Permissions::getActions('post');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$dir = $this->row->get('id');
if (!$dir)
{
	$dir = 'tmp' . time(); // . rand(0, 100);
}

Toolbar::title(Lang::txt('COM_COLLECTIONS') . ': ' . Lang::txt('COM_COLLECTIONS_ITEMS') . ': ' . $text, 'collection.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('collection');

Html::behavior('switcher', 'submenu');

$this->css()
     ->js('jquery.fileuploader.js', 'system')
     ->js('fileupload.js');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if ($('#field-type').val() == '') {
		alert('<?php echo Lang::txt('COM_COLLECTIONS_ERROR_MISSING_TYPE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
<?php if ($this->row->get('id')) { ?>
	<nav role="navigation" class="sub-navigation">
		<ul id="submenu" class="item-nav">
			<li><a href="#" onclick="return false;" id="idetails" class="active"><?php echo Lang::txt('JDETAILS'); ?></a></li>
			<li><a href="#" onclick="return false;" id="iposts"><?php echo Lang::txt('COM_COLLECTIONS_POSTS'); ?></a></li>
		</ul>
	</nav><!-- / .sub-navigation -->

	<div id="item-document">
		<div id="page-idetails" class="tab">
<?php } ?>

			<div class="grid">
				<div class="col span7">
					<fieldset class="adminform">
						<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

						<div class="input-wrap">
							<label for="field-title"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_TITLE'); ?>:</label><br />
							<input type="text" name="fields[title]" id="field-title" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
						</div>

						<div class="input-wrap">
							<label for="field-url"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_URL'); ?>:</label><br />
							<input type="text" name="fields[url]" id="field-url" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('url'))); ?>" />
						</div>

						<div class="input-wrap">
							<label for="field-description"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_DESCRIPTION'); ?></label><br />
							<?php echo $this->editor('fields[description]', $this->escape($this->row->get('description')),  35, 10, 'field-description', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
						</div>

						<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COLLECTIONS_FIELD_TAGS_HINT'); ?>">
							<label for="field-tags"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_TAGS'); ?>:</label><br />
							<input type="text" name="tags" id="field-tags" value="<?php echo $this->escape(stripslashes($this->row->tags('string'))); ?>" />
							<span class="hint"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_TAGS_HINT'); ?></span>
						</div>
					</fieldset>

					<fieldset class="adminform">
						<div class="input-wrap">
							<div class="asset-uploader">
								<div class="grid">
									<div class="col span6">
										<div id="ajax-uploader" data-txt-instructions="<?php echo Lang::txt('COM_COLLECTIONS_CLICK_OR_DROP_FILE'); ?>" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&no_html=1&controller=media&task=upload'); ?>" data-list="<?php echo Route::url('index.php?option=' . $this->option . '&no_html=1&controller=media&task=list&dir='); ?>">
											<noscript>
												<label for="upload"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_FILE'); ?></label>
												<input type="file" name="upload" id="field-upload" />
											</noscript>
										</div>
									</div>
									<div class="col span6">
										<div id="link-adder" data-txt-delete="<?php echo Lang::txt('JACTION_DELETE'); ?>" data-txt-instructions="<?php echo Lang::txt('COM_COLLECTIONS_CLICK_TO_ADD_LINK'); ?>" data-base="<?php echo Route::url('index.php?option=' . $this->option . '&no_html=1&controller=media&task=delete&dir='); ?>" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&no_html=1&controller=media&task=create&dir='); ?>" data-list="<?php echo Route::url('index.php?option=' . $this->option . '&no_html=1&controller=media&task=list&dir='); ?>">
											<noscript>
												<label for="add-link"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_LINK'); ?></label>
												<input type="text" name="assets[-1][filename]" id="add-link" value="http://" />
												<input type="hidden" name="assets[-1][id]" value="0" />
												<input type="hidden" name="assets[-1][type]" value="link" />
											</noscript>
										</div>
									</div>
								</div>
							</div><!-- / .asset-uploader -->

							<div id="ajax-uploader-list">
								<?php
								$assets = $this->row->assets()->rows();

								if ($assets->count() > 0)
								{
									$i = 0;
									foreach ($assets as $asset)
									{
										$this->view('_asset', 'media')
										     ->set('i', $i)
										     ->set('option', $this->option)
										     ->set('controller', $this->controller)
										     ->set('asset', $asset)
										     ->set('no_html', 1)
										     ->display();

										$i++;
									}
								}
								?>
							</div><!-- / .field-wrap -->
						</div>
					</fieldset>
				</div>
				<div class="col span5">
					<table class="meta">
						<tbody>
							<?php if (!$this->row->isNew()) { ?>
								<tr>
									<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_ID'); ?>:</th>
									<td>
										<?php echo $this->row->get('id'); ?>
									</td>
								</tr>
							<?php } ?>
							<tr>
								<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_TYPE'); ?>:</th>
								<td>
									<?php echo $this->row->get('type', 'file'); ?>
									<input type="hidden" name="fields[type]" id="field-type" value="<?php echo $this->escape($this->row->get('type', 'file')); ?>" />
								</td>
							</tr>
							<?php if ($object_id = $this->row->get('object_id')) { ?>
								<tr>
									<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_OBJECT_ID'); ?>:</th>
									<td>
										<?php echo $object_id; ?>
									</td>
								</tr>
							<?php } ?>
							<tr>
								<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_CREATOR'); ?>:</th>
								<td>
									<?php
									$editor = User::getInstance($this->row->get('created_by'));
									echo $this->escape(stripslashes($editor->get('name')));
									?>
									<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
								</td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_CREATED'); ?>:</th>
								<td>
									<?php echo $this->row->get('created'); ?>
									<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
								</td>
							</tr>
							<?php if ($this->row->get('modified_by')) { ?>
								<tr>
									<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_MODIFIER'); ?>:</th>
									<td>
										<?php
										$modifier = User::getInstance($this->row->get('modified_by'));
										echo $this->escape(stripslashes($modifier->get('name')));
										?>
										<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->escape($this->row->get('modified_by')); ?>" />
									</td>
								</tr>
								<tr>
									<th><?php echo Lang::txt('COM_COLLECTIONS_FIELD_MODIFIED'); ?>:</th>
									<td>
										<?php echo $this->row->get('modified'); ?>
										<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->escape($this->row->get('modified')); ?>" />
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<fieldset class="adminform">
						<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

						<div class="input-wrap">
							<label for="field-state"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_STATE'); ?>:</label><br />
							<select name="fields[state]" id="field-state">
								<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
								<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
								<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
							</select>
						</div>

						<div class="input-wrap">
							<label for="field-access"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_ACCESS'); ?>:</label><br />
							<select name="fields[access]" id="field-access">
								<option value="0"<?php if ($this->row->get('access') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC'); ?></option>
								<option value="1"<?php if ($this->row->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED'); ?></option>
								<option value="4"<?php if ($this->row->get('access') == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE'); ?></option>
							</select>
						</div>
					</fieldset>
				</div>
			</div>

<?php if ($this->row->get('id')) { ?>
		</div>
		<div id="page-iposts" class="tab">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COLLECTIONS_POSTS'); ?></span></legend>

				<iframe height="500" name="grouper" id="grouper" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&tmpl=component&item_id=' . $this->row->get('id') . '&t=' . time()); ?>"></iframe>
			</fieldset>
		</div>
	</div>
<?php } ?>

	<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="dir" id="field-dir" value="<?php echo $dir; ?>" />
	<input type="hidden" name="fields[object_id]" value="<?php echo $this->escape($this->row->get('object_id')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>