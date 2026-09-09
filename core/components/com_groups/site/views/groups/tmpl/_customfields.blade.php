{{--
  Group custom fields rendering partial.

  Variables (passed via $__view->view('_customfields')):
    $customFields  — collection of Field models
    $customAnswers — array of field name => value

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('customfields');

  $xml      = \Components\Groups\Models\Orm\Field::toXml($customFields);
  $formInfo = ['control' => 'customfields'];
  $form     = new Hubzero\Form\Form('application', $formInfo);
  $form->load($xml);
  $form->bind($customAnswers);
@endphp

@foreach($customFields as $field)
  @php
    $formfield = $form->getField($field->get('name'));
  @endphp
  <div class="field-wrap">
    @if(strtolower($formfield->type) !== 'paragraph')
      {!! $formfield->label !!}
    @endif

    @if($field->type === 'textarea')
      @php
        $fieldName     = $field->get('name');
        $fieldValue    = $customAnswers[$fieldName] ?? $field->get('default_value', '');
        $fieldNameAttr = $formInfo['control'] . '[' . $fieldName . ']';
        $fieldIdAttr   = $formInfo['control'] . '_' . $fieldName;
      @endphp
      {!! $__view->editor(
          $fieldNameAttr,
          e($fieldValue),
          35,
          8,
          $fieldIdAttr,
          ['class' => 'minimal no-footer images macros']
      ) !!}
    @else
      {!! $formfield->input !!}
    @endif

    @if($formfield->description && strtolower($formfield->type) !== 'paragraph')
      <span class="text-sm text-base-content/60">{{ $formfield->description }}</span>
    @endif
  </div>
@endforeach
