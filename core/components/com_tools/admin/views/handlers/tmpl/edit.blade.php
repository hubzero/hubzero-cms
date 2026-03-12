{{--
  Tool Handler — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Components\Tools\Models\Orm\Tool;
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HANDLERS') . ': ' . $text, 'tools');
  Toolbar::save();
  Toolbar::cancel();

  $__view->css('handlers')->js('handlers');

  $tools = Tool::whereEquals('state', 7)->order('title', 'asc')->rows();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    class="handlers"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

    <div class="admin-field">
      <label for="field-tool" class="label text-base-content">
        {{ Lang::txt('COM_TOOLS_HANDLERS_TOOLNAME') }}
        <span class="text-error">*</span>
      </label>
      <select name="tool" id="field-tool" class="select select-bordered w-full" required>
        @if($tools->count())
          @foreach($tools as $tool)
            <option value="{{ $tool->id }}" @selected($tool->id == $row->tool_id)>
              {{ $tool->title }}
            </option>
          @endforeach
        @else
          <option value="">{{ Lang::txt('COM_TOOLS_HANDLERS_NO_TOOLS') }}</option>
        @endif
      </select>
    </div>

    <div class="admin-field">
      <label for="field-prompt" class="label text-base-content">
        {{ Lang::txt('COM_TOOLS_HANDLERS_PROMPT') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="prompt"
             id="field-prompt"
             class="input input-bordered w-full"
             required
             value="{{ $row->prompt ?? '' }}" />
    </div>

  </x-admin-fieldset>

  {{-- Rules fieldset – JS-driven dynamic rows --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_HANDLERS_RULES') }}">

    {{-- Hidden sample row cloned by handlers.js --}}
    <div class="rule rule-sample hidden">
      <div class="flex gap-4 items-end">
        <div class="admin-field flex-1">
          <label for="field-extension-new" class="label text-base-content">{{ Lang::txt('COM_TOOLS_HANDLERS_EXTENSION') }}</label>
          <input type="text" name="" id="field-extension-new" class="input input-bordered w-full" value="" />
        </div>
        <div class="admin-field w-32">
          <label for="field-quantity-new" class="label text-base-content">{{ Lang::txt('COM_TOOLS_HANDLERS_QUANTITY') }}</label>
          <select name="" id="field-quantity-new" class="select select-bordered w-full">
            @foreach(range(1, 5) as $q)
              <option value="{{ $q }}" @selected($q == 1)>{{ $q }}</option>
            @endforeach
          </select>
        </div>
        <button type="button" class="btn btn-sm btn-ghost delete-rule text-error mb-1">
          {{ Lang::txt('COM_TOOLS_HANDLERS_DELETE_RULE') }}
        </button>
      </div>
    </div>

    <div class="rules space-y-3">
      @foreach($row->rules as $i => $rule)
        <div class="rule">
          <input type="hidden" name="rules[{{ $i }}][id]" value="{{ $rule->id ?? '' }}" />
          <div class="flex gap-4 items-end">
            <div class="admin-field flex-1">
              <label for="field-extension-{{ $i }}" class="label text-base-content">
                {{ Lang::txt('COM_TOOLS_HANDLERS_EXTENSION') }}
              </label>
              <input type="text"
                     name="rules[{{ $i }}][extension]"
                     id="field-extension-{{ $i }}"
                     class="input input-bordered w-full"
                     value="{{ $rule->extension }}" />
            </div>
            <div class="admin-field w-32">
              <label for="field-quantity-{{ $i }}" class="label text-base-content">
                {{ Lang::txt('COM_TOOLS_HANDLERS_QUANTITY') }}
              </label>
              <select name="rules[{{ $i }}][quantity]" id="field-quantity-{{ $i }}" class="select select-bordered w-full">
                @foreach(range(1, 5) as $q)
                  <option value="{{ $q }}" @selected($rule->quantity == $q)>{{ $q }}</option>
                @endforeach
              </select>
            </div>
            <button type="button" class="btn btn-sm btn-ghost delete-rule text-error mb-1">
              {{ Lang::txt('COM_TOOLS_HANDLERS_DELETE_RULE') }}
            </button>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      <a href="#" class="new-rule btn btn-sm btn-outline">
        {{ Lang::txt('COM_TOOLS_HANDLERS_NEW_RULE') }}
      </a>
    </div>

  </x-admin-fieldset>

  <input type="hidden" name="id"   value="{{ $row->id }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
