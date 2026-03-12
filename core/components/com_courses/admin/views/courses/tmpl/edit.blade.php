{{--
  Courses — Admin edit view

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
    title="{{ Lang::txt('COM_COURSES') . ': ' . $text }}"
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
          label="{{ Lang::txt('COM_COURSES_FIELD_GROUP') }}"
          inputId="field-group_id"
      >
        @php
          $groupFilters = array(
              'authorized' => 'admin',
              'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
              'type'       => array(1, 3),
              'sortby'     => 'description'
          );
          $groups = \Hubzero\User\Group::find($groupFilters);
        @endphp
        <select name="fields[group_id]" id="field-group_id" class="select select-bordered select-sm w-full">
          <option value="0" @selected(!$row->get('group_id'))>
            {{ Lang::txt('COM_COURSES_NONE') }}
          </option>
          @if($groups)
            @foreach($groups as $group)
              <option value="{{ $group->gidNumber }}" @selected($group->gidNumber == $row->get('group_id'))>
                {{ $group->description }} ({{ $group->cn }})
              </option>
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

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_BLURB') }}"
          inputId="field-blurb"
          hint="{{ Lang::txt('COM_COURSES_FIELD_BLURB_HINT') }}"
      >
        <textarea name="fields[blurb]"
                  id="field-blurb"
                  class="textarea textarea-bordered w-full"
                  rows="3">{{ $row->get('blurb') }}</textarea>
      </x-form-field>

      <div class="grid grid-cols-2 gap-4">
        <x-form-field
            label="{{ Lang::txt('COM_COURSES_FIELD_LENGTH') }}"
            inputId="field-length"
            hint="{{ Lang::txt('COM_COURSES_FIELD_LENGTH_HINT') }}"
        >
          <input type="text"
                 name="fields[length]"
                 id="field-length"
                 class="input input-bordered input-sm w-full"
                 value="{{ $row->get('length') }}" />
        </x-form-field>

        <x-form-field
            label="{{ Lang::txt('COM_COURSES_FIELD_EFFORT') }}"
            inputId="field-effort"
            hint="{{ Lang::txt('COM_COURSES_FIELD_EFFORT_HINT') }}"
        >
          <input type="text"
                 name="fields[effort]"
                 id="field-effort"
                 class="input input-bordered input-sm w-full"
                 value="{{ $row->get('effort') }}" />
        </x-form-field>
      </div>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_DESCRIPTION') }}"
          inputId="field-description"
          hint="{{ Lang::txt('COM_COURSES_FIELD_DESCRIPTION_HINT') }}"
      >
        {!! $__view->editor(
            'fields[description]',
            e($row->description('raw')),
            40,
            15,
            'field-description'
        ) !!}
      </x-form-field>

      <x-form-field
          label="{{ Lang::txt('COM_COURSES_FIELD_TAGS') }}"
          inputId="field-tags"
          hint="{{ Lang::txt('COM_COURSES_FIELD_TAGS_HINT') }}"
      >
        <textarea name="tags"
                  id="field-tags"
                  class="textarea textarea-bordered w-full"
                  rows="3">{{ $row->tags('string') }}</textarea>
      </x-form-field>
    </x-form-section>

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_MANAGERS') }}">
      @if($row->exists())
        @php
          $managersUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=managers&tmpl=component&id=' . $row->get('id'), false
          );
        @endphp
        <iframe height="400"
                name="managers"
                id="managers"
                title="{{ Lang::txt('COM_COURSES_FIELDSET_MANAGERS') }}"
                class="w-full border border-base-300 rounded-box"
                src="{{ $managersUrl }}"></iframe>
      @else
        <div class="alert alert-warning">
          {{ Lang::txt('COM_COURSES_FIELDSET_MANAGERS_WARNING') }}
        </div>
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
    @endif

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
          <option value="3" @selected($row->get('state') == 3)>
            {{ Lang::txt('COM_COURSES_DRAFT') }}
          </option>
          <option value="2" @selected($row->get('state') == 2)>
            {{ Lang::txt('COM_COURSES_TRASHED') }}
          </option>
        </select>
      </x-form-field>
    </x-form-section>

    @php
      $plugins = Event::trigger('courses.onCourseEdit');
    @endphp
    @if($plugins)
      @foreach($plugins as $plugin)
        @php
          $data = $row->get('params');
          $param = new \Hubzero\Html\Parameter(
              (is_object($data) ? $data->toString() : $data),
              PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
          );
          $out = $param->render('params', 'onCourseEdit');
        @endphp
        @if($out)
          <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']) }}">
            {!! $out !!}
          </x-form-section>
        @endif
      @endforeach
    @endif

    <x-form-section title="{{ Lang::txt('COM_COURSES_FIELDSET_IMAGE') }}">
      @if($row->exists())
        @php
          $logo = $row->get('logo');
          $pics = explode(DS, $logo);
          $file = end($pics);

          $width  = 0;
          $height = 0;
          $fsize  = 0;
          $pic    = 'blank.png';
          $path   = '/core/components/com_courses/admin/assets/img';

          if ($logo) {
              $pathl = substr(PATH_APP, strlen(PATH_ROOT)) . DS
                  . trim($config->get('uploadpath', '/site/courses'), DS) . DS . $row->get('id');
              if (file_exists(PATH_ROOT . $pathl . DS . $logo)) {
                  $fsize = filesize(PATH_ROOT . $pathl . DS . $file);
                  list($width, $height) = getimagesize(PATH_ROOT . $pathl . DS . $file);
                  $pic  = $file;
                  $path = $pathl;
              } else {
                  $logo = null;
              }
          }

          $uploadUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=logo&task=upload&type=course&id=' . $row->get('id')
              . '&no_html=1&' . Session::getFormToken() . '=1', false
          );
          $removeUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=logo&tmpl=component&task=remove&currentfile=' . $logo
              . '&type=course&id=' . $row->get('id')
              . '&' . Session::getFormToken() . '=1', false
          );
          $baseRoot = rtrim(Request::root(true), '/');
          $imgSrc = $baseRoot . $path . '/' . $pic;
          $defaultImg = $baseRoot . '/core/components/com_courses/admin/assets/img/blank.png';
        @endphp
        <div class="uploader-wrap">
          <div id="ajax-uploader"
               data-action="{{ $uploadUrl }}"
               data-instructions="{{ Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP') }}">
            <noscript>
              @php
                $filerUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=logo&tmpl=component&file=' . $file
                    . '&type=course&id=' . $row->get('id'), false
                );
              @endphp
              <iframe height="350" name="filer" id="filer"
                      title="File upload"
                      src="{{ $filerUrl }}"></iframe>
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
                <span id="img-name">{{ $row->get('logo') ?: Lang::txt('COM_COURSES_NONE') }}</span>
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
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
</x-admin-edit>
