{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$item = $row->item();

$content = $row->description('parsed');
$content = ($content ?: $item->description('parsed'));

$path = $item->filespace() . DS . $item->get('id');
$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;

$assets = $item->assets();

$images = [];
$files = [];
if ($assets->total() > 0) {
    foreach ($assets as $asset) {
        if ($asset->image()) {
            $images[] = $asset;
        } else {
            $files[] = $asset;
        }
    }
}
@endphp

@if ($item->get('title'))
    <h4>{{ e(stripslashes($item->get('title'))) }}</h4>
@endif

@if ($assets->total() > 0)
    @if (count($images) > 0)
        @php
        $first = array_shift($images);
        $isLocal = (filter_var($first->file('original'), FILTER_VALIDATE_URL)) ? false : true;
        $imgPath = $isLocal ? $path . DS . $first->file('thumbnail') : $first->file('original');
        @endphp

        @if (file_exists($imgPath))
            @php
            list($originalWidth, $originalHeight) = getimagesize($imgPath);
            $ratio = $originalWidth / $originalHeight;
            $alt = e(stripslashes($first->get('description', '')));
            $height = (!isset($actual) || !$actual)
                ? round($params->get('maxWidth', 290) / $ratio, 0, PHP_ROUND_HALF_UP)
                : $originalHeight;
            @endphp

            @if ($isLocal)
                <div class="mb-3">
                    <a class="block"
                        href="{{ $first->link('medium') }}"
                        data-rel="post{{ $row->get('id') }}"
                        data-download="{{ $first->link('original') }}"
                        data-downloadtext="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_DOWNLOAD') }}">
                        <img src="{{ $first->link('thumb') }}"
                            alt="{{ $alt }}"
                            class="rounded-box max-w-full"
                            height="{{ $height }}" />
                    </a>
                </div>
            @else
                <div class="mb-3">
                    <a class="block" rel="nofollow" download="download"
                        href="{{ $imgPath }}"
                        data-rel="post{{ $row->get('id') }}"
                        data-download="{{ $imgPath }}"
                        data-downloadtext="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_DOWNLOAD') }}">
                        <img src="{{ $imgPath }}"
                            alt="{{ $alt }}"
                            class="rounded-box max-w-full"
                            height="{{ $height }}" />
                    </a>
                </div>
            @endif
        @else
            <div class="alert alert-warning mb-3">
                <p>{{ Lang::txt('Image not found.') }}</p>
            </div>
        @endif

        @if (count($images) > 0)
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach ($images as $asset)
                    <a href="{{ $asset->link('medium') }}"
                        data-rel="post{{ $row->get('id') }}"
                        data-download="{{ $asset->link('original') }}"
                        data-downloadtext="{{ Lang::txt('PLG_GROUPS_COLLECTIONS_DOWNLOAD') }}">
                        <img src="{{ $asset->link('thumb') }}"
                            alt="{{ e(stripslashes($asset->get('description', ''))) }}"
                            class="rounded-box"
                            width="50"
                            height="50" />
                    </a>
                @endforeach
            </div>
        @endif
    @endif

    @if (count($files) > 0)
        <ul class="menu bg-base-200 rounded-box mb-3">
            @foreach ($files as $asset)
                <li>
                    <a href="{{ $asset->isLink() ? $asset->get('filename') : $asset->link() }}"
                        @if ($asset->isLink()) rel="external nofollow noreferrer" @endif>
                        {{ $asset->get('filename') }}
                        <span class="text-xs opacity-70">
                            @if (!$asset->isLink() && $asset->exists())
                                {{ \Hubzero\Utility\Number::formatBytes($asset->size()) }}
                            @else
                                {{ $asset->isExternalLink() ? Lang::txt('external link') : Lang::txt('internal link') }}
                            @endif
                        </span>
                        @if ($desc = $asset->get('description'))
                            <span class="text-xs opacity-60">{{ e($desc) }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endif

@if ($content)
    <div class="prose prose-sm">
        {!! $content !!}
    </div>
@endif
