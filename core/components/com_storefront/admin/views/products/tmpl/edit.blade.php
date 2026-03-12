{{--
  Storefront Product — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

  $text = ($task == 'edit')
      ? Lang::txt('COM_STOREFRONT_EDIT')
      : Lang::txt('COM_STOREFRONT_NEW');

  $title = Lang::txt('COM_STOREFRONT') . ': '
      . Lang::txt('COM_STOREFRONT_PRODUCT') . ': ' . $text;
  Toolbar::title($title, 'storefront.png');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('product');

  if (empty($meta->qtyTxt)) {
      $meta->qtyTxt = '';
  }

  $__view->js('product-edit.blade.js');
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
               name="fields[pName]"
               id="field-title"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               required
               value="{{ $row->getName() }}" />
      </div>

      <div class="admin-field">
        <label for="field-alais" class="label">{{ Lang::txt('Alias') }}</label>
        <input type="text"
               name="fields[pAlias]"
               id="field-alais"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               value="{{ $row->getAlias() }}" />
      </div>

      <div class="admin-field">
        <label for="field-pTagline" class="label">
          {{ Lang::txt('COM_STOREFRONT_TAGLINE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[pTagline]"
               id="field-pTagline"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               required
               value="{{ $row->getTagline() }}" />
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">
          {{ Lang::txt('COM_STOREFRONT_DESCRIPTION') }}
          <span class="text-error">*</span>
        </label>
        {!! $__view->editor(
            'fields[pDescription]',
            e($row->getDescription()),
            50,
            10,
            'field-description',
            ['buttons' => false]
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-features" class="label">{{ Lang::txt('COM_STOREFRONT_FEATURES') }}</label>
        {!! $__view->editor(
            'fields[pFeatures]',
            e($row->getFeatures()),
            50,
            10,
            'field-features',
            ['buttons' => false]
        ) !!}
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta table --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_ID') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_STOREFRONT_ID') }}</td>
              <td>
                {{ $row->getId() }}
                <input type="hidden"
                       name="fields[pId]"
                       id="field-id"
                       value="{{ $row->getId() }}" />
              </td>
            </tr>
            @if ($row->getTypeInfo() && $row->getTypeInfo()->name == 'Software Download')
              <tr>
                <td>{{ Lang::txt('COM_STOREFRONT_DOWNLOADED') }}</td>
                <td>
                  {{ $downloaded }}
                  {{ $downloaded == 1 ? 'time' : 'times' }}
                </td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Options --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_OPTIONS') }}">

        <div class="admin-field">
          <label for="field-type" class="label">{{ Lang::txt('COM_STOREFRONT_TYPE') }}</label>
          <select name="fields[ptId]" id="field-type"
                  class="select select-bordered select-sm w-full">
            @foreach ($types as $type)
              <option value="{{ $type->ptId }}" @selected($row->getType() == $type->ptId)>
                {{ $type->ptName }}
              </option>
            @endforeach
          </select>
        </div>

        @if ($metaNeeded)
          <p>
            <a class="link link-hover text-primary"
               href="{{ Route::url('index.php?option=' . $option . '&controller=meta&task=edit&id=' . $row->getId(), false) }}">
              Edit type-related options (save product first if you updated the type)
            </a>
          </p>
        @endif

        <div class="admin-field">
          <label for="field-multi" class="label">{{ Lang::txt('COM_STOREFRONT_ALLOW_MULTIPLE') }}</label>
          <select name="fields[pAllowMultiple]" id="field-multi"
                  class="select select-bordered select-sm w-full">
            <option value="0" @selected($row->getAllowMultiple() == 0)>
              {{ Lang::txt('COM_STOREFRONT_NO') }}
            </option>
            <option value="1" @selected($row->getAllowMultiple() == 1)>
              {{ Lang::txt('COM_STOREFRONT_YES') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-qtytxt" class="label">{{ Lang::txt('COM_STOREFRONT_QTY_TXT') }}</label>
          <input type="text"
                 name="fields[pQtyTxt]"
                 id="field-qtytxt"
                 class="input input-bordered input-sm w-full"
                 maxlength="100"
                 value="{{ $meta->qtyTxt }}" />
        </div>

    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_PUBLISH_OPTIONS') }}">

        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_STOREFRONT_STATE') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered select-sm w-full">
            <option value="0" @selected($row->getActiveStatus() == 0)>
              {{ Lang::txt('JUNPUBLISHED') }}
            </option>
            <option value="1" @selected($row->getActiveStatus() == 1)>
              {{ Lang::txt('JPUBLISHED') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-publish_up" class="label">{{ Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_UP') }}</label>
          @php
            $publishUp = $row->getPublishTime()->publish_up;
            $publishUpVal = ($publishUp && $publishUp != '0000-00-00 00:00:00')
                ? e(Date::of($publishUp)->toLocal('Y-m-d H:i:s'))
                : '';
          @endphp
          {!! Html::input('calendar', 'fields[publish_up]', $publishUpVal, ['id' => 'field-publish_up']) !!}
        </div>

        <div class="admin-field">
          <label for="field-publish_down" class="label">{{ Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_DOWN') }}</label>
          @php
            $publishDown = $row->getPublishTime()->publish_down;
            $publishDownVal = ($publishDown && $publishDown != '0000-00-00 00:00:00')
                ? e(Date::of($publishDown)->toLocal('Y-m-d H:i:s'))
                : '';
          @endphp
          {!! Html::input('calendar', 'fields[publish_down]', $publishDownVal, ['id' => 'field-publish_down']) !!}
        </div>

        @if ($config->get('productAccess'))
          <div class="admin-field"
               data-hint="{{ Lang::txt('COM_STOREFRONT_ACCESS_GROUPS_HINT') }}">
            <label class="label">{!! Lang::txt('User is <strong>one</strong> of the following') !!}</label>
            <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_STOREFRONT_ACCESS_GROUPS_HINT') }}</p>
            {!! Html::access('usergroups', 'accessgroupsyes', $row->getAccessGroups('include'), true) !!}
          </div>

          <p class="font-semibold text-center">AND</p>

          <div class="admin-field"
               data-hint="{{ Lang::txt('COM_STOREFRONT_ACCESS_GROUPS_HINT') }}">
            <label class="label">{!! Lang::txt('User is <strong>not</strong> one of the following') !!}</label>
            <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_STOREFRONT_ACCESS_GROUPS_HINT') }}</p>
            {!! Html::access('usergroups', 'accessgroupsno', $row->getAccessGroups('exclude'), true) !!}
          </div>
        @else
          <div class="admin-field">
            <label for="field-access" class="label">{{ Lang::txt('COM_STOREFRONT_ACCESS_LEVEL') }}</label>
            {!! Html::access('level', 'fields[access]', $row->getAccessLevel(), '', true, 'field-access') !!}
          </div>
        @endif

    </x-admin-fieldset>

    {{-- Collections --}}
    @if ($collections->total())
      <x-admin-fieldset legend="{{ Lang::txt('Collections') }}">
          @php $productCollections = $row->getCollections(); @endphp
          <ul class="space-y-1">
            @foreach ($collections as $cat)
              @if ($cat->cActive || in_array($cat->cId, $productCollections))
                <li>
                  <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox"
                           name="fields[collections][]"
                           class="checkbox checkbox-sm"
                           value="{{ $cat->cId }}"
                           id="collection_{{ $cat->cId }}"
                           @checked(in_array($cat->cId, $productCollections)) />
                    <span class="text-base-content">{{ $cat->cName }}</span>
                  </label>
                </li>
              @endif
            @endforeach
          </ul>
      </x-admin-fieldset>
    @endif

    {{-- Option Groups --}}
    @if ($optionGroups->total())
      <x-admin-fieldset legend="{{ Lang::txt('Product option groups') }}">
          <ul class="space-y-1">
            @foreach ($optionGroups as $og)
              @if ($og->ogActive || in_array($og->ogId, $productOptionGroups))
                <li>
                  <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox"
                           name="fields[optionGroups][]"
                           class="checkbox checkbox-sm"
                           value="{{ $og->ogId }}"
                           id="optionGroup_{{ $og->ogId }}"
                           @checked(in_array($og->ogId, $productOptionGroups)) />
                    <span class="text-base-content">{{ $og->ogName }}</span>
                  </label>
                </li>
              @endif
            @endforeach
          </ul>
      </x-admin-fieldset>
    @endif

    {{-- Image --}}
    <x-admin-fieldset legend="{{ Lang::txt('Image') }}">

        @if ($row->getId())
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

            $uploadAction = Route::url(
                'index.php?option=' . $option
                . '&controller=images&task=upload&type=product&id='
                . $row->getId() . '&no_html=1&'
                . Session::getFormToken() . '=1',
                false, false
            );
            $iframeSrc = Route::url(
                'index.php?option=' . $option
                . '&controller=images&tmpl=component&file=' . $file
                . '&type=product&id=' . $row->getId(),
                false, false
            );

            $width     = 0;
            $height    = 0;
            $this_size = 0;
            $imagesFolder = $config->get('imagesFolder', '/site/storefront/products');
            $pathl = DS . trim($imagesFolder, DS) . DS . $row->getId();

            if ($image && file_exists(PATH_APP . $pathl . DS . $file)) {
                $this_size = filesize(PATH_APP . $pathl . DS . $file);
                list($width, $height, $imgType, $attr) = getimagesize(PATH_APP . $pathl . DS . $file);
                $pic  = $file;
                $path = '/app/' . $pathl;
            } else {
                $image = false;
                $pic   = 'noimage.png';
                $relDir = str_replace(PATH_ROOT, '', __DIR__);
                $path  = dirname(dirname(dirname(dirname($relDir))))
                    . '/site/assets/img' . DS;
            }

            $deleteUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=images&tmpl=component&task=remove'
                . '&type=product&id=' . $row->getId()
                . '&' . Session::getFormToken() . '=1',
                false, false
            );
            $rootPath = rtrim(Request::root(true), '/');
          @endphp

          <div class="uploader-wrap">
            <div id="ajax-uploader"
                 data-action="{{ $uploadAction }}"
                 data-upload-text="{{ Lang::txt('COM_STOREFRONT_UPLOAD_CLICK_OR_DROP') }}">
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
                  <a id="img-delete"
                     class="{{ $image ? '' : 'hidden' }}"
                     href="{{ $deleteUrl }}"
                     title="{{ Lang::txt('Delete') }}">[ x ]</a>
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

          @php
            $noImgSrc = $rootPath
                . '/core/components/com_storefront/site/assets/img/noimage.png';
            $uploadTxt = Lang::txt('COM_STOREFRONT_UPLOAD_CLICK_OR_DROP');
            $__view->js('jquery.fileuploader.js', 'system');
          @endphp
          <div id="product-image-config"
               data-no-image="{{ $noImgSrc }}"
               data-upload-text="{{ $uploadTxt }}"
               class="hidden"></div>

        @else
          <div role="alert" class="alert alert-warning">
            {{ Lang::txt('COM_STOREFRONT_PICTURE_ADDED_LATER') }}
          </div>
        @endif

    </x-admin-fieldset>
  @endslot
</x-admin-edit>
