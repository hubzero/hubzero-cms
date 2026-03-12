{{--
  com_modules — Options partial (params fieldsets)

  Variables: $form (Form object with params fieldsets)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  $fieldSets = $form->getFieldsets('params');
@endphp

@foreach ($fieldSets as $name => $fieldSet)
  @php
    $label       = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MODULES_' . $name . '_FIELDSET_LABEL';
    $description = isset($fieldSet->description) ? trim($fieldSet->description) : '';
    $hidden      = '';
  @endphp
  <details class="admin-fieldset" open>
    <summary class="admin-fieldset-heading">{{ Lang::txt($label) }}</summary>
    <div class="admin-fieldset-body space-y-4">
      @if ($description)
        <p class="text-sm text-muted-foreground">{{ Lang::txt($description) }}</p>
      @endif
      @foreach ($form->getFieldset($name) as $field)
        @if (!$field->hidden)
          <div class="admin-field {{ $field->type == 'Spacer' ? 'admin-field-spacer' : '' }}">
            {!! $field->label !!}
            {!! $field->input !!}
          </div>
        @else
          @php $hidden .= $field->input @endphp
        @endif
      @endforeach
      {!! $hidden !!}
    </div>
  </details>
@endforeach
