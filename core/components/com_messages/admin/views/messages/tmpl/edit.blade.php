{{--
  com_messages — Compose / reply

  Variables: $item (Message model, new or existing)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_MESSAGES_WRITE_PRIVATE_MESSAGE'), 'inbox');
  Toolbar::save('message.save', 'COM_MESSAGES_TOOLBAR_SEND');
  Toolbar::cancel('message.cancel');
  Toolbar::help('write');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_MESSAGES_WRITE_PRIVATE_MESSAGE') }}">

    <div class="admin-field">
      <label for="field-user_id_to" class="required">
        {{ Lang::txt('COM_MESSAGES_FIELD_USER_ID_TO_LABEL') }}
        <span class="text-error text-xs ml-1">*</span>
      </label>
      @php
        $mc = Event::trigger('hubzero.onGetSingleEntry', [[
            'members',
            'fields[user_id_to]',
            'field-user_id_to',
            'required input input-bordered input-sm w-full',
            (string) $item->get('user_id_to', ''),
        ]]);
      @endphp
      @if (!empty($mc[0]))
        {!! $mc[0] !!}
      @else
        <input type="text"
               name="fields[user_id_to]"
               id="field-user_id_to"
               class="input input-bordered input-sm w-full"
               value="{{ $item->get('user_id_to', '') }}"
               required />
      @endif
      <p class="text-xs text-muted-foreground mt-1">
        {{ Lang::txt('COM_MESSAGES_FIELD_USER_ID_TO_DESC') }}
      </p>
    </div>

    <div class="admin-field">
      <label for="field-subject" class="required">
        {{ Lang::txt('COM_MESSAGES_FIELD_SUBJECT_LABEL') }}
        <span class="text-error text-xs ml-1">*</span>
      </label>
      <input type="text"
             name="fields[subject]"
             id="field-subject"
             class="input input-bordered input-sm w-full"
             maxlength="250"
             value="{{ $item->get('subject', '') }}"
             required />
    </div>

    <div class="admin-field">
      <label for="field-message">
        {{ Lang::txt('COM_MESSAGES_FIELD_MESSAGE_LABEL') }}
      </label>
      <textarea name="message"
                id="field-message"
                class="textarea textarea-bordered w-full font-mono text-sm"
                rows="12"
      >{{ $item->get('message', '') }}</textarea>
    </div>

  </x-admin-fieldset>
</x-admin-edit>
