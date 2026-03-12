{{--
  User Notes — Edit entry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions($row->get('category_id'), $row->get('id'));
  $text  = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_NOTES') }}: {{ $text }}"
    icon="user"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
    <div class="admin-field">
      <label for="field-subject" class="label">
        {{ Lang::txt('COM_MEMBERS_FIELD_SUBJECT') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[subject]"
             id="field-subject"
             class="input input-bordered w-full"
             required
             value="{{ $row->get('subject', '') }}" />
    </div>

    <div class="admin-field">
      <label for="field-body" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_BODY') }}</label>
      {!! $__view->editor('fields[body]', e($row->get('body')), 50, 15, 'field-body', ['class' => 'minimal no-footer']) !!}
    </div>

    <div class="admin-field">
      <label for="field-category_id" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_CATEGORY') }}</label>
      <select name="fields[catid]"
              id="field-category_id"
              class="select select-bordered w-full">
        <option value="0">{{ Lang::txt('JOPTION_SELECT_CATEGORY') }}</option>
        {!! Html::select('options', Html::category('options', 'com_members'), 'value', 'text', $row->get('category_id')) !!}
      </select>
    </div>

    <div class="admin-field">
      <label for="fielduser_id" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_USER') }}</label>
      {!! \Components\Members\Helpers\Admin::getUserInput('fields[user_id]', 'fielduser_id', $row->get('user_id')) !!}
    </div>

    <div class="admin-field">
      <label for="field-state" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_STATE') }}</label>
      <select name="fields[state]"
              id="field-state"
              class="select select-bordered w-full">
        <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
      </select>
    </div>

    <div class="admin-field">
      <label for="field-review_time" class="label">{{ Lang::txt('COM_MEMBERS_FIELD_REVIEW_TIME_LABEL') }}</label>
      @php
        $reviewTime = $row->get('review_time');
        $reviewVal  = ($reviewTime && $reviewTime != '0000-00-00 00:00:00')
            ? e(Date::of($reviewTime)->toLocal('Y-m-d H:i:s'))
            : '';
      @endphp
      {!! Html::input('calendar', 'fields[review_time]', $reviewVal, ['id' => 'field-review_time']) !!}
    </div>

    <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
    <input type="hidden" name="id" value="{{ $row->get('id') }}" />
  </x-admin-fieldset>

  <x-slot name="sidebar">
    @if($row->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_FIELD_ID') }}</td>
            <td>{{ $row->get('id') }}</td>
          </tr>
        </table>
      </x-admin-fieldset>
    @endif
  </x-slot>
</x-admin-edit>
