{{--
  Mailing List Email — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;

  $text = ($task == 'editemail' || $task == 'editEmail')
      ? Lang::txt('Edit') : Lang::txt('New');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('Newsletter Mailing List Email') }}: {{ $text }}"
    icon="list"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ $text }} {{ Lang::txt('Mailing List Email') }}">

      <div class="admin-field">
        <label class="label">{{ Lang::txt('Mailing List') }}</label>
        <p class="text-sm font-semibold">{{ $list->name }}</p>
      </div>

      <div class="admin-field">
        <label for="field-email" class="label">{{ Lang::txt('Email') }}</label>
        <input type="text"
               name="fields[email]"
               id="field-email"
               class="input input-bordered w-full"
               value="{{ $email->email }}" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('Date Added') }}</td>
              <td>{{ $email->date_added ? Date::of($email->date_added)->format('M d, Y @ g:ia') : '' }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('Confirmed?') }}</td>
              <td>{{ $email->confirmed ? Lang::txt('JYES') : Lang::txt('JNO') }}</td>
            </tr>
            @if($email->confirmed && $email->date_confirmed)
              <tr>
                <td>{{ Lang::txt('Date Confirmed') }}</td>
                <td>{{ Date::of($email->date_confirmed)->format('M d, Y @ g:ia') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[mid]" value="{{ $list->id }}" />
  <input type="hidden" name="fields[id]" value="{{ $email->id }}" />
  <input type="hidden" name="task" value="saveemail" />
</x-admin-edit>
