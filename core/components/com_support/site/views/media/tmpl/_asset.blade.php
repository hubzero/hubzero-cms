{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $deleteUrl = \Hubzero\Facades\Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=delete&asset=' . $asset->get('id')
        . '&no_html=' . $no_html
    );
    $deleteTitle = \Hubzero\Facades\Lang::txt('COM_SUPPORT_DELETE');
@endphp

        <p class="item-asset">
            <span class="asset-file">
                {{ $__view->escape(stripslashes($asset->get('filename'))) }}
            </span>
            <span class="asset-description">
                <a
                    class="icon-delete delete"
                    data-id="{{ $asset->get('id') }}"
                    href="{{ $deleteUrl }}"
                    title="{{ $deleteTitle }}"
                >
                    {{ $deleteTitle }}
                </a>
            </span>
        </p>
