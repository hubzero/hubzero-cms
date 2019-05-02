<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': '. $text, 'tools.png');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('version');

Html::behavior('formvalidation');
Html::behavior('keepalive');
Html::behavior('modal');
Html::behavior('switcher', 'submenu');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu">
						<li><a href="#page-details" id="details" class="active"><?php echo Lang::txt('JDETAILS'); ?></a></li>
						<li><a href="#page-zones" id="zones"><?php echo Lang::txt('COM_TOOLS_FIELDSET_ZONES'); ?></a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="version-document">
		<?php if ($this->getError()) : ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php endif; ?>
		<div id="page-details" class="tab">
			<div class="grid">
				<div class="col span7">
					<fieldset class="adminform">
						<legend><span><?php echo Lang::txt('COM_TOOLS_FIELD_VERSION_DETAILS'); ?></span></legend>

						<div class="input-wrap">
							<label for="field-command"><?php echo Lang::txt('COM_TOOLS_FIELD_COMMAND'); ?>:</label><br />
							<input type="text" name="fields[vnc_command]" id="field-command" value="<?php echo $this->escape(stripslashes($this->row->vnc_command));?>" size="50" />
						</div>

						<div class="input-wrap">
							<label for="field-timeout"><?php echo Lang::txt('COM_TOOLS_FIELD_TIMEOUT'); ?>:</label><br />
							<input type="text" name="fields[vnc_timeout]" id="field-timeout" value="<?php echo $this->escape(stripslashes($this->row->vnc_timeout));?>" size="50" />
						</div>

						<div class="input-wrap">
							<label for="field-hostreq"><?php echo Lang::txt('COM_TOOLS_FIELD_HOSTREQ'); ?>:</label><br />
							<input type="text" name="fields[hostreq]" id="field-hostreq" value="<?php echo $this->escape(stripslashes(implode(', ', $this->row->hostreq)));?>" size="50" />
						</div>

						<div class="input-wrap">
							<label for="field-mw"><?php echo Lang::txt('COM_TOOLS_FIELD_MIDDLEWARE'); ?>:</label><br />
							<input type="text" name="fields[mw]" id="field-mw" value="<?php echo $this->escape(stripslashes($this->row->mw));?>" size="50" />
						</div>

						<div class="input-wrap">
							<label for="field-params"><?php echo Lang::txt('COM_TOOLS_FIELD_PARAMS'); ?>:</label><br />
							<textarea name="fields[params]" id="field-params" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->params));?></textarea>
						</div>
					</fieldset>

					<?php if ($this->doi): ?>
						<fieldset class="adminform">
							<legend><span><?php echo Lang::txt('COM_TOOLS_FIELD_VERSION_DOI'); ?></span></legend>

							<?php if ($err = $this->doi->getError()): ?>
								<div class="input-wrap">
									<p class="warning"><?php echo $err; ?></p>
								</div>
							<?php else: ?>
								<div class="grid">
									<div class="col span5">
										<div class="input-wrap">
											<label for="field-doi_shoulder"><?php echo Lang::txt('COM_TOOLS_FIELD_DOI_SHOULDER'); ?>:</label><br />
											<input type="text" name="doi[doi_shoulder]" id="field-doi_shoulder" value="<?php echo $this->escape($this->doi->doi_shoulder); ?>" />
										</div>
									</div>
									<div class="col span2">
										&nbsp;<br />/
									</div>
									<div class="col span5">
										<div class="input-wrap">
											<label for="field-doi"><?php echo Lang::txt('COM_TOOLS_FIELD_DOI_DOI'); ?>:</label><br />
											<input type="text" name="doi[doi]" id="field-doi" value="<?php echo $this->escape($this->doi->doi); ?>" />
										</div>
									</div>
								</div>

								<div class="input-wrap">
									<label for="field-doi_label"><?php echo Lang::txt('COM_TOOLS_FIELD_DOI_LABEL'); ?>:</label><br />
									<input type="text" name="doi[doi_label]" id="field-doi_label" value="<?php echo $this->escape($this->doi->doi_label); ?>" />
								</div>

								<input type="hidden" name="doi[id]" value="<?php echo $this->escape($this->doi->id); ?>" />
								<input type="hidden" name="doi[rid]" value="<?php echo $this->escape($this->doi->rid); ?>" />
								<input type="hidden" name="doi[local_revision]" value="<?php echo $this->escape($this->row->revision); ?>" />
								<input type="hidden" name="doi[versionid]" value="<?php echo $this->escape($this->row->id); ?>" />
								<input type="hidden" name="doi[alias]" value="<?php echo $this->escape($this->row->toolname); ?>" />
							<?php endif; ?>
						</fieldset>
					<?php endif; ?>
				</div>
				<div class="col span5">
					<table class="meta">
						<tbody>
							<tr>
								<th><?php echo Lang::txt('COM_TOOLS_FIELD_TITLE'); ?>:</th>
								<td><?php echo $this->escape(stripslashes($this->parent->title));?></td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_TOOLS_FIELD_TOOLNAME'); ?>:</th>
								<td><?php echo $this->escape(stripslashes($this->parent->toolname));?></td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_TOOLS_FIELD_VERSION'); ?>:</th>
								<td><?php echo $this->escape($this->row->id);?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<input type="hidden" name="fields[id]" value="<?php echo $this->parent->id; ?>" />
			<input type="hidden" name="fields[version]" value="<?php echo $this->row->id; ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</div>
		<div class="clr"></div>
	</div>
	<div id="page-zones" class="tab">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_TOOLS_FIELDSET_ZONES'); ?></span></legend>
			<?php if ($this->row->get('id')) : ?>
				<iframe width="100%" height="400" name="zoneslist" id="zoneslist" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=versions&task=displayZones&tmpl=component&version=' . $this->row->get('id')); ?>"></iframe>
			<?php endif; ?>
		</fieldset>
	</div>
	<?php echo Html::input('token'); ?>
</form>