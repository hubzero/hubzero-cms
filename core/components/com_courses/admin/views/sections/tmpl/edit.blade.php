{{--
  Courses: Sections — Admin edit view (tabbed form)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $text  = $row->exists() ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
  $__view->js('jquery.fileuploader.js', 'system');
  \Hubzero\Html\Builder\Behavior::flatpickr();
  $__view->js();
  $__view->css();
  $course_id = 0;
@endphp

@php
  Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_SECTIONS') . ': ' . $text, 'courses');
  if ($canDo->get('core.edit')) {
      Toolbar::save();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('courses');
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post"
      name="adminForm"
      id="item-form"
      enctype="multipart/form-data"
      class="editform">

  {{-- Tab navigation --}}
  <div role="tablist" class="tabs tabs-bordered mb-4">
    <a role="tab" class="tab tab-active" data-tab-target="#page-details">
      {{ Lang::txt('JDETAILS') }}
    </a>
    <a role="tab" class="tab" data-tab-target="#page-managers">
      {{ Lang::txt('COM_COURSES_FIELDSET_MANAGERS') }}
    </a>
    <a role="tab" class="tab" data-tab-target="#page-datetime">
      {{ Lang::txt('COM_COURSES_FIELDSET_DATES') }}
    </a>
    <a role="tab" class="tab" data-tab-target="#page-badge">
      {{ Lang::txt('COM_COURSES_FIELDSET_REWARDS') }}
    </a>
  </div>

  {{-- Tab: Details --}}
  <div id="page-details" class="tab-panel">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div>
        <x-form-section title="{{ Lang::txt('JDETAILS') }}">
          <x-form-field
              label="{{ Lang::txt('COM_COURSES_OFFERING') }}"
              inputId="offering_id"
          >
            @php
              $model = \Components\Courses\Models\Courses::getInstance();
            @endphp
            <select name="fields[offering_id]" id="offering_id" class="select select-bordered select-sm w-full">
              <option value="-1">{{ Lang::txt('COM_COURSES_SELECT') }}</option>
              @if($model->courses()->total() > 0)
                @foreach($model->courses() as $courseItem)
                  <optgroup label="{{ $courseItem->get('alias') }}">
                    @foreach($courseItem->offerings() as $offeringItem)
                      @php
                        if ($offeringItem->get('id') == $row->get('offering_id')) {
                            $course_id = $offeringItem->get('course_id');
                        }
                      @endphp
                      <option value="{{ $offeringItem->get('id') }}"
                              @selected($offeringItem->get('id') == $row->get('offering_id'))>
                        {{ $offeringItem->get('alias') }}
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              @endif
            </select>
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

          <fieldset class="mb-3">
            <legend class="text-sm font-medium mb-1">{{ Lang::txt('COM_COURSES_FIELD_DEFAULT_SECTION') }}</legend>
            <div class="flex gap-4">
              <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio"
                       name="fields[is_default]"
                       class="radio radio-sm"
                       value="1"
                       @checked($row->get('is_default', 0) == 1) />
                {{ Lang::txt('JYES') }}
              </label>
              <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio"
                       name="fields[is_default]"
                       class="radio radio-sm"
                       value="0"
                       @checked($row->get('is_default', 0) == 0) />
                {{ Lang::txt('JNO') }}
              </label>
            </div>
          </fieldset>

          <x-form-field
              label="{{ Lang::txt('COM_COURSES_FIELD_ENROLLMENT') }}"
              inputId="field-enrollment"
          >
            @php $enrollVal = $row->get('enrollment', $row->config('default_enrollment', 0)); @endphp
            <select name="fields[enrollment]" id="field-enrollment" class="select select-bordered select-sm w-full">
              <option value="0" @selected($enrollVal == 0)>
                {{ Lang::txt('COM_COURSES_FIELD_ENROLLMENT_OPEN') }}
              </option>
              <option value="1" @selected($enrollVal == 1)>
                {{ Lang::txt('COM_COURSES_FIELD_ENROLLMENT_RESTRICTED') }}
              </option>
              <option value="2" @selected($enrollVal == 2)>
                {{ Lang::txt('COM_COURSES_FIELD_ENROLLMENT_CLOSED') }}
              </option>
            </select>
          </x-form-field>

          <x-form-field
              label="{{ Lang::txt('COM_COURSES_FIELD_STATE') }}"
              inputId="field-state"
          >
            <select name="fields[state]" id="field-state" class="select select-bordered select-sm w-full">
              <option value="0" @selected($row->get('state') == 0)>
                {{ Lang::txt('COM_COURSES_UNPUBLISHED') }}
              </option>
              <option value="3" @selected($row->get('state') == 3)>
                {{ Lang::txt('COM_COURSES_DRAFT') }}
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

        <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PUBLISHING') }}">
          <x-form-field
              label="{{ Lang::txt('COM_COURSES_FIELD_PUBLISH_UP') }}"
              inputId="field-publish_up"
              hint="{{ Lang::txt('COM_COURSES_FIELD_PUBLISH_UP_HINT') }}"
          >
            @php
              $pubUp = $row->get('publish_up');
              $pubUpVal = ($pubUp && $pubUp != '0000-00-00 00:00:00') ? $pubUp : '';
            @endphp
            {!! Html::input('calendar', 'fields[publish_up]', $pubUpVal, array('id' => 'field-publish_up')) !!}
          </x-form-field>

          <x-form-field
              label="{{ Lang::txt('COM_COURSES_FIELD_SECTION_STARTS') }}"
              inputId="field-start_date"
              hint="{{ Lang::txt('COM_COURSES_FIELD_SECTION_STARTS_HINT') }}"
          >
            @php
              $startDate = $row->get('start_date');
              $startVal = ($startDate && $startDate != '0000-00-00 00:00:00') ? $startDate : '';
            @endphp
            {!! Html::input('calendar', 'fields[start_date]', $startVal, array('id' => 'field-start_date')) !!}
          </x-form-field>

          <x-form-field
              label="{{ Lang::txt('COM_COURSES_FIELD_FINISHES') }}"
              inputId="field-end_date"
              hint="{{ Lang::txt('COM_COURSES_FIELD_FINISHES_HINT') }}"
          >
            @php
              $endDate = $row->get('end_date');
              $endVal = ($endDate && $endDate != '0000-00-00 00:00:00') ? $endDate : '';
            @endphp
            {!! Html::input('calendar', 'fields[end_date]', $endVal, array('id' => 'field-end_date')) !!}
          </x-form-field>

          <x-form-field
              label="{{ Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN') }}"
              inputId="field-publish_down"
              hint="{{ Lang::txt('COM_COURSES_FIELD_PUBLISH_DOWN_HINT') }}"
          >
            @php
              $pubDown = $row->get('publish_down');
              $pubDownVal = ($pubDown && $pubDown != '0000-00-00 00:00:00') ? $pubDown : '';
            @endphp
            {!! Html::input('calendar', 'fields[publish_down]', $pubDownVal, array('id' => 'field-publish_down')) !!}
          </x-form-field>
        </x-form-section>
      </div>

      <div>
        <div class="bg-base-200/50 rounded-box p-4 mb-4">
          <table class="meta-table">
            <tbody>
              <tr>
                <th>{{ Lang::txt('COM_COURSES_FIELD_COURSE_ID') }}</th>
                <td>{{ $course_id }}</td>
              </tr>
              <tr>
                <th>{{ Lang::txt('COM_COURSES_FIELD_OFFERING_ID') }}</th>
                <td>{{ $row->get('offering_id') }}</td>
              </tr>
              <tr>
                <th>{{ Lang::txt('COM_COURSES_FIELD_SECTION_ID') }}</th>
                <td>{{ $row->get('id') }}</td>
              </tr>
              @if($row->get('created'))
                <tr>
                  <th>{{ Lang::txt('COM_COURSES_FIELD_CREATED') }}</th>
                  <td>{{ $row->get('created') }}</td>
                </tr>
                @if($row->get('created_by'))
                  <tr>
                    <th>{{ Lang::txt('COM_COURSES_FIELD_CREATOR') }}</th>
                    <td>{{ User::getInstance($row->get('created_by'))->get('name') }}</td>
                  </tr>
                @endif
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
                  . '&controller=logo&task=upload&type=section&id=' . $row->get('id')
                  . '&no_html=1&' . Session::getFormToken() . '=1', false
              );
              $width = 0; $height = 0; $fsize = 0;
              $pic = 'blank.png';
              $path = '/core/components/com_courses/admin/assets/img';
              if ($logo) {
                  $pathl = $row->logo('path');
                  if (file_exists(PATH_APP . $pathl . DS . $logo)) {
                      $fsize = filesize(PATH_APP . $pathl . DS . $logo);
                      list($width, $height) = getimagesize(PATH_APP . $pathl . DS . $logo);
                      $pic = $logo;
                      $path = substr(PATH_APP, strlen(PATH_ROOT)) . $pathl;
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
                  . '&type=section&id=' . $row->get('id')
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
                          src="{{ Route::url('index.php?option=' . $option . '&controller=logo&tmpl=component&file=' . $logo . '&type=section&id=' . $row->get('id'), false) }}"></iframe>
                </noscript>
              </div>
            </div>
            <div id="img-container" class="mt-2">
              <img id="img-display" src="{{ $imgSrc }}" alt="{{ Lang::txt('COM_COURSES_LOGO') }}" class="max-w-full rounded" />
              <input type="hidden" name="currentfile" id="currentfile" value="{{ $logo }}" />
            </div>
            <table class="meta-table mt-2">
              <tbody>
                <tr>
                  <th>{{ Lang::txt('COM_COURSES_FILE') }}</th>
                  <td><span id="img-name">{{ $row->params('logo', Lang::txt('COM_COURSES_NONE')) }}</span></td>
                  <td>
                    <a id="img-delete"
                       class="{{ $logo ? '' : 'hidden' }} btn btn-xs btn-error btn-outline"
                       href="{{ $removeUrl }}"
                       data-defaultimg="{{ $defaultImg }}">&times;</a>
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
              {{ Lang::txt('COM_COURSES_UPLOAD_ADDED_LATER') }}
            </div>
          @endif
        </x-form-section>

        @php $params = new \Hubzero\Config\Registry($row->get('params')); @endphp

        <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PARAMS') }}">
          <x-form-field
              label="{{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION') }}"
              inputId="params-progress-calculation"
          >
            @php $pc = $params->get('progress_calculation', ''); @endphp
            <select name="params[progress_calculation]" id="params-progress-calculation" class="select select-bordered select-sm w-full">
              <option value="" @selected($pc == '')>{{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_INHERIT_FROM_OFFERING') }}</option>
              <option value="all" @selected($pc == 'all')>{{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_ALL') }}</option>
              <option value="graded" @selected($pc == 'graded')>{{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_GRADED') }}</option>
              <option value="videos" @selected($pc == 'videos')>{{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_VIDEOS') }}</option>
              <option value="manual" @selected($pc == 'manual')>{{ Lang::txt('COM_COURSES_PROGRESS_CALCULATION_MANUAL') }}</option>
            </select>
          </x-form-field>

          <x-form-field
              label="{{ Lang::txt('COM_COURSES_PREVIEW_MODE') }}"
              inputId="params-preview"
          >
            @php $pv = $params->get('preview', ''); @endphp
            <select name="params[preview]" id="params-preview" class="select select-bordered select-sm w-full">
              <option value="0" @selected($pv == '0')>{{ Lang::txt('COM_COURSES_PREVIEW_NO') }}</option>
              <option value="1" @selected($pv == '1')>{{ Lang::txt('COM_COURSES_PREVIEW_YES_FULL') }}</option>
              <option value="2" @selected($pv == '2')>{{ Lang::txt('COM_COURSES_PREVIEW_YES_FIRST_UNIT') }}</option>
            </select>
          </x-form-field>
        </x-form-section>

        @php $plugins = Event::trigger('courses.onSectionEdit'); @endphp
        @if($plugins)
          @foreach($plugins as $plugin)
            @php
              $data = $row->get('params');
              $param = new \Hubzero\Html\Parameter(
                  (is_object($data) ? $data->toString() : $data),
                  PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
              );
              $out = $param->render('params', 'onSectionEdit');
            @endphp
            @if($out)
              <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']) }}">
                {!! $out !!}
              </x-form-section>
            @endif
          @endforeach
        @endif
      </div>
    </div>
  </div>

  {{-- Tab: Managers --}}
  <div id="page-managers" class="tab-panel hidden">
    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_MANAGERS') }}">
      @if($row->get('id'))
        @php
          $mgrUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=supervisors&tmpl=component&offering=' . $row->get('offering_id')
              . '&section=' . $row->get('id'), false
          );
        @endphp
        <iframe height="500"
                name="managers"
                id="managers"
                title="{{ Lang::txt('COM_COURSES_FIELDSET_MANAGERS') }}"
                class="w-full border border-base-300 rounded-box"
                src="{{ $mgrUrl }}"></iframe>
      @else
        <div class="alert alert-warning">
          {{ Lang::txt('COM_COURSES_FIELDSET_MANAGERS_WARNING') }}
        </div>
      @endif
    </x-form-section>
  </div>

  {{-- Tab: Dates --}}
  <div id="page-datetime" class="tab-panel hidden">
    @if($offering->units()->total() > 0)
      @if(!$row->exists() && !$row->get('is_default'))
        <div class="alert alert-info mb-4">
          {{ Lang::txt('COM_COURSES_SECTION_DATES_HELP') }}
        </div>
      @endif

      @php
        $nullDate = '0000-00-00 00:00:00';
        $offering->section($row->get('alias', '!!default!!'));
        $i = 0;
      @endphp

      @foreach($offering->units(array(), true) as $unit)
        <details class="collapse collapse-arrow bg-base-100 border border-base-300 mb-2" @if($i === 0) open @endif>
          <summary class="collapse-title font-medium">
            {{ $unit->get('title') }}
          </summary>
          <div class="collapse-content">
            @php
              $unitDateId = $row->date('unit', $unit->get('id'))->get('id');
              $unitId = $unit->get('id');
            @endphp
            <input type="hidden" name="dates[{{ $i }}][id]" value="{{ $unitDateId }}" />
            <input type="hidden" name="dates[{{ $i }}][scope]" value="unit" />
            <input type="hidden" name="dates[{{ $i }}][scope_id]" value="{{ $unitId }}" />

            <table class="admin-table text-sm">
              <tbody>
                <tr>
                  <th class="font-medium">{{ $unit->get('title') }}</th>
                  <td>
                    <label class="text-xs">{{ Lang::txt('COM_COURSES_FROM') }}</label>
                    @php
                      $unitPubUp = $unit->get('publish_up');
                      $unitDatePubUp = $row->date('unit', $unitId)->get('publish_up');
                      $tm = ($unitPubUp && $unitPubUp != $nullDate) ? $unitPubUp : $unitDatePubUp;
                      $tmVal = (!$tm || $tm == $nullDate) ? '' : Date::of($tm)->toLocal('Y-m-d H:i:s');
                    @endphp
                    <input type="text"
                           name="dates[{{ $i }}][publish_up]"
                           id="dates_{{ $i }}_publish_up"
                           class="input input-bordered input-xs w-44 datetime-field"
                           value="{{ $tmVal }}" />
                  </td>
                  <td>
                    <label class="text-xs">{{ Lang::txt('COM_COURSES_TO') }}</label>
                    @php
                      $unitPubDown = $unit->get('publish_down');
                      $unitDatePubDown = $row->date('unit', $unitId)->get('publish_down');
                      $tm = ($unitPubDown && $unitPubDown != $nullDate) ? $unitPubDown : $unitDatePubDown;
                      $tmVal = (!$tm || $tm == $nullDate) ? '' : Date::of($tm)->toLocal('Y-m-d H:i:s');
                    @endphp
                    <input type="text"
                           name="dates[{{ $i }}][publish_down]"
                           id="dates_{{ $i }}_publish_down"
                           class="input input-bordered input-xs w-44 datetime-field"
                           value="{{ $tmVal }}" />
                  </td>
                  <td class="text-xs text-muted-foreground">
                    {{ Lang::txt('COM_COURSES_SECTION_DATES_INHERITED') }}
                  </td>
                </tr>

                {{-- Asset groups --}}
                @php $z = 0; @endphp
                @foreach($unit->assetgroups() as $agt)
                  @php
                    $agtId = $agt->get('id');
                    $agt->set('publish_up', $row->date('asset_group', $agtId)->get('publish_up'));
                    $agt->set('publish_down', $row->date('asset_group', $agtId)->get('publish_down'));
                    if ($agt->get('publish_up') == $nullDate) $agt->set('publish_up', $unit->get('publish_up'));
                    if ($agt->get('publish_down') == $nullDate) $agt->set('publish_down', $unit->get('publish_down'));
                    $namePrefix = 'dates[' . $i . '][asset_group][' . $z . ']';
                    $agtDateId = $row->date('asset_group', $agtId)->get('id');
                  @endphp
                  <tr>
                    <th class="pl-6 font-normal">
                      <span class="text-muted-foreground">&#8970;</span>
                      {{ $agt->get('title') }}
                    </th>
                    <td>
                      <input type="hidden" name="{{ $namePrefix }}[id]" value="{{ $agtDateId }}" />
                      <input type="hidden" name="{{ $namePrefix }}[scope]" value="asset_group" />
                      <input type="hidden" name="{{ $namePrefix }}[scope_id]" value="{{ $agtId }}" />
                      @php
                        $agtUp = $agt->get('publish_up');
                        $unitUp = $unit->get('publish_up');
                        $val = (!$agtUp || $agtUp == $unitUp || $agtUp == $nullDate)
                            ? '' : Date::of($agtUp)->toLocal('Y-m-d H:i:s');
                      @endphp
                      <input type="text"
                             name="{{ $namePrefix }}[publish_up]"
                             class="input input-bordered input-xs w-44 datetime-field"
                             value="{{ $val }}" />
                    </td>
                    <td>
                      @php
                        $agtDown = $agt->get('publish_down');
                        $unitDown = $unit->get('publish_down');
                        $val = (!$agtDown || $agtDown == $unitDown || $agtDown == $nullDate)
                            ? '' : Date::of($agtDown)->toLocal('Y-m-d H:i:s');
                      @endphp
                      <input type="text"
                             name="{{ $namePrefix }}[publish_down]"
                             class="input input-bordered input-xs w-44 datetime-field"
                             value="{{ $val }}" />
                    </td>
                    <td></td>
                  </tr>

                  {{-- Child asset groups --}}
                  @php $j = 0; @endphp
                  @foreach($agt->children() as $ag)
                    @php
                      $agId = $ag->get('id');
                      $ag->set('publish_up', $row->date('asset_group', $agId)->get('publish_up'));
                      $ag->set('publish_down', $row->date('asset_group', $agId)->get('publish_down'));
                      if ($ag->get('publish_up') == $nullDate) $ag->set('publish_up', $agt->get('publish_up'));
                      if ($ag->get('publish_down') == $nullDate) $ag->set('publish_down', $agt->get('publish_down'));
                      $agNamePrefix = $namePrefix . '[asset_group][' . $j . ']';
                      $agDateId = $row->date('asset_group', $agId)->get('id');
                    @endphp
                    <tr>
                      <th class="pl-12 font-normal text-sm">
                        <span class="text-muted-foreground">&#8970;</span>
                        {{ $ag->get('title') }}
                      </th>
                      <td>
                        <input type="hidden" name="{{ $agNamePrefix }}[id]" value="{{ $agDateId }}" />
                        <input type="hidden" name="{{ $agNamePrefix }}[scope]" value="asset_group" />
                        <input type="hidden" name="{{ $agNamePrefix }}[scope_id]" value="{{ $agId }}" />
                        @php
                          $agUp = $ag->get('publish_up');
                          $val = (!$agUp || $agUp == $agt->get('publish_up') || $agUp == $nullDate)
                              ? '' : Date::of($agUp)->toLocal('Y-m-d H:i:s');
                        @endphp
                        <input type="text"
                               name="{{ $agNamePrefix }}[publish_up]"
                               class="input input-bordered input-xs w-44 datetime-field"
                               value="{{ $val }}" />
                      </td>
                      <td>
                        @php
                          $agDown = $ag->get('publish_down');
                          $val = (!$agDown || $agDown == $agt->get('publish_down') || $agDown == $nullDate)
                              ? '' : Date::of($agDown)->toLocal('Y-m-d H:i:s');
                        @endphp
                        <input type="text"
                               name="{{ $agNamePrefix }}[publish_down]"
                               class="input input-bordered input-xs w-44 datetime-field"
                               value="{{ $val }}" />
                      </td>
                      <td></td>
                    </tr>

                    {{-- Assets of child asset group --}}
                    @if($ag->assets()->total())
                      @php $k = 0; @endphp
                      @foreach($ag->assets() as $a)
                        @php
                          $aId = $a->get('id');
                          $a->set('publish_up', $row->date('asset', $aId)->get('publish_up'));
                          $a->set('publish_down', $row->date('asset', $aId)->get('publish_down'));
                          if ($a->get('publish_up') == $nullDate) $a->set('publish_up', $ag->get('publish_up'));
                          if ($a->get('publish_down') == $nullDate) $a->set('publish_down', $ag->get('publish_down'));
                          $aNamePrefix = $agNamePrefix . '[asset][' . $k . ']';
                          $aDateId = $row->date('asset', $aId)->get('id');
                        @endphp
                        <tr>
                          <th class="pl-20 font-normal text-xs">
                            <span class="text-muted-foreground">&#8970;</span>
                            {{ $a->get('title') }}
                          </th>
                          <td>
                            <input type="hidden" name="{{ $aNamePrefix }}[id]" value="{{ $aDateId }}" />
                            <input type="hidden" name="{{ $aNamePrefix }}[scope]" value="asset" />
                            <input type="hidden" name="{{ $aNamePrefix }}[scope_id]" value="{{ $aId }}" />
                            @php
                              $aUp = $a->get('publish_up');
                              $val = (!$aUp || $aUp == $ag->get('publish_up') || $aUp == $nullDate)
                                  ? '' : Date::of($aUp)->toLocal('Y-m-d H:i:s');
                            @endphp
                            <input type="text"
                                   name="{{ $aNamePrefix }}[publish_up]"
                                   class="input input-bordered input-xs w-44 datetime-field"
                                   value="{{ $val }}" />
                          </td>
                          <td>
                            @php
                              $aDown = $a->get('publish_down');
                              $val = (!$aDown || $aDown == $ag->get('publish_down') || $aDown == $nullDate)
                                  ? '' : Date::of($aDown)->toLocal('Y-m-d H:i:s');
                            @endphp
                            <input type="text"
                                   name="{{ $aNamePrefix }}[publish_down]"
                                   class="input input-bordered input-xs w-44 datetime-field"
                                   value="{{ $val }}" />
                          </td>
                          <td></td>
                        </tr>
                        @php $k++; @endphp
                      @endforeach
                    @endif
                    @php $j++; @endphp
                  @endforeach

                  {{-- Direct assets of asset group --}}
                  @if($agt->assets()->total())
                    @php $k = 0; @endphp
                    @foreach($agt->assets() as $a)
                      @php
                        $aId = $a->get('id');
                        $a->set('publish_up', $row->date('asset', $aId)->get('publish_up'));
                        $a->set('publish_down', $row->date('asset', $aId)->get('publish_down'));
                        if ($a->get('publish_up') == $nullDate) $a->set('publish_up', $agt->get('publish_up'));
                        if ($a->get('publish_down') == $nullDate) $a->set('publish_down', $agt->get('publish_down'));
                        $aNamePrefix = $namePrefix . '[asset][' . $k . ']';
                        $aDateId = $row->date('asset', $aId)->get('id');
                      @endphp
                      <tr>
                        <th class="pl-12 font-normal text-xs">
                          <span class="text-muted-foreground">&#8970;</span>
                          {{ $a->get('title') }}
                        </th>
                        <td>
                          <input type="hidden" name="{{ $aNamePrefix }}[id]" value="{{ $aDateId }}" />
                          <input type="hidden" name="{{ $aNamePrefix }}[scope]" value="asset" />
                          <input type="hidden" name="{{ $aNamePrefix }}[scope_id]" value="{{ $aId }}" />
                          @php
                            $aUp = $a->get('publish_up');
                            $val = (!$aUp || $aUp == $agt->get('publish_up') || $aUp == $nullDate)
                                ? '' : Date::of($aUp)->toLocal('Y-m-d H:i:s');
                          @endphp
                          <input type="text"
                                 name="{{ $aNamePrefix }}[publish_up]"
                                 class="input input-bordered input-xs w-44 datetime-field"
                                 value="{{ $val }}" />
                        </td>
                        <td>
                          @php
                            $aDown = $a->get('publish_down');
                            $val = (!$aDown || $aDown == $agt->get('publish_down') || $aDown == $nullDate)
                                ? '' : Date::of($aDown)->toLocal('Y-m-d H:i:s');
                          @endphp
                          <input type="text"
                                 name="{{ $aNamePrefix }}[publish_down]"
                                 class="input input-bordered input-xs w-44 datetime-field"
                                 value="{{ $val }}" />
                        </td>
                        <td></td>
                      </tr>
                      @php $k++; @endphp
                    @endforeach
                  @endif
                  @php $z++; @endphp
                @endforeach

                {{-- Direct assets of unit --}}
                @if($unit->assets()->total())
                  @php $k = 0; @endphp
                  @foreach($unit->assets() as $a)
                    @php
                      $aId = $a->get('id');
                      $a->set('publish_up', $row->date('asset', $aId)->get('publish_up'));
                      $a->set('publish_down', $row->date('asset', $aId)->get('publish_down'));
                      if ($a->get('publish_up') == $nullDate) $a->set('publish_up', $unit->get('publish_up'));
                      if ($a->get('publish_down') == $nullDate) $a->set('publish_down', $unit->get('publish_down'));
                      $aNamePrefix = 'dates[' . $i . '][asset][' . $k . ']';
                      $aDateId = $row->date('asset', $aId)->get('id');
                    @endphp
                    <tr>
                      <th class="pl-6 font-normal text-xs">
                        <span class="text-muted-foreground">&#8970;</span>
                        {{ $a->get('title') }}
                      </th>
                      <td>
                        <input type="hidden" name="{{ $aNamePrefix }}[id]" value="{{ $aDateId }}" />
                        <input type="hidden" name="{{ $aNamePrefix }}[scope]" value="asset" />
                        <input type="hidden" name="{{ $aNamePrefix }}[scope_id]" value="{{ $aId }}" />
                        @php
                          $aUp = $a->get('publish_up');
                          $val = (!$aUp || $aUp == $unit->get('publish_up') || $aUp == $nullDate)
                              ? '' : Date::of($aUp)->toLocal('Y-m-d H:i:s');
                        @endphp
                        <input type="text"
                               name="{{ $aNamePrefix }}[publish_up]"
                               class="input input-bordered input-xs w-44 datetime-field"
                               value="{{ $val }}" />
                      </td>
                      <td>
                        @php
                          $aDown = $a->get('publish_down');
                          $val = (!$aDown || $aDown == $unit->get('publish_down') || $aDown == $nullDate)
                              ? '' : Date::of($aDown)->toLocal('Y-m-d H:i:s');
                        @endphp
                        <input type="text"
                               name="{{ $aNamePrefix }}[publish_down]"
                               class="input input-bordered input-xs w-44 datetime-field"
                               value="{{ $val }}" />
                      </td>
                      <td></td>
                    </tr>
                    @php $k++; @endphp
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </details>
        @php $i++; @endphp
      @endforeach
    @else
      <div class="alert alert-warning">
        {{ Lang::txt('COM_COURSES_NO_DATES_FOUND') }}
      </div>
    @endif
  </div>

  {{-- Tab: Rewards/Badge --}}
  <div id="page-badge" class="tab-panel hidden">
    @php $certificate = $course->certificate(); @endphp
    @if($certificate->exists() && $certificate->hasFile())
      <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_CERTIFICATE') }}">
        <x-form-field
            label="{{ Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE') }}"
            inputId="params-certificate"
            hint="{{ Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_EXPLANATION') }}"
        >
          @php $cert = $params->get('certificate', 0); @endphp
          <select name="params[certificate]" id="params-certificate" class="select select-bordered select-sm w-full">
            <option value="0" @selected($cert == 0)>{{ Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_NO') }}</option>
            <option value="1" @selected($cert == 1)>{{ Lang::txt('COM_COURSES_CERTIFICATE_AVAILABLE_YES') }}</option>
          </select>
        </x-form-field>
      </x-form-section>
    @else
      <input type="hidden" name="params[certificate]" value="0" />
    @endif

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_BADGE') }}">
      <input type="hidden" name="badge[id]" value="{{ $badge->get('id') }}" />

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_BADGE_ENABLED') }}"
          inputId="badge-published"
      >
        <input type="checkbox"
               name="badge[published]"
               id="badge-published"
               class="checkbox checkbox-sm"
               value="1"
               @checked($badge->get('published')) />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_BADGE_IMAGE') }}"
          inputId="badge-image"
      >
        @if($badge->get('img_url'))
          <img src="{{ $badge->get('img_url') }}" width="125" class="mb-2 rounded" />
        @endif
        <input type="file"
               name="badge_image"
               id="badge-image"
               class="file-input file-input-bordered file-input-sm w-full" />
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_BADGE_PROVIDER') }}"
          inputId="badge-provider"
      >
        <select name="badge[provider_name]" id="badge-provider" class="select select-bordered select-sm w-full">
          <option value="passport" @selected($badge->get('provider_name', 'passport') == 'passport')>
            Passport
          </option>
        </select>
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_BADGE_CRITERIA') }}"
          inputId="badge-criteria"
      >
        {!! $__view->editor(
            'badge[criteria]',
            e($badge->get('criteria_text')),
            50,
            10,
            'badge-criteria'
        ) !!}
      </x-form-field>
    </x-form-section>
  </div>

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="offering" value="{{ $row->get('offering_id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />
  {!! Html::input('token') !!}
</form>
