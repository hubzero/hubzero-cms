{{--
  Resource Edit — Non-tool resource fields partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<div class="admin-field">
  <label for="field-title" class="label">
    {{ Lang::txt('COM_RESOURCES_FIELD_TITLE') }}:
    <span class="text-error text-xs">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
  </label>
  <input type="text"
         name="fields[title]"
         id="field-title"
         class="input input-bordered w-full"
         maxlength="250"
         required
         value="{{ $row->title }}" />
</div>

<div class="admin-field">
  <label for="type" class="label">
    {{ Lang::txt('COM_RESOURCES_FIELD_TYPE') }}:
    <span class="text-error text-xs">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
  </label>
  {!! $lists['type'] !!}
</div>

@if($row->standalone == 1)
  <div class="admin-field">
    <label for="field-alias" class="label">
      {{ Lang::txt('COM_RESOURCES_FIELD_ALIAS') }}
    </label>
    <input type="text"
           name="fields[alias]"
           id="field-alias"
           class="input input-bordered w-full"
           maxlength="250"
           value="{{ $row->alias }}" />
  </div>

  <div class="admin-field">
    <label for="field-license" class="label">
      {{ Lang::txt('COM_RESOURCES_FIELD_LICENSE') }}
    </label>
    @php $currentLicense = $row->get('license', $row->params->get('license')); @endphp
    <select name="fields[license]" id="field-license" class="select select-bordered w-full">
      <option value="" @selected($currentLicense == '')>
        {{ Lang::txt('COM_RESOURCES_NONE') }}
      </option>
      @foreach($licenses as $license)
        <option value="{{ $license->get('name') }}" @selected($currentLicense == $license->get('name'))>
          {{ $license->get('title') }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="admin-field">
      <label for="attrib-location" class="label">
        {{ Lang::txt('COM_RESOURCES_FIELD_LOCATION') }}
      </label>
      <input type="text"
             name="attrib[location]"
             id="attrib-location"
             class="input input-bordered w-full"
             maxlength="250"
             value="{{ $row->attribs->get('location', '') }}" />
    </div>
    <div class="admin-field">
      <label for="attrib-timeof" class="label">
        {{ Lang::txt('COM_RESOURCES_FIELD_TIME') }}
      </label>
      <input type="text"
             name="attrib[timeof]"
             id="attrib-timeof"
             class="input input-bordered w-full"
             maxlength="250"
             value="{{ $time ? Date::of($time)->toLocal('Y-m-d H:i:s') : '' }}"
             placeholder="YYYY-MM-DD hh:mm:ss" />
    </div>
  </div>

  <div class="admin-field">
    <label for="attrib-canonical" class="label">
      {{ Lang::txt('COM_RESOURCES_FIELD_CANONICAL') }}
    </label>
    <input type="text"
           name="attrib[canonical]"
           id="attrib-canonical"
           class="input input-bordered w-full"
           maxlength="250"
           value="{{ $row->attribs->get('canonical', '') }}" />
    <p class="text-xs text-muted-foreground mt-1">
      {{ Lang::txt('COM_RESOURCES_FIELD_CANONICAL_HINT') }}
    </p>
  </div>
@else
  <div class="admin-field">
    <label for="logical_type" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_LOGICAL_TYPE') }}</label>
    {!! $lists['logical_type'] !!}
    <input type="hidden" name="fields[alias]" value="" />
  </div>

  <div class="admin-field">
    <label for="field-path" class="label">
      {{ Lang::txt('COM_RESOURCES_FIELD_PATH') }}
    </label>
    <input type="text"
           name="fields[path]"
           id="field-path"
           class="input input-bordered w-full"
           maxlength="250"
           value="{{ $row->get('path') }}" />
  </div>

  <div class="admin-field">
    <label for="attrib-duration" class="label">
      {{ Lang::txt('COM_RESOURCES_FIELD_DURATION') }}
    </label>
    <input type="text"
           name="attrib[duration]"
           id="attrib-duration"
           class="input input-bordered w-full"
           maxlength="100"
           value="{{ $row->attribs->get('duration', '') }}" />
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div class="admin-field">
      <label for="attrib-width" class="label">
        {{ Lang::txt('COM_RESOURCES_FIELD_WIDTH') }}
      </label>
      <input type="text"
             name="attrib[width]"
             id="attrib-width"
             class="input input-bordered w-full"
             maxlength="250"
             value="{{ $row->attribs->get('width', '') }}" />
    </div>
    <div class="admin-field">
      <label for="attrib-height" class="label">
        {{ Lang::txt('COM_RESOURCES_FIELD_HEIGHT') }}
      </label>
      <input type="text"
             name="attrib[height]"
             id="attrib-height"
             class="input input-bordered w-full"
             maxlength="250"
             value="{{ $row->attribs->get('height', '') }}" />
    </div>
  </div>

  <div class="admin-field">
    <label for="attrib-attributes" class="label">
      {{ Lang::txt('COM_RESOURCES_FIELD_ATTRIBUTES') }}
    </label>
    <input type="text"
           name="attrib[attributes]"
           id="attrib-attributes"
           class="input input-bordered w-full"
           maxlength="100"
           value="{{ $row->attribs->get('attributes', '') }}" />
    <p class="text-xs text-muted-foreground mt-1">
      {{ Lang::txt('COM_RESOURCES_FIELD_ATTRIBUTES_HINT') }}
    </p>
  </div>
@endif

<div class="admin-field">
  <label for="field-introtext" class="label">
    {{ Lang::txt('COM_RESOURCES_FIELD_ABSTRACT_DESCRIPTION') }}:
    <span class="text-error text-xs">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
  </label>
  {!! $__view->editor(
      'fields[introtext]',
      e($row->get('introtext')),
      45,
      5,
      'field-introtext',
      ['buttons' => false]
  ) !!}
</div>

<div class="admin-field">
  <label for="field-fulltxt" class="label">
    {{ Lang::txt('COM_RESOURCES_FIELD_MAIN_TEXT') }}
  </label>
  {!! $__view->editor(
      'fields[fulltxt]',
      e($row->description),
      45,
      15,
      'field-fulltxt',
      ['buttons' => false]
  ) !!}
</div>
