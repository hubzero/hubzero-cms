{{--
 * Storefront search results — search bar and product card grid
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Component;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    $baseUrl = Route::url('index.php?option=' . Request::getCmd('option'));
    $searchUrl = $baseUrl . 'search/';
    $cartUrl = Route::url('index.php?option=com_cart');
    $imagesFolder = $config->get('imagesFolder', '/site/storefront/products');
    $noImagePath = str_replace(PATH_ROOT, '', Component::path('com_storefront'))
        . '/site/assets/img/noimage.png';
@endphp

<x-page-container :title="Lang::txt('COM_STOREFRONT') . ' — ' . Lang::txt('COM_STOREFRONT_SEARCH_RESULTS')">
    @slot('actions')
        <a class="btn btn-sm" href="{{ $cartUrl }}">
            {{ Lang::txt('COM_STOREFRONT_CART') }}
        </a>
    @endslot

    <x-search-bar
        :action="$searchUrl"
        name="q"
        :query="$search"
        :placeholder="Lang::txt('COM_STOREFRONT_SEARCH_PLACEHOLDER')"
        :buttonLabel="Lang::txt('COM_STOREFRONT_SEARCH')"
    />

    @if(!empty($products))
        <x-card-grid cols="3">
            @foreach($products as $product)
                {!! $__view->view('_product-card', 'shared')
                    ->set('product', $product)
                    ->set('baseUrl', $baseUrl)
                    ->set('imagesFolder', $imagesFolder)
                    ->set('noImagePath', $noImagePath)
                    ->loadTemplate() !!}
            @endforeach
        </x-card-grid>
    @else
        <x-empty-state
            :title="Lang::txt('COM_STOREFRONT_NO_RESULTS')"
            :message="Lang::txt('COM_STOREFRONT_NO_RESULTS_DESC')"
        />
    @endif
</x-page-container>
