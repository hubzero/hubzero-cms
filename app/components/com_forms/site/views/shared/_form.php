<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$action = $this->action;
$disabled = isset($this->disabled) ? $this->disabled : false;
$elements = $this->elements;
$hiddenMetadata = isset($this->hiddenMetadata) ? $this->hiddenMetadata : [];
$noJsNotice = Lang::txt('COM_FORMS_NOTICES_FIELDS_FILL_NO_JS');
$submitClasses = isset($this->submitClasses) ? $this->submitClasses : 'btn';
$submitValue = isset($this->submitValue) ? $this->submitValue : 'Submit';
$title = isset($this->title) ? $this->title : '';
?>

<form action="<?php echo $action; ?>" method="post" id="hubForm">

	<fieldset <?php if ($disabled) echo 'disabled'; ?>>
		<legend><?php echo $title; ?></legend>

		<?php
			foreach($elements as $element):
				$this->view('_form_element', 'shared')
					->set('element', $element)
					->display();
			endforeach;
		?>
	</fieldset>

	<span>
		<?php foreach($hiddenMetadata as $datum): ?>
			<input type="hidden"
				name="<?php echo $datum->name; ?>"
				value="<?php echo $datum->value; ?>">
		<?php	endforeach; ?>
	</span>

	<?php if (!$disabled): ?>
		<div class="button-container">
			<input type="submit"
				value="<?php echo $submitValue; ?>"
				class="<?php echo $submitClasses; ?>">
		</div>
	<?php endif; ?>

</form>

<noscript>
	<h2>
		<?php echo $noJsNotice; ?>
	</h2>
</noscript>
