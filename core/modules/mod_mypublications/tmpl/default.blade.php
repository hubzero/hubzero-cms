{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div id="{{ $module->module . $module->id }}">
    @if ($params->get('button_show_all', 1) || $params->get('button_show_add', 1))
        <div class="flex gap-2 mb-3">
            @if ($params->get('button_show_all', 1))
                <a class="btn btn-sm btn-outline"
                    href="{{ Route::url('index.php?option=com_publications&task=browse') }}"
                >{{ Lang::txt('MOD_MYPUBLICATIONS_ALL_PUBLICATIONS') }}</a>
            @endif
            @if ($params->get('button_show_add', 1))
                <a class="btn btn-sm btn-outline"
                    href="{{ Route::url('index.php?option=com_publications&task=submit') }}"
                >{{ Lang::txt('MOD_MYPUBLICATIONS_NEW_PUBLICATION') }}</a>
            @endif
        </div>
    @endif

    <div role="tablist" class="tabs tabs-border mb-3">
        <a role="tab" class="tab tab-active"
            data-target="draftpublications{{ $module->id }}"
        >{{ Lang::txt('MOD_MYPUBLICATIONS_DRAFTS') }}</a>
        <a role="tab" class="tab"
            data-target="publishedpublications{{ $module->id }}"
        >{{ Lang::txt('MOD_MYPUBLICATIONS_PUBLISHED') }}</a>
    </div>

    <div id="draftpublications{{ $module->id }}">
        @if (count($drafts) > 0)
            <ul class="list bg-base-100 rounded-box">
                @foreach ($drafts as $item)
                    @include('modules.mod_mypublications.tmpl._item', ['item' => $item])
                @endforeach
            </ul>
        @else
            <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYPUBLICATIONS_NO_DRAFTS') }}</em></p>
        @endif
    </div>

    <div id="publishedpublications{{ $module->id }}" class="hidden">
        @if (count($published) > 0)
            <ul class="list bg-base-100 rounded-box">
                @foreach ($published as $item)
                    @include('modules.mod_mypublications.tmpl._item', ['item' => $item])
                @endforeach
            </ul>
        @else
            <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYPUBLICATIONS_NO_PUBLISHED') }}</em></p>
        @endif
    </div>
</div>
