{{--
  Mailing List — Add emails

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') }}: {{ $list->name }}"
    icon="list"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    enctype="multipart/form-data"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS') }}">

      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_MAILINGLIST') }}</label>
        <p class="text-sm font-semibold">{{ $list->name }}</p>
      </div>

      <div class="admin-field">
        <label for="email_confirmation" class="label">
          {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION') }}
        </label>
        <select name="email_confirmation" id="email_confirmation" class="select select-bordered w-full">
          <option value="-1">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NULL') }}</option>
          <option value="1">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_YES') }}</option>
          <option value="0">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NO') }}</option>
        </select>
      </div>

      <div class="admin-field">
        <label for="email_file" class="label">
          {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_FILE') }}
        </label>
        <input type="file" name="email_file" id="email_file"
               class="file-input file-input-bordered w-full" />
      </div>

      @if(!empty($groups))
        <div class="divider text-sm">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR') }}</div>

        <div class="admin-field">
          <label for="email_group" class="label">
            {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP') }}
          </label>
          <select name="email_group" id="email_group" class="select select-bordered w-full">
            <option value="">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP_OPTION_NULL') }}</option>
            @foreach($groups as $group)
              <option value="{{ $group->gidNumber }}">{{ $group->description }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div class="divider text-sm">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR') }}</div>

      <div class="admin-field">
        <label for="email_box" class="label">
          {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_RAW') }}
        </label>
        <textarea name="email_box"
                  id="email_box"
                  class="textarea textarea-bordered w-full font-mono text-sm"
                  rows="10">{{ $emailBox }}</textarea>
      </div>

  </x-admin-fieldset>

  <input type="hidden" name="mid" value="{{ $list->id }}" />
  <input type="hidden" name="task" value="doaddemail" />
</x-admin-edit>
