{{--
  Storefront Collection — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

  $text = ($task == 'edit')
      ? Lang::txt('COM_STOREFRONT_EDIT')
      : Lang::txt('COM_STOREFRONT_NEW');

  Toolbar::title(
      Lang::txt('COM_STOREFRONT') . ': '
      . Lang::txt('COM_STOREFRONT_COLLECTION') . ': ' . $text,
      'storefront'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();

  $__view->css()
      ->js('jquery.fileuploader.js', 'system')
      ->js();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_DETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_STOREFRONT_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[cName]"
               id="field-title"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               required
               value="{{ $row->getName() }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">
          {{ Lang::txt('Alias') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               required
               value="{{ $row->getAlias() }}" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_ID') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_STOREFRONT_ID') }}</td>
              <td>
                {{ $row->getId() }}
                <input type="hidden"
                       name="fields[cId]"
                       id="field-id"
                       value="{{ $row->getId() }}" />
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_PUBLISH_OPTIONS') }}">

        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_STOREFRONT_PUBLISH') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered select-sm w-full">
            <option value="0" @selected($row->getActiveStatus() == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->getActiveStatus() == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
          </select>
        </div>

    </x-admin-fieldset>

    {{-- Image --}}
    <x-admin-fieldset legend="{{ Lang::txt('Image') }}">
        @if($row->getId())
          @php
            $img = $row->getImage();

            if (!empty($img)) {
                $image = $img->imgName;
                $pics  = explode(DS, $image);
                $file  = end($pics);
            } else {
                $image = false;
                $file  = false;
                $img   = new \stdClass();
                $img->imgId = null;
            }

            $uploadUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=images&task=upload&type=collection&id='
                . $row->getId() . '&no_html=1&'
                . Session::getFormToken() . '=1',
                false, false
            );
            $iframeSrc = Route::url(
                'index.php?option=' . $option
                . '&controller=images&tmpl=component&file=' . $file
                . '&type=collection&id=' . $row->getId(),
                false, false
            );

            $width     = 0;
            $height    = 0;
            $this_size = 0;
            $colImgFolder = $config->get(
                'collectionsImagesFolder',
                '/site/storefront/collections'
            );
            $pathl = DS . trim($colImgFolder, DS) . DS . $row->getId();

            if ($image && file_exists(PATH_APP . $pathl . DS . $file)) {
                $this_size = filesize(PATH_APP . $pathl . DS . $file);
                list($width, $height, $type, $attr) = getimagesize(
                    PATH_APP . $pathl . DS . $file
                );
                $pic  = $file;
                $path = '/app/' . $pathl;
            } else {
                $image = false;
                $pic   = 'noimage.png';
                $relDir = str_replace(
                    PATH_ROOT,
                    '',
                    __DIR__
                );
                $path = dirname(dirname(dirname(dirname($relDir))))
                    . '/site/assets/img';
            }

            $deleteUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=images&tmpl=component&task=remove'
                . '&currentfile=' . $img->imgId
                . '&type=collection&id=' . $row->getId()
                . '&' . Session::getFormToken() . '=1',
                false, false
            );
            $hideClass   = $image ? '' : 'hide';
            $noImgPath   = '/core/components/com_storefront/site/assets/img/noimage.png';
          @endphp

          <div class="uploader-wrap">
            <div id="ajax-uploader"
                 data-action="{{ $uploadUrl }}"
                 data-instructions="{{ Lang::txt('COM_STOREFRONT_UPLOAD_CLICK_OR_DROP') }}">
              <noscript>
                <iframe height="350"
                        name="filer"
                        id="filer"
                        src="{{ $iframeSrc }}"></iframe>
              </noscript>
            </div>
          </div>

          <div id="img-container">
            <img id="img-display"
                 src="{{ $path . DS . $pic }}"
                 alt="{{ Lang::txt('COM_STOREFRONT_PRODUCT_IMAGE') }}" />
            <input type="hidden"
                   name="currentfile"
                   id="currentfile"
                   value="{{ $img->imgId }}" />
          </div>

          <table class="formed">
            <tbody>
              <tr>
                <th>{{ Lang::txt('COM_STOREFRONT_FILE') }}:</th>
                <td><span id="img-name">{{ $image }}</span></td>
                <td>
                  <a id="img-delete {{ $hideClass }}"
                     href="{{ $deleteUrl }}"
                     title="{{ Lang::txt('Delete') }}"
                     data-noimg="{{ $noImgPath }}">[ x ]</a>
                </td>
              </tr>
              <tr>
                <th>{{ Lang::txt('COM_STOREFRONT_PICTURE_SIZE') }}:</th>
                <td><span id="img-size">{{ \Hubzero\Utility\Number::formatBytes($this_size) }}</span></td>
                <td></td>
              </tr>
              <tr>
                <th>{{ Lang::txt('COM_STOREFRONT_PICTURE_WIDTH') }}:</th>
                <td><span id="img-width">{{ $width }}</span> px</td>
                <td></td>
              </tr>
              <tr>
                <th>{{ Lang::txt('COM_STOREFRONT_PICTURE_HEIGHT') }}:</th>
                <td><span id="img-height">{{ $height }}</span> px</td>
                <td></td>
              </tr>
            </tbody>
          </table>
        @else
          <p class="warning">{{ Lang::txt('COM_STOREFRONT_PICTURE_ADDED_LATER') }}</p>
        @endif
    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="fields[cId]" value="{{ $row->getId() }}" />
</x-admin-edit>
