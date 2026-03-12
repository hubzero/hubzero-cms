{{--
  Support — Message edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Support\Helpers\Permissions::getActions('message');
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(
      Lang::txt('COM_SUPPORT_TICKETS') . ': '
      . Lang::txt('COM_SUPPORT_MESSAGES') . ': ' . $text,
      'support'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('messages');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Left column: Message fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_SUPPORT_MESSAGE_LEGEND') }}">

      <div class="admin-field">
        <label for="field-title" class="label text-base-content">
          {{ Lang::txt('COM_SUPPORT_MESSAGE_SUMMARY') }}
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-message" class="label text-base-content">
          {{ Lang::txt('COM_SUPPORT_MESSAGE_TEXT') }}
          <span class="text-error">*</span>
        </label>
        <textarea name="fields[message]"
                  id="field-message"
                  class="textarea textarea-bordered w-full"
                  rows="10"
                  required>{{ $row->get('message', '') }}</textarea>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <div class="bg-base-100 rounded-box border border-base-300 p-4 space-y-3">
      <p class="text-sm text-muted-foreground">
        {!! Lang::txt('COM_SUPPORT_MESSAGE_TEXT_EXPLANATION') !!}
      </p>
      <dl class="text-sm space-y-2">
        <dt class="font-mono font-semibold">{ticket#}</dt>
        <dd class="text-muted-foreground ml-4">
          {!! Lang::txt('COM_SUPPORT_MESSAGE_TICKET_NUM_EXPLANATION') !!}
        </dd>
        <dt class="font-mono font-semibold">{sitename}</dt>
        <dd class="text-muted-foreground ml-4">{{ Config::get('sitename') }}</dd>
        <dt class="font-mono font-semibold">{siteemail}</dt>
        <dd class="text-muted-foreground ml-4">{{ Config::get('mailfrom') }}</dd>
      </dl>
    </div>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id', 0) }}" />
</x-admin-edit>
