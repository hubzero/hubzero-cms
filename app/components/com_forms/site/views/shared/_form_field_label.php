<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field = $this->field;
$helpText = htmlspecialchars($field->get('help_text'));
$label = htmlspecialchars($field->get('label'), ENT_COMPAT);
$isRequired = $field->get('required');
?>

<legend>
	<?php echo $label; ?>

	<?php if (!!$helpText): ?>
		<span class="hasTip fontcon" title="<?php echo $helpText; ?>">
			&#xf075;
		</span>
	<?php endif; ?>

	<?php if($isRequired): ?>
		<span class="required star">
			<?php echo Lang::txt('COM_FORMS_FIELDS_META_REQUIRED'); ?>
		</span>
	<?php endif; ?>
</legend>
