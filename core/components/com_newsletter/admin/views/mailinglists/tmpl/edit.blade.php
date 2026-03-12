{{--
  Mailing List — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Newsletter\Helpers\Permissions::getActions('mailinglist');
  $text  = ($task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') }}: {{ $text }}"
    icon="list"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  @if(!$row->id)
    <div role="alert" class="alert alert-info mb-4">
      {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MUST_CREATE_BEFORE_ADD') }}
    </div>
  @endif

  <x-admin-fieldset legend="{{ $text }} {{ Lang::txt('COM_NEWSLETTER_LISTS') }}">

      <div class="admin-field">
        <label for="field-name" class="label">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_NAME') }}</label>
        <input type="text"
               name="field[name]"
               id="field-name"
               class="input input-bordered w-full"
               value="{{ $row->name }}" />
      </div>

      <div class="admin-field">
        <label for="field-private" class="label">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY') }}</label>
        <select name="field[private]" id="field-private" class="select select-bordered w-full">
          <option value="0" @selected($row->private == 0)>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PUBLIC') }}</option>
          <option value="1" @selected($row->private == 1)>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PRIVATE') }}</option>
        </select>
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_DESC') }}</label>
        <textarea name="field[description]"
                  id="field-description"
                  class="textarea textarea-bordered w-full"
                  rows="5">{{ $row->description }}</textarea>
      </div>

  </x-admin-fieldset>

  <input type="hidden" name="field[id]" value="{{ $row->id }}" />
</x-admin-edit>
