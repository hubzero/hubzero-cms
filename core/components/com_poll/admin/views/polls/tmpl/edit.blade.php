{{--
  Poll — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $__view->js();

  $canDo = \Components\Poll\Helpers\Permissions::getActions('component');
  $text  = $poll->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_POLL') . ': ' . $text, 'poll');
  if ($poll->id) {
      Toolbar::preview('index.php?option=' . $option . '&task=preview&id=' . $poll->id);
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::save();
      Toolbar::apply();
      Toolbar::spacer();
  }
  if ($poll->id) {
      Toolbar::cancel('cancel', 'COM_POLL_CLOSE');
  } else {
      Toolbar::cancel();
  }
  Toolbar::spacer();
  Toolbar::help('poll');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller=""
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_POLL_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               required
               value="{{ $poll->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">{{ Lang::txt('COM_POLL_FIELD_ALIAS') }}</label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               value="{{ $poll->get('alias', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-lag" class="label">
          {{ Lang::txt('COM_POLL_FIELD_LAG') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[lag]"
               id="field-lag"
               class="input input-bordered w-full"
               required
               value="{{ $poll->get('lag', 86400) }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_POLL_FIELD_LAG_HINT') }}</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_POLL_FIELD_PUBLISHED') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($poll->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($poll->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($poll->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_POLL_FIELD_ACCESS_LEVEL') }}</label>
          <select name="fields[access]" id="field-access"
                  class="select select-bordered w-full">
            {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $poll->get('access')) !!}
          </select>
        </div>

        <div class="admin-field">
          <label for="field-open" class="label">{{ Lang::txt('COM_POLL_FIELD_OPEN') }}</label>
          <select name="fields[open]" id="field-open"
                  class="select select-bordered w-full">
            <option value="0" @selected($poll->get('open') == 0)>{{ Lang::txt('COM_POLL_CLOSED') }}</option>
            <option value="1" @selected($poll->get('open') == 1)>{{ Lang::txt('COM_POLL_OPEN') }}</option>
          </select>
        </div>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Poll options --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_POLL_FIELDSET_OPTIONS') }}" body-class="space-y-3">
        @php
          $i = 0;
          $n = $options->count();
        @endphp
        @foreach($options as $opt)
          <div class="admin-field">
            <label for="polloption{{ $opt->id }}" class="label text-sm">
              {{ Lang::txt('COM_POLL_FIELD_OPTION') }} {{ $i + 1 }}
            </label>
            <input type="text"
                   name="polloption[{{ $opt->id }}]"
                   id="polloption{{ $opt->id }}"
                   class="input input-bordered input-sm w-full"
                   value="{{ str_replace('&#039;', "'", $opt->text) }}" />
          </div>
          @php $i++; @endphp
        @endforeach
        @for(; $i < 12; $i++)
          <div class="admin-field">
            <label for="polloption{{ $i + 1 }}" class="label text-sm">
              {{ Lang::txt('COM_POLL_FIELD_OPTION') }} {{ $i + 1 }}
            </label>
            <input type="text"
                   name="polloption[]"
                   id="polloption{{ $i + 1 }}"
                   class="input input-bordered input-sm w-full"
                   value="" />
          </div>
        @endfor
    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="fields[id]" value="{{ $poll->get('id') }}" />
  <input type="hidden" name="id" value="{{ $poll->get('id') }}" />
  <input type="hidden" name="textfieldcheck" value="{{ $n }}" />
</x-admin-edit>
