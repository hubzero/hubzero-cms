{{--
  Courses: Offerings — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->exists() ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
  $__view->js('jquery.fileuploader.js', 'system');
  $__view->js();
  $__view->css();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_OFFERING') . ': ' . $text }}"
    icon="courses"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
    <x-form-section title="{{ Lang::txt('JDETAILS') }}">
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
    </x-form-section>

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

      <p class="text-sm text-muted-foreground mb-2">
        {!! Lang::txt('COM_COURSES_OFFERING_START_END_HINT') !!}
      </p>

      <div class="grid grid-cols-2 gap-4">
        <x-form-field
            label="{{ Lang::txt('COM_COURSES_FIELD_STARTS') }}"
            inputId="publish_up"
            hint="{{ Lang::txt('COM_COURSES_FIELD_STARTS_HINT') }}"
        >
          @php
            $pubUp = ($row->get('publish_up') && $row->get('publish_up') != '0000-00-00 00:00:00')
                ? $row->get('publish_up') : '';
          @endphp
          {!! Html::input('calendar', 'fields[publish_up]', $pubUp, array('id' => 'publish_up')) !!}
        </x-form-field>

        <x-form-field
            label="{{ Lang::txt('COM_COURSES_FIELD_ENDS') }}"
            inputId="publish_down"
            hint="{{ Lang::txt('COM_COURSES_FIELD_ENDS_HINT') }}"
        >
          @php
            $pubDown = ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00')
                ? $row->get('publish_down') : '';
          @endphp
          {!! Html::input('calendar', 'fields[publish_down]', $pubDown, array('id' => 'publish_down')) !!}
        </x-form-field>
      </div>
    </x-form-section>

  @slot('sidebar')
    <div class="bg-base-200/50 rounded-box p-4 mb-4">
      <table class="meta-table">
        <tbody>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_COURSE_ID') }}</th>
            <td>{{ $row->get('course_id') }}</td>
          </tr>
          <tr>
            <th>{{ Lang::txt('COM_COURSES_FIELD_OFFERING_ID') }}</th>
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

    <x-form-section title="{{ Lang::txt('COM_COURSES_LOGO') }}">
      @if($row->exists())
        @php
          $logo = $row->params('logo');
          $uploadUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=logo&task=upload&type=offering&id=' . $row->get('id')
              . '&no_html=1&' . Session::getFormToken() . '=1', false
          );

          $width  = 0;
          $height = 0;
          $fsize  = 0;
          $pic    = 'blank.png';
          $path   = '/core/components/com_courses/admin/assets/img';

          if ($logo) {
              $pathl = substr(PATH_APP, strlen(PATH_ROOT)) . $row->logo('path');
              if (file_exists(PATH_ROOT . $pathl . DS . $logo)) {
                  $fsize = filesize(PATH_ROOT . $pathl . DS . $logo);
                  list($width, $height) = getimagesize(PATH_ROOT . $pathl . DS . $logo);
                  $pic  = $logo;
                  $path = $pathl;
              } else {
                  $logo = null;
              }
          }

          $baseRoot = rtrim(Request::root(true), '/');
          $imgSrc = $baseRoot . $path . '/' . $pic;
          $defaultImg = $baseRoot . '/core/components/com_courses/admin/assets/img/blank.png';
          $removeUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=logo&tmpl=component&task=remove&currentfile=' . $logo
              . '&type=offering&id=' . $row->get('id')
              . '&' . Session::getFormToken() . '=1', false
          );
        @endphp
        <div class="uploader-wrap">
          <div id="ajax-uploader"
               data-action="{{ $uploadUrl }}"
               data-instructions="{{ Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP') }}">
            <noscript>
              <iframe height="350" name="filer" id="filer"
                      title="File upload"
                      class="w-full border border-base-300 rounded"
                      src="{{ Route::url('index.php?option=' . $option . '&controller=logo&tmpl=component&file=' . $logo . '&type=offering&id=' . $row->get('id'), false) }}"></iframe>
            </noscript>
          </div>
        </div>
        <div id="img-container" class="mt-2">
          <img id="img-display"
               src="{{ $imgSrc }}"
               alt="{{ Lang::txt('COM_COURSES_LOGO') }}"
               class="max-w-full rounded" />
          <input type="hidden" name="currentfile" id="currentfile" value="{{ $logo }}" />
        </div>
        <table class="meta-table mt-2">
          <tbody>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_FILE') }}</th>
              <td>
                <span id="img-name">{{ ($pic && $pic != 'blank.png') ? $pic : Lang::txt('COM_COURSES_NONE') }}</span>
              </td>
              <td>
                <a id="img-delete"
                   class="{{ $logo ? '' : 'hidden' }} btn btn-xs btn-error btn-outline"
                   href="{{ $removeUrl }}"
                   title="{{ Lang::txt('COM_COURSES_DELETE') }}"
                   data-defaultimg="{{ $defaultImg }}">
                  &times;
                </a>
              </td>
            </tr>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_PICTURE_SIZE') }}</th>
              <td><span id="img-size">{{ \Hubzero\Utility\Number::formatBytes($fsize) }}</span></td>
              <td></td>
            </tr>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_PICTURE_WIDTH') }}</th>
              <td><span id="img-width">{{ $width }}</span> px</td>
              <td></td>
            </tr>
            <tr>
              <th>{{ Lang::txt('COM_COURSES_PICTURE_HEIGHT') }}</th>
              <td><span id="img-height">{{ $height }}</span> px</td>
              <td></td>
            </tr>
          </tbody>
        </table>
      @else
        <div class="alert alert-warning">
          {{ Lang::txt('COM_COURSES_PICTURE_ADDED_LATER') }}
        </div>
      @endif
    </x-form-section>

    @php
      $params = new \Hubzero\Config\Registry($row->get('params'));
    @endphp
    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PARAMS') }}">
      <x-form-field
          label="{{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION') }}"
          inputId="params-progress-calculation"
      >
        @php $pc = $params->get('progress_calculation', ''); @endphp
        <select name="params[progress_calculation]" id="params-progress-calculation" class="select select-bordered select-sm w-full">
          <option value="" @selected($pc == '')>
            {{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_INHERIT') }}
          </option>
          <option value="all" @selected($pc == 'all')>
            {{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_ALL') }}
          </option>
          <option value="graded" @selected($pc == 'graded')>
            {{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_GRADED') }}
          </option>
          <option value="videos" @selected($pc == 'videos')>
            {{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_VIDEOS') }}
          </option>
          <option value="manual" @selected($pc == 'manual')>
            {{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_MANUAL') }}
          </option>
        </select>
      </x-form-field>
    </x-form-section>

    @php
      $plugins = Event::trigger('courses.onOfferingEdit');
    @endphp
    @if($plugins)
      @foreach($plugins as $plugin)
        @php
          $data = $row->get('params');
          $param = new \Hubzero\Html\Parameter(
              (is_object($data) ? $data->toString() : $data),
              PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
          );
          $out = $param->render('params', 'onOfferingEdit');
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
  <input type="hidden" name="fields[course_id]" value="{{ $row->get('course_id') }}" />
  <input type="hidden" name="course" value="{{ $row->get('course_id') }}" />
</x-admin-edit>
