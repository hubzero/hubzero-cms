@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

$item = $row->item();
$content = $row->description('parsed');
$content = ($content ?: $item->description('parsed'));
$path = $item->filespace() . DS . $item->get('id');
$base = $member->link() . '&active=' . $name;
$assets = $item->assets();
@endphp

@if ($item->get('title'))
    <h4>{{ e(stripslashes($item->get('title'))) }}</h4>
@endif

@if ($assets->total() > 0)
    @php
        $images = [];
        $files = [];
        foreach ($assets as $asset) {
            if ($asset->image()) {
                $images[] = $asset;
            } else {
                $files[] = $asset;
            }
        }
    @endphp

    @if (count($images) > 0)
        @php
            $first = array_shift($images);
            $isLocal = !filter_var($first->file('original'), FILTER_VALIDATE_URL);
            $imgPath = $isLocal ? $path . DS . $first->file('thumbnail') : $first->file('original');
        @endphp

        @if ($first->exists())
            @php
                list($originalWidth, $originalHeight) = getimagesize($imgPath);
                $ratio = $originalWidth / $originalHeight;
                $height = (!isset($actual) || !$actual)
                    ? round($params->get('maxWidth', 290) / $ratio, 0, PHP_ROUND_HALF_UP)
                    : $originalHeight;
                $alt = e(stripslashes($first->get('description', '')));
            @endphp

            @if ($isLocal)
                <div class="holder">
                    <a class="img-link"
                        href="{{ $first->link('original') }}"
                        data-rel="post{{ $row->get('id') }}"
                        data-download="{{ $first->link('original') }}"
                        data-downloadtext="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DOWNLOAD') }}">
                        <img src="{{ $first->link('thumb') }}"
                            alt="{{ $alt }}"
                            class="img"
                            height="{{ $height }}" />
                    </a>
                </div>
            @else
                <div class="holder">
                    <a rel="nofollow" download="download" class="img-link"
                        href="{{ $imgPath }}"
                        data-rel="post{{ $row->get('id') }}"
                        data-download="{{ $imgPath }}"
                        data-downloadtext="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DOWNLOAD') }}">
                        <img src="{{ $imgPath }}"
                            alt="{{ $alt }}"
                            class="img"
                            height="{{ $height }}" />
                    </a>
                </div>
            @endif
        @else
            <div class="holder notfound">
                <p class="warning">{{ Lang::txt('Image not found.') }}</p>
            </div>
        @endif

        @if (count($images) > 0)
            <div class="gallery">
                @foreach ($images as $asset)
                    <a class="img-link"
                        href="{{ $asset->link('medium') }}"
                        data-rel="post{{ $row->get('id') }}"
                        data-download="{{ $asset->link('original') }}"
                        data-downloadtext="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DOWNLOAD') }}">
                        <img src="{{ $asset->link('thumb') }}"
                            alt="{{ e(stripslashes($asset->get('description', ''))) }}"
                            class="img"
                            width="50"
                            height="50" />
                    </a>
                @endforeach
            </div>
        @endif
    @endif

    @if (count($files) > 0)
        <ul class="file-list">
            @foreach ($files as $asset)
                <li class="type-{{ $asset->get('type') }}">
                    <a href="{{ $asset->isLink() ? $asset->get('filename') : $asset->link('original') }}"
                        {!! $asset->isLink() ? ' rel="external nofollow noreferrer"' : '' !!}>
                        {{ $asset->get('filename') }}
                    </a>
                    <span class="file-meta">
                        <span class="file-size">
                            @if (!$asset->isLink())
                                {{ \Hubzero\Utility\Number::formatBytes($asset->size()) }}
                            @elseif ($asset->isExternalLink())
                                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LINK_EXTERNAL') }}
                            @else
                                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_LINK_INTERNAL') }}
                            @endif
                        </span>
                        @if ($desc = $asset->get('description'))
                            <span class="file-description">{{ e($desc) }}</span>
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    @endif
@endif

@if ($content)
    <div class="description">
        {!! $content !!}
    </div>
@endif
