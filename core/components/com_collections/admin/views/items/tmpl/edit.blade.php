{{--
  Items — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\User;

  $canDo = \Components\Collections\Helpers\Permissions::getActions('post');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  $dir = $row->get('id') ?: 'tmp' . time();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COLLECTIONS') }}: {{ Lang::txt('COM_COLLECTIONS_ITEMS') }}: {{ $text }}"
    icon="collections"
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
        <label for="field-title" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_TITLE') }}</label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-url" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_URL') }}</label>
        <input type="text"
               name="fields[url]"
               id="field-url"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->get('url', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_DESCRIPTION') }}</label>
        {!! $__view->editor(
            'fields[description]',
            e($row->get('description')),
            35,
            10,
            'field-description',
            ['class' => 'minimal no-footer', 'buttons' => false]
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-tags" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_TAGS') }}</label>
        <input type="text"
               name="tags"
               id="field-tags"
               class="input input-bordered w-full"
               value="{{ $row->tags('string') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_COLLECTIONS_FIELD_TAGS_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  {{-- Asset uploader --}}
  <x-admin-fieldset>
      @php
        $uploadUrl = Route::url(
            'index.php?option=' . $option . '&no_html=1&controller=media&task=upload',
            false, false
        );
        $listUrl = Route::url(
            'index.php?option=' . $option . '&no_html=1&controller=media&task=list&dir=',
            false, false
        );
        $deleteUrl = Route::url(
            'index.php?option=' . $option . '&no_html=1&controller=media&task=delete&dir=',
            false, false
        );
        $createUrl = Route::url(
            'index.php?option=' . $option . '&no_html=1&controller=media&task=create&dir=',
            false, false
        );
      @endphp
      <div class="asset-uploader">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div>
            <div id="ajax-uploader"
                 data-txt-instructions="{{ Lang::txt('COM_COLLECTIONS_CLICK_OR_DROP_FILE') }}"
                 data-action="{{ $uploadUrl }}"
                 data-list="{{ $listUrl }}">
              <noscript>
                <label for="field-upload">{{ Lang::txt('COM_COLLECTIONS_FIELD_FILE') }}</label>
                <input type="file" name="upload" id="field-upload" />
              </noscript>
            </div>
          </div>
          <div>
            <div id="link-adder"
                 data-txt-delete="{{ Lang::txt('JACTION_DELETE') }}"
                 data-txt-instructions="{{ Lang::txt('COM_COLLECTIONS_CLICK_TO_ADD_LINK') }}"
                 data-base="{{ $deleteUrl }}"
                 data-action="{{ $createUrl }}"
                 data-list="{{ $listUrl }}">
              <noscript>
                <label for="add-link">{{ Lang::txt('COM_COLLECTIONS_FIELD_LINK') }}</label>
                <input type="text" name="assets[-1][filename]" id="add-link" value="http://" />
                <input type="hidden" name="assets[-1][id]" value="0" />
                <input type="hidden" name="assets[-1][type]" value="link" />
              </noscript>
            </div>
          </div>
        </div>

      <div id="ajax-uploader-list">
        @php
          $assets = $row->assets()->rows();
        @endphp
        @if($assets->count() > 0)
          @foreach($assets as $ai => $asset)
            {!! $__view->view('_asset', 'media')
                 ->set('i', $ai)
                 ->set('option', $option)
                 ->set('controller', $controller)
                 ->set('asset', $asset)
                 ->set('no_html', 1)
                 ->loadTemplate() !!}
          @endforeach
        @endif
      </div>
    </div>
  </x-admin-fieldset>

  {{-- Posts tab (edit only) --}}
  @if($row->get('id'))
    <x-admin-fieldset legend="{{ Lang::txt('COM_COLLECTIONS_POSTS') }}">
        @php
          $iframeSrc = Route::url(
              'index.php?option=' . $option
              . '&controller=posts&tmpl=component&item_id='
              . $row->get('id') . '&t=' . time(),
              false, false
          );
        @endphp
        <iframe height="500"
                name="grouper"
                id="grouper"
                title="{{ Lang::txt('COM_COLLECTIONS_POSTS') }}"
                class="w-full border border-base-300 rounded-box"
                src="{{ $iframeSrc }}"></iframe>
    </x-admin-fieldset>
  @endif

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            @if(!$row->isNew())
              <tr>
                <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_ID') }}</td>
                <td>{{ $row->get('id') }}</td>
              </tr>
            @endif
            <tr>
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_TYPE') }}</td>
              <td>
                {{ $row->get('type', 'file') }}
                <input type="hidden" name="fields[type]"
                       value="{{ $row->get('type', 'file') }}" />
              </td>
            </tr>
            @if($row->get('object_id'))
              <tr>
                <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_OBJECT_ID') }}</td>
                <td>{{ $row->get('object_id') }}</td>
              </tr>
            @endif
            <tr>
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_CREATOR') }}</td>
              <td>
                @php $editor = User::getInstance($row->get('created_by')); @endphp
                {{ $editor->get('name') }}
                <input type="hidden" name="fields[created_by]"
                       value="{{ $row->get('created_by') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_CREATED') }}</td>
              <td>
                {{ $row->get('created') }}
                <input type="hidden" name="fields[created]"
                       value="{{ $row->get('created') }}" />
              </td>
            </tr>
            @if($row->get('modified_by'))
              <tr>
                <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_MODIFIER') }}</td>
                <td>
                  @php $modifier = User::getInstance($row->get('modified_by')); @endphp
                  {{ $modifier->get('name') }}
                  <input type="hidden" name="fields[modified_by]"
                         value="{{ $row->get('modified_by') }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_MODIFIED') }}</td>
                <td>
                  {{ $row->get('modified') }}
                  <input type="hidden" name="fields[modified]"
                         value="{{ $row->get('modified') }}" />
                </td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_STATE') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_ACCESS') }}</label>
          <select name="fields[access]" id="field-access"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('access') == 0)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC') }}</option>
            <option value="1" @selected($row->get('access') == 1)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED') }}</option>
            <option value="4" @selected($row->get('access') == 4)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE') }}</option>
          </select>
        </div>

    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="dir" value="{{ $dir }}" />
  <input type="hidden" name="fields[object_id]" value="{{ $row->get('object_id') }}" />
</x-admin-edit>
