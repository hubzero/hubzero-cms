{{--
  Tool Zone — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_ZONES') . ': ' . $text, 'tools');
  Toolbar::apply();
  Toolbar::save();
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('zone');

  $__view->css('tools')->js('zones.blade.js');

  $hn = Request::host();

  $wsEnabled  = $row->params->get('websocket_enable');
  $vncEnabled = $row->params->get('vnc_enable');

  $locIframeUrl = $row->get('id')
    ? Route::url('index.php?option=' . $option . '&controller=locations&tmpl=component&zone=' . $row->get('id'), false)
    : null;

  $ajaxUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=upload&id=' . $row->get('id') . '&no_html=1&' . Session::getFormToken() . '=1', false);
  $deleteImgUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&tmpl=component&task=removefile&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false);

  if ($pic = $row->get('picture')) {
      $imgPath = $row->logo('path');
      $imgSrc  = '..' . str_replace(PATH_APP, '', $imgPath) . '/' . $pic;
      $imgSize = file_exists($imgPath . '/' . $pic) ? filesize($imgPath . '/' . $pic) : 0;
      [$imgW, $imgH] = @getimagesize($imgPath . '/' . $pic) ?: [0, 0];
  } else {
      $pic     = 'blank.png';
      $imgSrc  = '/core/components/com_tools/admin/assets/img/blank.png';
      $imgSize = 0; $imgW = 0; $imgH = 0;
  }
@endphp

@if($__view->getError())
  <div class="alert alert-error mb-4">{!! implode('<br>', $__view->getErrors()) !!}</div>
@endif

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Tabs --}}
  <div class="tabs tabs-border mb-4">
    <button type="button" class="tab tab-active" data-tab="profile">{{ Lang::txt('JDETAILS') }}</button>
    <button type="button" class="tab" data-tab="locations">{{ Lang::txt('COM_TOOLS_FIELDSET_LOCATIONS') }}</button>
  </div>

  {{-- Details tab --}}
  <div data-tab-panel="profile">
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field" data-hint="{{ Lang::txt('COM_TOOLS_FIELD_ZONE_HINT') }}">
        <label for="field-zone" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_ZONE') }}</label>
        <input type="text"
               name="fields[zone]"
               id="field-zone"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->get('zone') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_TOOLS_FIELD_ZONE_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-title" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_TITLE') }}</label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->get('title') }}" />
      </div>

      <div class="admin-field">
        <label for="field-description" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_DESCRIPTION') }}</label>
        <textarea name="fields[description]"
                  id="field-description"
                  class="textarea textarea-bordered w-full"
                  rows="2">{{ $row->get('description') }}</textarea>
      </div>

      <div class="admin-field">
        <label for="field-master" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_MASTER') }}</label>
        <input type="text"
               name="fields[master]"
               id="field-master"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->get('master') }}" />
      </div>

      <div class="admin-field">
        <label for="field-type" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_TYPE') }}</label>
        <select name="fields[type]" id="field-type" class="select select-bordered w-full">
          <option value="local"  @selected($row->get('type') == 'local')>{{ Lang::txt('COM_TOOLS_FIELD_TYPE_LOCAL') }}</option>
          <option value="remote" @selected($row->get('type') == 'remote')>{{ Lang::txt('COM_TOOLS_FIELD_TYPE_REMOTE') }}</option>
        </select>
      </div>

      <div class="admin-field">
        <p class="label mb-1">{{ Lang::txt('COM_TOOLS_FIELD_STATE') }}</p>
        <div class="flex flex-col gap-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[state]" value="up"
                   class="radio radio-sm"
                   @checked($row->get('state') == 'up') />
            <span>{{ Lang::txt('COM_TOOLS_FIELD_STATE_UP') }}</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="fields[state]" value="down"
                   class="radio radio-sm"
                   @checked($row->get('state') == 'down') />
            <span>{{ Lang::txt('COM_TOOLS_FIELD_STATE_DOWN') }}</span>
          </label>
        </div>
      </div>

    </x-admin-fieldset>

    {{-- Network params fieldset --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIEDSET_ZONES_PARAMS') }}">

      <div class="admin-field">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="hidden" name="zoneparams[websocket_enable]" value="0" />
          <input type="checkbox"
                 name="zoneparams[websocket_enable]"
                 id="field-zone-params-websocket-enable"
                 class="checkbox checkbox-sm"
                 value="1"
                 @checked($wsEnabled) />
          <span class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_ZONE_WEBSOCKET_PROXY_ENABLE') }}</span>
        </label>
      </div>
      <div class="admin-field {{ !$wsEnabled ? 'opacity-50' : '' }}">
        <label for="field-zone-params-websocket-server" class="label text-base-content">
          {{ Lang::txt('COM_TOOLS_FIELD_ZONE_WEBSOCKET_PROXY_SERVER') }}
        </label>
        <input type="text"
               name="zoneparams[websocket_server]"
               id="field-zone-params-websocket-server"
               class="input input-bordered w-full"
               maxlength="255"
               @disabled(!$wsEnabled)
               value="{{ $row->params->get('websocket_server', 'ws://' . $hn . ':8080') }}" />
      </div>
      <div class="admin-field {{ !$wsEnabled ? 'opacity-50' : '' }}">
        <label for="field-zone-params-websocket-secure-server" class="label text-base-content">
          {{ Lang::txt('COM_TOOLS_FIELD_ZONE_WEBSOCKET_PROXY_SECURE_SERVER') }}
        </label>
        <input type="text"
               name="zoneparams[websocket_secure_server]"
               id="field-zone-params-websocket-secure-server"
               class="input input-bordered w-full"
               maxlength="255"
               @disabled(!$wsEnabled)
               value="{{ $row->params->get('websocket_secure_server', 'wss://' . $hn . ':8443') }}" />
      </div>

      <div class="divider"></div>

      <div class="admin-field">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="hidden" name="zoneparams[vnc_enable]" value="0" />
          <input type="checkbox"
                 name="zoneparams[vnc_enable]"
                 id="field-zone-params-vnc-enable"
                 class="checkbox checkbox-sm"
                 value="1"
                 @checked($vncEnabled) />
          <span class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_ZONE_VNC_PROXY_ENABLE') }}</span>
        </label>
      </div>
      <div class="admin-field {{ !$vncEnabled ? 'opacity-50' : '' }}">
        <label for="field-zone-params-vnc-server" class="label text-base-content">
          {{ Lang::txt('COM_TOOLS_FIELD_ZONE_VNC_PROXY_SERVER') }}
        </label>
        <input type="text"
               name="zoneparams[vnc_server]"
               id="field-zone-params-vnc-server"
               class="input input-bordered w-full"
               maxlength="255"
               @disabled(!$vncEnabled)
               value="{{ $row->params->get('vnc_server', 'http://' . $hn . ':80') }}" />
      </div>
      <div class="admin-field {{ !$vncEnabled ? 'opacity-50' : '' }}">
        <label for="field-zone-params-vnc-secure-server" class="label text-base-content">
          {{ Lang::txt('COM_TOOLS_FIELD_ZONE_VNC_PROXY_SECURE_SERVER') }}
        </label>
        <input type="text"
               name="zoneparams[vnc_secure_server]"
               id="field-zone-params-vnc-secure-server"
               class="input input-bordered w-full"
               maxlength="255"
               @disabled(!$vncEnabled)
               value="{{ $row->params->get('vnc_secure_server', 'https://' . $hn . ':80') }}" />
      </div>

    </x-admin-fieldset>
  </div>

  {{-- Locations tab --}}
  <div data-tab-panel="locations" class="hidden">
    <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIELDSET_LOCATIONS') }}">
      @if($locIframeUrl)
        <iframe width="100%"
                height="400"
                name="locationslist"
                id="locationslist"
                style="border:0"
                src="{{ $locIframeUrl }}"></iframe>
      @else
        <p class="text-muted-foreground">{{ Lang::txt('COM_TOOLS_LOCATIONS_ADDED_LATER') }}</p>
      @endif
    </x-admin-fieldset>
  </div>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_COL_ID') }}</td>
          <td>{{ $row->get('id') ?: '—' }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_COL_STATE') }}</td>
          <td>{{ $row->get('state') }}</td>
        </tr>
      </tbody>
    </table>

    {{-- Zone image --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIELDSET_IMAGE') }}">
      @if($row->exists())
        <div id="ajax-uploader"
             data-action="{{ $ajaxUrl }}"
             data-instructions="{{ Lang::txt('COM_TOOLS_IMAGE_CLICK_OR_DROP') }}">
          <noscript>
            <iframe width="100%" height="350" name="filer" id="filer" style="border:0"
                    src="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&tmpl=component&id=' . $row->get('id'), false) }}"></iframe>
          </noscript>
        </div>
        <div class="flex gap-3 mt-3 items-start">
          <img id="img-display"
               src="{{ $imgSrc }}"
               alt="{{ Lang::txt('COM_TOOLS_FIELDSET_IMAGE') }}"
               class="rounded border border-base-300 max-w-24" />
          <table class="text-sm">
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_FILE') }}</td>
                <td id="img-name">{{ $row->get('picture', Lang::txt('COM_TOOLS_IMAGE_NONE')) }}</td></tr>
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_SIZE') }}</td>
                <td id="img-size">{{ \Hubzero\Utility\Number::formatBytes($imgSize) }}</td></tr>
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_WIDTH') }}</td>
                <td id="img-width">{{ $imgW }} px</td></tr>
            <tr><td class="text-muted-foreground pr-2">{{ Lang::txt('COM_TOOLS_IMAGE_HEIGHT') }}</td>
                <td id="img-height">{{ $imgH }} px</td></tr>
            <tr><td colspan="2">
              <a id="img-delete" href="{{ $deleteImgUrl }}" class="link link-error text-xs">
                {{ Lang::txt('JDELETE') }}
              </a>
            </td></tr>
          </table>
        </div>
        <input type="hidden" name="currentfile" id="currentfile" value="{{ $pic }}" />
      @else
        <p class="text-warning text-sm">{{ Lang::txt('COM_TOOLS_PICTURE_ADDED_LATER') }}</p>
      @endif
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="task"       value="save" />
</x-admin-edit>
