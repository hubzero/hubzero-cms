<?php
	$this->js('customfields');
	$xml = Components\Groups\Models\Orm\Field::toXml($this->customFields);
	$formInfo = array('control' => 'customfields');
	$form = new Hubzero\Form\Form('application', $formInfo);
	$form->load($xml);
	$form->bind($this->customAnswers);
	foreach ($this->customFields as $field)
	{
		echo '<div class="field-wrap">';
		$formfield = $form->getField($field->get('name'));
		if (strtolower($formfield->type) != 'paragraph')
		{
			echo $formfield->label;
		}
		if ($field->type == 'textarea')
		{
			$fieldName = $field->get('name');
			$fieldValue = isset($this->customAnswers[$fieldName]) ? $this->customAnswers[$fieldName] : $field->get('default_value', '');
			$fieldNameAttr = $formInfo['control'] . '[' . $fieldName . ']';
			$fieldIdAttr = $formInfo['control'] . '_' . $fieldName;
			echo $this->editor($fieldNameAttr, $this->escape($fieldValue), 35, 8, $fieldIdAttr, array('class' => 'minimal no-footer images macros'));
		}
		else
		{
			echo $formfield->input;
		}

		if ($formfield->description && strtolower($formfield->type) != 'paragraph')
		{
			echo '<span class="hint">' . $formfield->description . '</span>';
		}
		echo '</div>';
	}
