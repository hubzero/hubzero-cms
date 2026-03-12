{{--
  Response — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\User;

  $canDo = \Components\Answers\Helpers\Permissions::getActions('answer');
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

      <div class="admin-field">
        <label class="label cursor-pointer justify-start gap-2">
          <input type="checkbox"
                 name="answer[anonymous]"
                 class="checkbox checkbox-sm"
                 value="1"
                 @checked($row->get('anonymous')) />
          <span>{{ Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS') }}</span>
        </label>
      </div>

      <div class="admin-field">
        <label for="field-question" class="label">{{ Lang::txt('COM_ANSWERS_FIELD_QUESTION') }}</label>
        <input type="text"
               id="field-question"
               class="input input-bordered w-full"
               disabled
               readonly
               value="{{ strip_tags($question->get('subject')) }}" />
      </div>

      <div class="admin-field">
        <label for="field-answer" class="label">
          {{ Lang::txt('COM_ANSWERS_FIELD_ANSWER') }}
          <span class="text-error">*</span>
        </label>
        {!! $__view->editor(
            'answer[answer]',
            e($row->get('answer')),
            50,
            15,
            'field-answer',
            ['class' => 'required minimal no-footer', 'buttons' => false]
        ) !!}
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Metadata table --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_ANSWERS_FIELD_ID') }}</td>
              <td>{{ $row->get('id', 0) }}</td>
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
            <tr>
              <td>{{ Lang::txt('COM_ANSWERS_FIELD_HELPFUL') }}</td>
              <td>
                <span class="text-[#15803d] font-medium">+{{ $row->get('helpful', 0) }}</span>
                <span class="text-[#b91c1c] font-medium">-{{ $row->get('nothelpful', 0) }}</span>
                @if($row->get('helpful') > 0 || $row->get('nothelpful') > 0)
                  <input type="button"
                         name="reset_helpful"
                         id="reset_helpful"
                         class="btn btn-xs btn-ghost ml-2"
                         value="{{ Lang::txt('COM_ANSWERS_FIELD_RESET') }}"
                         data-confirm="{{ Lang::txt('COM_ANSWERS_CONFIRM_RESET') }}" />
                @endif
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="answer[state]"
                   class="checkbox checkbox-sm"
                   value="1"
                   @checked($row->get('state') == 1) />
            <span>{{ Lang::txt('COM_ANSWERS_FIELD_ACCEPT') }}</span>
          </label>
          <p class="text-xs text-muted-foreground mt-1">
            {{ $row->get('state') == 1 ? Lang::txt('COM_ANSWERS_STATE_ACCEPTED') : Lang::txt('COM_ANSWERS_STATE_UNACCEPTED') }}
          </p>
        </div>

        <div class="admin-field">
          <label for="field-created_by" class="label">{{ Lang::txt('COM_ANSWERS_FIELD_CREATOR') }}</label>
          <input type="text"
                 name="answer[created_by]"
                 id="field-created_by"
                 class="input input-bordered w-full"
                 maxlength="50"
                 value="{{ $row->get('created_by', User::get('id')) }}" />
        </div>

        <div class="admin-field">
          <label for="field-created" class="label">{{ Lang::txt('COM_ANSWERS_FIELD_CREATED') }}</label>
          {!! Html::input('calendar', 'answer[created]', $row->get('created', Date::toSql()), ['id' => 'field-created']) !!}
        </div>

    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="answer[question_id]" value="{{ $question->get('id') }}" />
  <input type="hidden" name="answer[id]" value="{{ $row->get('id') }}" />
</x-admin-edit>
