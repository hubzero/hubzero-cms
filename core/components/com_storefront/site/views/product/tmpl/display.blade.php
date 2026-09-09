{{--
 * Product detail — image, pricing, options, add-to-cart form
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Component;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $cartUrl = Route::url('index.php?option=com_cart');
    $imagesFolder = $config->get('imagesFolder', '/site/storefront/products');
    $imgPath = '/app/' . trim($imagesFolder, '/') . '/' . $pId . '/';

    // Resolve primary image
    $firstImg = $product->images[0]->imgName ?? '';
    if (empty($product->images) || !is_file(PATH_ROOT . $imgPath . $firstImg)) {
        $imgPath = str_replace(PATH_ROOT, '', Component::path('com_storefront'))
            . '/site/assets/img/';
        $image = new \stdClass();
        $image->imgName = 'noimage.png';
        $product->images[0] = $image;
    }
    $primaryImg = $imgPath . $product->images[0]->imgName;
    $isNoImage = str_contains($product->images[0]->imgName, 'noimage');

    // Format price
    $price = $__view->price;
    if (!$inStock) {
        $priceDisplay = Lang::txt('COM_STOREFRONT_OUT_OF_STOCK');
        $priceOut = true;
    } elseif ($price['high'] == $price['low']) {
        $priceDisplay = '$' . number_format($price['high'], 2);
        $priceOut = false;
    } else {
        $priceDisplay = '$' . number_format($price['low'], 2)
            . ' – $' . number_format($price['high'], 2);
        $priceOut = false;
    }

    // Quantity label
    if (!empty($meta->qtyTxt)) {
        $qtyTxt = $meta->qtyTxt;
    } elseif ($config->get('quantityText')) {
        $qtyTxt = $config->get('quantityText');
    } else {
        $qtyTxt = Lang::txt('COM_STOREFRONT_QUANTITY');
    }

    $qtyDropDown = $__view->qtyDropDown;
    $addToCartEnabled = (bool) $qtyDropDown;
@endphp

<x-page-container :title="$product->pName">
    @slot('actions')
        <a class="btn btn-sm" href="{{ $cartUrl }}">
            {{ Lang::txt('COM_STOREFRONT_CART') }}
        </a>
    @endslot

    <x-alert-list :notifications="$notifications" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Product image --}}
        <div>
            @if(!$isNoImage)
                <a href="{{ $primaryImg }}" target="_blank">
                    <img src="{{ $primaryImg }}"
                         alt="{{ $product->pName }}"
                         class="w-full rounded-lg" />
                </a>
            @else
                <img src="{{ $primaryImg }}"
                     alt="{{ $product->pName }}"
                     class="w-full rounded-lg" />
            @endif
        </div>

        {{-- Product info + form --}}
        <div>
            @if(empty($__view->statusMessage) || $__view->statusMessage != 'restricted')
                <div class="text-2xl font-bold mb-4 {{ $priceOut ? 'text-error' : '' }}">
                    {{ $priceDisplay }}
                </div>
            @endif

            <form action="{{ $_SERVER['REQUEST_URI'] }}" method="post"
                  id="productInfo"
                  @if($sfOptionsJson) data-sf-options="{{ $sfOptionsJson }}" @endif>
                <input type="hidden" name="pId" value="{{ $pId }}" />

                @if(isset($__view->options) && count($__view->options))
                    <div class="space-y-4 mb-4">
                        @foreach($__view->options as $optionGroupId => $info)
                            <div>
                                <p class="font-semibold mb-2">
                                    {{ $info['info']->ogName }}:
                                </p>
                                <div class="space-y-1">
                                    @foreach($info['options'] as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer"
                                               for="option_{{ $opt->oId }}">
                                            <input type="radio"
                                                   class="radio radio-sm"
                                                   name="og[{{ $optionGroupId }}]"
                                                   value="{{ $opt->oId }}"
                                                   id="option_{{ $opt->oId }}" />
                                            <span>{{ $opt->oName }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div id="qtyWrap" data-label="{{ $qtyTxt }}" class="mb-4">
                    @if($qtyDropDown && $qtyDropDown > 1)
                        <label for="qty" class="font-semibold mr-2">
                            {{ $qtyTxt }}
                        </label>
                        <select name="qty" id="qty"
                                class="select select-bordered select-sm">
                            @for($i = 1; $i <= $qtyDropDown; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    @endif
                </div>

                @if($inStock && $productAvailable)
                    <button type="submit"
                            name="addToCart" value="1"
                            id="addToCart"
                            class="btn btn-primary {{ $addToCartEnabled ? '' : 'btn-disabled' }}">
                        {{ Lang::txt('COM_STOREFRONT_ADD_TO_CART') }}
                    </button>
                @endif
            </form>

            @if($product->pTagline)
                <h3 class="text-lg font-semibold mt-6 mb-2">
                    {{ $product->pTagline }}
                </h3>
            @endif

            @if($product->pDescription)
                <div class="prose mt-4">
                    {!! $product->pDescription !!}
                </div>
            @endif

            @if(!empty($product->pFeatures))
                <div class="prose mt-4">
                    {!! $product->pFeatures !!}
                </div>
            @endif
        </div>
    </div>
</x-page-container>
