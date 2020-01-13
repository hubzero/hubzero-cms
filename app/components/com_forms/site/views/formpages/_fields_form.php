<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('fieldsForm')
	->js('formBuilderDomHelper')
	->js('form-builder.min')
	->js('form-builder.min')
	->js('formBuilder')
	->js('comFormsFormBuilder')
	->js('comFormsFieldTranslator')
	->js('api')
	->js('notify')
	->js('objectHelper')
	->js('page')
	->js('fieldsForm');

\Html::behavior('core');

$noJsNotice = Lang::txt('COM_FORMS_NOTICES_FIELDS_NO_JS');
$pageId = $this->pageId;
$submitValue = Lang::txt('COM_FORMS_FIELDS_VALUES_UPDATE_FIELDS');
?>

<fieldset>
	<legend>Fields</legend>

	<div id="form-builder-anchor"></div>

	<form action="">
		<input type="hidden" name="page_id" value="<?php echo $pageId; ?>" />
		<input type="hidden" name="api_endpoint" value="<?php echo $pageId; ?>" />
	</form>

	<noscript>
		<h2>
			<?php echo $noJsNotice; ?>
		</h2>
	</noscript>

</fieldset>

<div class="row button-container">
	<input type="submit"
		id="fields-submit"
		class="btn btn-success"
		value="<?php echo $submitValue; ?>" />
</div>
