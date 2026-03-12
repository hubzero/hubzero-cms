{{--
  Support — Single media attachment partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $deleteUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=delete&asset=' . $asset->get('id')
      . '&no_html=' . $no_html, false
  );
  $deleteTitle = Lang::txt('COM_SUPPORT_DELETE');
@endphp
        <p class="item-asset">
            <span class="asset-file">
                {{ $asset->get('filename') }}
            </span>
            <span class="asset-description">
                <a
                    class="icon-delete delete"
                    data-id="{{ $asset->get('id') }}"
                    href="{{ $deleteUrl }}"
                    title="{{ $deleteTitle }}"
                >
                    {{ Lang::txt('COM_SUPPORT_DELETE') }}
                </a>
            </span>
        </p>
