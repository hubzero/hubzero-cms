{{--
  com_menus items — Options panels partial (request + params + associations fieldsets)

  Replaces Html::sliders() with native <details>/<summary> accordion panels.
  Included by edit.blade.php inside the right column.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $requestFieldsets = $form->getFieldsets('request');
  $paramsFieldsets  = $form->getFieldsets('params');
  $assocFieldsets   = $form->getFieldsets('associations');
@endphp

{{-- Request fieldset (component-specific params) --}}
@if(!empty($requestFieldsets))
  @php $requestFieldset = array_shift($requestFieldsets); @endphp
  @php $requestLabel = !empty($requestFieldset->label) ? $requestFieldset->label : 'COM_MENUS_' . $requestFieldset->name . '_FIELDSET_LABEL'; @endphp
  <details class="admin-fieldset" name="menu-options" open>
    <summary class="admin-fieldset-heading cursor-pointer select-none">
      {{ Lang::txt($requestLabel) }}
    </summary>
    <div class="admin-fieldset-body space-y-2">
      @if(isset($requestFieldset->description) && trim($requestFieldset->description))
        <p class="text-sm text-muted-foreground mb-2">{{ Lang::txt($requestFieldset->description) }}</p>
      @endif
      @php $hiddenRequestFields = ''; @endphp
      @foreach($form->getFieldset('request') as $field)
        @if(!$field->hidden)
          <div class="form-control">
            {!! $field->label !!}
            {!! $field->input !!}
          </div>
        @else
          @php $hiddenRequestFields .= $field->input; @endphp
        @endif
      @endforeach
      {!! $hiddenRequestFields !!}
    </div>
  </details>
@endif

{{-- Params fieldsets (display, advanced, etc.) --}}
@foreach($paramsFieldsets as $name => $fieldSet)
  @php $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_' . $name . '_FIELDSET_LABEL'; @endphp
  <details class="admin-fieldset" name="menu-options"
    @if($loop->first && empty($requestFieldsets)) open @endif>
    <summary class="admin-fieldset-heading cursor-pointer select-none">
      {{ Lang::txt($label) }}
    </summary>
    <div class="admin-fieldset-body space-y-2">
      @if(isset($fieldSet->description) && trim($fieldSet->description))
        <p class="text-sm text-muted-foreground mb-2">{{ Lang::txt($fieldSet->description) }}</p>
      @endif
      @foreach($form->getFieldset($name) as $field)
        <div class="form-control">
          {!! $field->label !!}
          {!! $field->input !!}
        </div>
      @endforeach
    </div>
  </details>
@endforeach

{{-- Associations fieldsets --}}
@foreach($assocFieldsets as $name => $fieldSet)
  @php $label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_' . $name . '_FIELDSET_LABEL'; @endphp
  <details class="admin-fieldset" name="menu-options">
    <summary class="admin-fieldset-heading cursor-pointer select-none">
      {{ Lang::txt($label) }}
    </summary>
    <div class="admin-fieldset-body space-y-2">
      @if(isset($fieldSet->description) && trim($fieldSet->description))
        <p class="text-sm text-muted-foreground mb-2">{{ Lang::txt($fieldSet->description) }}</p>
      @endif
      @foreach($form->getFieldset($name) as $field)
        <div class="form-control">
          {!! $field->label !!}
          {!! $field->input !!}
        </div>
      @endforeach
    </div>
  </details>
@endforeach
