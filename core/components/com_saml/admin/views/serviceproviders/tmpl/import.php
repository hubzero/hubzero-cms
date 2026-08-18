<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SAML') . ': ' . Lang::txt('COM_SAML_IMPORT_METADATA'), 'saml');
Toolbar::custom('processImport', 'forward', '', 'COM_SAML_IMPORT_PARSE', false);
Toolbar::cancel();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_SAML_IMPORT_METADATA'); ?></span></legend>

		<p><?php echo Lang::txt('COM_SAML_IMPORT_EXPLANATION'); ?></p>

		<div class="input-wrap">
			<label for="field-metadata_url"><?php echo Lang::txt('COM_SAML_IMPORT_URL'); ?>:</label><br />
			<input type="text" name="metadata_url" id="field-metadata_url" value="" placeholder="https://" />
			<span class="hint"><?php echo Lang::txt('COM_SAML_IMPORT_URL_HINT'); ?></span>
		</div>

		<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_IMPORT_XML_HINT')); ?>">
			<label for="field-metadata_xml"><?php echo Lang::txt('COM_SAML_IMPORT_XML'); ?>:</label><br />
			<textarea name="metadata_xml" id="field-metadata_xml" cols="70" rows="15" class="monospace"></textarea>
			<span class="hint"><?php echo Lang::txt('COM_SAML_IMPORT_XML_HINT'); ?></span>
		</div>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="processImport" autocomplete="off" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>
