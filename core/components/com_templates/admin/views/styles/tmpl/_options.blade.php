{{--
  com_templates — Template options partial (included by styles/edit)

  Variables: $form (Form object with params fieldsets from template config.xml)

  Renders each param fieldset as a <details class="admin-fieldset"> accordion.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $fieldSets = $form->getFieldsets('params');
@endphp

@if (count($fieldSets))
  @foreach ($fieldSets as $name => $fieldSet)
    @php
      $label = !empty($fieldSet->label)
          ? $fieldSet->label
          : 'COM_TEMPLATES_' . $name . '_FIELDSET_LABEL';
    @endphp
    <details class="admin-fieldset">
      <summary class="admin-fieldset-heading">{{ Lang::txt($label) }}</summary>
      <div class="admin-fieldset-body space-y-3">
        @if (isset($fieldSet->description) && trim($fieldSet->description))
          <p class="text-sm text-muted-foreground">{{ Lang::txt($fieldSet->description) }}</p>
        @endif
        @foreach ($form->getFieldset($name) as $field)
          <div class="admin-field">
            @if (!$field->hidden)
              {!! $field->label !!}
            @endif
            {!! $field->input !!}
          </div>
        @endforeach
      </div>
    </details>
  @endforeach
@else
  <div role="alert" class="alert alert-warning text-sm">
    {{ Lang::txt('No options found for this template.') }}
  </div>
@endif
