{{--
 * Storefront homepage — search bar and category grid
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $baseUrl = Route::url('index.php?option=com_storefront');
    $searchUrl = $baseUrl . 'search/';
    $cartUrl = Route::url('index.php?option=com_cart');
    $collectionsFolder = $config->get(
        'collectionsImagesFolder',
        '/site/storefront/collections'
    );
@endphp

<x-page-container :title="Lang::txt('COM_STOREFRONT')">
    @slot('actions')
        <a class="btn btn-sm" href="{{ $cartUrl }}">
            {{ Lang::txt('COM_STOREFRONT_CART') }}
        </a>
    @endslot

    <x-search-bar
        :action="$searchUrl"
        name="q"
        query=""
        :placeholder="Lang::txt('COM_STOREFRONT_SEARCH_PLACEHOLDER')"
        :buttonLabel="Lang::txt('COM_STOREFRONT_SEARCH')"
    />

    @if(count($categories))
        <x-card-grid cols="3">
            @foreach($categories as $category)
                @php
                    $categoryId = !empty($category->cAlias)
                        ? $category->cAlias
                        : $category->cId;
                    $categoryUrl = $baseUrl . 'browse/' . $categoryId;
                    $hasImage = isset($category->imgName) && $category->imgName;
                    if ($hasImage) {
                        $imgPath = '/app/' . trim($collectionsFolder, '/')
                            . '/' . $category->cId . '/'
                            . $category->imgName;
                    }
                @endphp
                <div class="card bg-base-100 shadow-sm">
                    @if($hasImage)
                        <figure>
                            <img src="{{ $imgPath }}"
                                 alt="{{ $category->cName }}"
                                 class="w-full h-48 object-cover" />
                        </figure>
                    @endif
                    <div class="card-body">
                        <h2 class="card-title">
                            <a href="{{ $categoryUrl }}"
                               class="link link-hover link-primary">
                                {{ $category->cName }}
                            </a>
                        </h2>
                    </div>
                </div>
            @endforeach
        </x-card-grid>
    @else
        <x-empty-state
            :title="Lang::txt('COM_STOREFRONT_NO_CATEGORIES_SETUP')"
            :message="Lang::txt('COM_STOREFRONT_NO_CATEGORIES_DESC')"
        />
    @endif
</x-page-container>
