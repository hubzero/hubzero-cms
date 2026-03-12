{{--
  Courses: Asset Groups — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ASSET_GROUPS') . ': ' . $text,
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
          label="{{ Lang::txt('COM_COURSES_FIELD_PARENT') }}"
          inputId="field-parent"
          hint="{{ Lang::txt('COM_COURSES_FIELD_PARENT_HINT') }}"
      >
        <select name="fields[parent]" id="field-parent" class="select select-bordered select-sm w-full">
          <option value="0" @selected(0 == $row->get('parent'))>
            {{ Lang::txt('COM_COURSES_NONE') }}
          </option>
          @foreach($assetgroups as $assetgroup)
            <option value="{{ $assetgroup->get('id') }}"
                    @selected($assetgroup->get('id') == $row->get('parent'))>
              {!! $assetgroup->treename !!}{{ $assetgroup->get('title') }}
            </option>
          @endforeach
        </select>
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_TITLE') }}"
          inputId="field-title"
          required
      >
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered input-sm w-full"
               required
               value="{{ $row->get('title') }}" />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_ALIAS') }}"
          inputId="field-alias"
          hint="{{ Lang::txt('COM_COURSES_FIELD_ALIAS_HINT') }}"
      >
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered input-sm w-full"
               value="{{ $row->get('alias') }}" />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_DESCRIPTION') }}"
          inputId="field-description"
      >
        <input type="text"
               name="fields[description]"
               id="field-description"
               class="input input-bordered input-sm w-full"
               value="{{ $row->get('description') }}" />
      </x-form-field>
    </x-form-section>

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_ASSETS') }}">
      @if($row->get('id'))
        @php
          $assetsUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=assets&tmpl=component&scope=asset_group'
              . '&scope_id=' . $row->get('id')
              . '&course_id=' . $offering->get('course_id'), false
          );
        @endphp
        <iframe height="400"
                name="assets"
                id="assets"
                title="{{ Lang::txt('COM_COURSES_ASSETS') }}"
                class="w-full border border-base-300 rounded-box"
                src="{{ $assetsUrl }}"></iframe>
      @else
        <div class="alert alert-info">
          {{ Lang::txt('COM_COURSES_ENTRY_MUST_BE_SAVED_BEFORE_ASSETS') }}
        </div>
      @endif
    </x-form-section>

  @slot('sidebar')
    <div class="bg-base-200/50 rounded-box p-4 mb-4">
      <table class="meta-table">
        <tbody>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_UNIT_ID') }}</th>
            <td>{{ $row->get('unit_id') }}</td>
          </tr>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_ID') }}</th>
            <td>{{ $row->get('id') }}</td>
          </tr>
          @if($row->get('created'))
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FIELD_CREATED') }}</th>
              <td>
                <time datetime="{{ $row->get('created') }}">
                  {{ Date::of($row->get('created'))->toLocal() }}
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

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PUBLISHING') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_STATE') }}"
          inputId="field-state"
      >
        <select name="fields[state]" id="field-state" class="select select-bordered select-sm w-full">
          <option value="0" @selected($row->get('state') == 0)>
            {{ Lang::txt('COM_COURSES_UNPUBLISHED') }}
          </option>
          <option value="1" @selected($row->get('state') == 1)>
            {{ Lang::txt('COM_COURSES_PUBLISHED') }}
          </option>
          <option value="2" @selected($row->get('state') == 2)>
            {{ Lang::txt('COM_COURSES_TRASHED') }}
          </option>
        </select>
      </x-form-field>
    </x-form-section>

    @php
      $plugins = Event::trigger('courses.onAssetgroupEdit');
    @endphp
    @if($plugins)
      @php $data = $row->get('params'); @endphp
      @foreach($plugins as $plugin)
        @php
          $default = Plugin::params('courses', $plugin['name']);
          $param = new \Hubzero\Html\Parameter(
              (is_object($data) ? $data->toString() : $data),
              PATH_CORE . DS . 'plugins' . DS . 'courses' . DS
              . $plugin['name'] . DS . $plugin['name'] . '.xml'
          );
          foreach ($default->toArray() as $k => $v) {
              if (substr($k, 0, strlen('default_')) == 'default_') {
                  $param->def(substr($k, strlen('default_')), $default->get($k, $v));
              }
          }
          $out = $param->render('params', 'onAssetgroupEdit');
        @endphp
        @if($out)
          <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']) }}">
            {!! $out !!}
          </x-form-section>
        @endif
      @endforeach
    @endif
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[unit_id]" value="{{ $row->get('unit_id') }}" />
  <input type="hidden" name="unit" value="{{ $row->get('unit_id') }}" />
</x-admin-edit>
