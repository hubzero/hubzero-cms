{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $link = $item->link('version');
    if ($item->get('state') == \Components\Publications\Models\Orm\Version::STATE_DRAFT) {
        $link = $item->link('edit');
    }
@endphp

<li class="list-row">
    <div class="list-col grow">
        <a href="{{ Route::url($link) }}">
            {{ $item->get('title') }}
        </a>
        <span class="text-xs text-base-content/60">
            {{ Lang::txt('MOD_MYPUBLICATIONS_VERSION', $item->get('version_label')) }}
            @if (!$item->publication->project->isProvisioned())
                &mdash;
                {{ Lang::txt('MOD_MYPUBLICATIONS_PROJECT', \Hubzero\Utility\Str::truncate($item->publication->project->get('title'), 100)) }}
            @endif
        </span>
    </div>
</li>
