{{--
  Courses — Coupon Codes admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->exists() ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_COUPON_CODES') . ': ' . $text,
      'courses'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::save();
  }
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
    <x-form-section title="{{ Lang::txt('JDETAILS') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_SECTION') }}"
          inputId="field-section_id"
          required
      >
        <select name="fields[section_id]" id="field-section_id" class="select select-bordered select-sm w-full">
          <option value="-1">{{ Lang::txt('COM_COURSES_SELECT') }}</option>
          @php
            $model = \Components\Courses\Models\Courses::getInstance();
          @endphp
          @if($model->courses()->total() > 0)
            @foreach($model->courses() as $course)
              <optgroup label="{{ $course->get('title') }}">
                @foreach($course->offerings() as $offering)
                  <optgroup label="&nbsp; &nbsp; {{ $offering->get('title') }}">
                    @foreach($offering->sections() as $sec)
                      <option value="{{ $sec->get('id') }}"
                              @selected($sec->get('id') == $row->get('section_id'))>
                        &nbsp; &nbsp; {{ $sec->get('title') }}
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              </optgroup>
            @endforeach
          @endif
        </select>
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_CODE') }}"
          inputId="field-code"
          required
      >
        <input type="text"
               name="fields[code]"
               id="field-code"
               class="input input-bordered input-sm w-full"
               required
               value="{{ $row->get('code') }}" />
      </x-form-field>
    </x-form-section>

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_AVAILABILITY') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_STARTS') }}"
          inputId="field-created"
          hint="{{ Lang::txt('COM_COURSES_FIELD_STARTS_HINT') }}"
      >
        {!! Html::input('calendar', 'fields[created]', $row->get('created'), array('id' => 'field-created')) !!}
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_EXPIRES') }}"
          inputId="field-expires"
          hint="{{ Lang::txt('COM_COURSES_FIELD_EXPIRES_HINT') }}"
      >
        {!! Html::input('calendar', 'fields[expires]', $row->get('expires'), array('id' => 'field-expires')) !!}
      </x-form-field>
    </x-form-section>

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_REDEEMED') }}">
      @if($row->get('redeemed_by'))
        <table class="meta-table">
          <tbody>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_REDEEMED') }}</th>
              <td>
                {{ $row->get('redeemed') }}
                <input type="hidden"
                       name="fields[redeemed]"
                       id="field-redeemed"
                       value="{{ $row->get('redeemed') }}" />
              </td>
            </tr>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_REDEEMED_BY') }}</th>
              <td>
                {{ $row->redeemer()->get('name') }}
                ({{ $row->redeemer()->get('username') }})
                <input type="hidden"
                       name="fields[redeemed_by]"
                       id="field-redeemed_by"
                       value="{{ $row->get('redeemed_by') }}" />
              </td>
            </tr>
          </tbody>
        </table>
      @else
        <p class="text-muted-foreground text-sm">
          {{ Lang::txt('COM_COURSES_CODE_NOT_REDEEMED') }}
        </p>
      @endif
    </x-form-section>

  @slot('sidebar')
    @if($row->exists())
      <div class="bg-base-200/50 rounded-box p-4 mb-4">
        <table class="meta-table">
          <tbody>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_ID') }}</th>
              <td>{{ $row->get('id') }}</td>
            </tr>
            @if($row->get('created'))
              <tr>
                <th>{{ Lang::txt('COM_COURSES_FIELD_CREATED') }}</th>
                <td>
                  <time datetime="{{ $row->get('created') }}">
                    {{ $row->get('created') }}
                  </time>
                </td>
              </tr>
            @endif
            @if($row->get('created_by'))
              <tr>
                <th>{{ Lang::txt('COM_COURSES_FIELD_CREATOR') }}</th>
                <td>{{ User::getInstance($row->get('created_by'))->get('name') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    @endif
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="section" value="{{ $row->get('section_id') }}" />
</x-admin-edit>
