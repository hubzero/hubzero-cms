{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if ($fav || $no_html)
    {!! $__module->buildList($favtools, 'fav') !!}
    <p class="text-sm text-base-content/60">{{ Lang::txt('MOD_MYTOOLS_EXPLANATION') }}</p>
@else
    @php
        $apiUrl = Route::url('index.php?option=com_members&id=' . User::get('id')
            . '&active=dashboard&no_html=1&init=1&action=$module&$moduleid=' . $module->id);
    @endphp
    <div id="myToolsTabs" data-api="{{ $apiUrl }}">
        <div role="tablist" class="tabs tabs-border mb-3">
            <a role="tab" class="tab tab-active" data-target="recenttools">{{ Lang::txt('MOD_MYTOOLS_RECENT') }}</a>
            <a role="tab" class="tab" data-target="favtools">{{ Lang::txt('MOD_MYTOOLS_FAVORITES') }}</a>
            <a role="tab" class="tab" data-target="alltools">{{ Lang::txt('MOD_MYTOOLS_ALL_TOOLS') }}</a>
        </div>

        <div id="recenttools">
            {!! $__module->buildList($rectools, 'recent') !!}
            <p class="text-sm text-base-content/60">{{ Lang::txt('MOD_MYTOOLS_RECENT_EXPLANATION') }}</p>
        </div>

        <div id="favtools" class="hidden">
            {!! $__module->buildList($favtools, 'favs') !!}
            <p class="text-sm text-base-content/60">{{ Lang::txt('MOD_MYTOOLS_FAVORITES_EXPLANATION') }}</p>
        </div>

        <div id="alltools" class="hidden">
            <div id="filter-mytools" class="mb-2">
                <input type="text"
                    class="input input-bordered input-sm w-full"
                    placeholder="{{ Lang::txt('MOD_MYTOOLS_SEARCH_PLACEHOLDER') }}"
                />
            </div>
            {!! $__module->buildList($alltools, 'all') !!}
            <p class="text-sm text-base-content/60">{{ Lang::txt('MOD_MYTOOLS_ALL_TOOLS_EXPLANATION') }}</p>
        </div>
    </div>
    <input type="hidden" class="mytools_favs" value="{{ implode(',', $favs) }}" />
@endif
