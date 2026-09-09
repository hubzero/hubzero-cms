{{--
 * Product card partial — used in collection and search grids
 *
 * Expects: $product, $baseUrl, $imagesFolder, $noImagePath
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $productId = !empty($product->pAlias)
        ? $product->pAlias
        : $product->pId;
    $productUrl = $baseUrl . 'product/' . $productId;
    if ($product->imgName) {
        $imgSrc = '/app/' . trim($imagesFolder, '/')
            . '/' . $product->pId . '/'
            . $product->imgName;
    } else {
        $imgSrc = $noImagePath;
    }
@endphp
<div class="card bg-base-100 shadow-sm">
    <figure>
        <img src="{{ $imgSrc }}"
             alt="{{ $product->pName }}"
             class="w-full h-48 object-cover" />
    </figure>
    <div class="card-body">
        <h2 class="card-title text-base">
            <a href="{{ $productUrl }}"
               class="link link-hover link-primary">
                {{ $product->pName }}
            </a>
        </h2>
    </div>
</div>
