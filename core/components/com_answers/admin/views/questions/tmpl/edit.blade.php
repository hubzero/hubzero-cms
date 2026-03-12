{{--
  Question — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\User;

  $canDo = \Components\Answers\Helpers\Permissions::getActions('question');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_ANSWERS_TITLE') }}: {{ Lang::txt('COM_ANSWERS_QUESTIONS') }}: {{ $text }}"
    icon="answers"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="question[anonymous]"
                   class="checkbox checkbox-sm"
                   value="1"
                   @checked($row->get('anonymous')) />
            <span>{{ Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="question[email]"
                   class="checkbox checkbox-sm"
                   value="1"
                   @checked($row->get('email')) />
            <span>{{ Lang::txt('COM_ANSWERS_FIELD_NOTIFY') }}</span>
          </label>
        </div>
      </div>

      <div class="admin-field">
        <label for="field-subject" class="label">
          {{ Lang::txt('COM_ANSWERS_FIELD_SUBJECT') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="question[subject]"
               id="field-subject"
               class="input input-bordered w-full"
               maxlength="250"
               required
               value="{{ $row->get('subject', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-question" class="label">
          {{ Lang::txt('COM_ANSWERS_FIELD_QUESTION') }}
        </label>
        {!! $__view->editor(
            'question[question]',
            e($row->get('question')),
            50,
            15,
            'field-question',
            ['class' => 'minimal no-footer', 'buttons' => false]
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-tags" class="label">
          {{ Lang::txt('COM_ANSWERS_FIELD_TAGS') }}
          <span class="text-error">*</span>
        </label>
        <textarea name="question[tags]"
                  id="field-tags"
                  class="textarea textarea-bordered w-full"
                  rows="3">{{ $row->tags('string') }}</textarea>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_ANSWERS_FIELD_TAGS_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Metadata table --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_ANSWERS_FIELD_ID') }}</td>
              <td>
                {{ $row->get('id', 0) }}
                <input type="hidden" name="question[id]" value="{{ $row->get('id') }}" />
              </td>
            </tr>
            @if($row->get('id'))
              <tr>
                <td>{{ Lang::txt('COM_ANSWERS_FIELD_CREATED') }}</td>
                <td>{{ Date::of($row->get('created'))->toLocal() }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_ANSWERS_FIELD_CREATOR') }}</td>
                <td>{{ $row->creator->get('name') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_ANSWERS_PARAMETERS') }}">

        <div class="admin-field">
          <label for="field-created_by" class="label">{{ Lang::txt('COM_ANSWERS_FIELD_CREATOR') }}</label>
          <input type="text"
                 name="question[created_by]"
                 id="field-created_by"
                 class="input input-bordered w-full"
                 maxlength="50"
                 value="{{ $row->get('created_by', User::get('id')) }}" />
        </div>

        <div class="admin-field">
          <label for="field-created" class="label">{{ Lang::txt('COM_ANSWERS_FIELD_CREATED') }}</label>
          {!! Html::input('calendar', 'question[created]', $row->get('created', Date::toSql()), ['id' => 'field-created']) !!}
        </div>

        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_ANSWERS_FIELD_STATE') }}</label>
          <select name="question[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('COM_ANSWERS_STATE_OPEN') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('COM_ANSWERS_STATE_CLOSED') }}</option>
          </select>
        </div>

    </x-admin-fieldset>
  @endslot
</x-admin-edit>
