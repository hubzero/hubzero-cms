{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
$__view->js();

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=citations';
@endphp

<div id="browsebox">
    <h3 class="text-xl font-bold mb-4">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS') }}</h3>

    @if ($__view->getError())
        <div class="alert alert-error mb-4">
            <p>{{ $__view->getError() }}</p>
        </div>
    @endif

    <form action="{{ Route::url($base . '?action=settings') }}"
        method="post"
        id="hubForm"
        class="add-citation">

        {{-- Citation Sources --}}
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_SOURCES') }}</h2>
                <p class="text-sm opacity-70 mb-4">{{ Lang::txt('PLG_GROUPS_CITATIONS_SOURCE_EXPLAIN') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="form-control" for="display-sources">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SELECT_SOURCES') }}</span>
                        </div>
                        <select name="display" id="display-sources" class="select select-bordered w-full">
                            <option value="group">{{ Lang::txt('PLG_GROUPS_CITATIONS_DISPLAY_GROUPS') }}</option>
                            <option value="member">{{ Lang::txt('PLG_GROUPS_CITATIONS_DISPLAY_MEMBERS') }}</option>
                        </select>
                    </label>

                    <div class="text-sm opacity-70">
                        <p>{{ Lang::txt('PLG_GROUPS_CITATIONS_GROUP_ATTRIB') }}</p>
                        <p class="mt-2">{{ Lang::txt('PLG_GROUPS_CITATIONS_MEMBER_ATTRIB') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Badge and Tag options --}}
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_BADGE_OPTIONS') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="form-control" for="show_tags">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_DISPLAY_TAGS') }}</span>
                        </div>
                        <select name="citations_show_tags" id="show_tags" class="select select-bordered w-full">
                            <option value="yes" {{ $citations_show_tags == 'yes' ? 'selected' : '' }}>
                                {{ Lang::txt('Yes') }}
                            </option>
                            <option value="no" {{ $citations_show_tags == 'no' ? 'selected' : '' }}>
                                {{ Lang::txt('No') }}
                            </option>
                        </select>
                    </label>

                    <label class="form-control" for="show_badges">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_DISPLAY_BADGES') }}</span>
                        </div>
                        <select name="citations_show_badges" id="show_badges" class="select select-bordered w-full">
                            <option value="yes" {{ $citations_show_badges == 'yes' ? 'selected' : '' }}>
                                {{ Lang::txt('Yes') }}
                            </option>
                            <option value="no" {{ $citations_show_badges == 'no' ? 'selected' : '' }}>
                                {{ Lang::txt('No') }}
                            </option>
                        </select>
                    </label>
                </div>
            </div>
        </div>

        {{-- COinS options --}}
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_COINS_OPTIONS') }}</h2>
                <p class="text-sm opacity-70 mb-4">
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_WHAT_ARE_COINS') }}
                    <a href="http://ocoins.info/"
                        rel="nofollow external"
                        class="link link-hover link-primary">
                        {{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_READ_MORE_COINS') }}
                    </a>
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="form-control" for="include-coins">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_INCLUDE_COINS') }}</span>
                        </div>
                        <select name="include_coins" id="include-coins" class="select select-bordered w-full">
                            <option value="no" {{ $include_coins == 'no' ? 'selected' : '' }}>
                                {{ Lang::txt('No') }}
                            </option>
                            <option value="yes" {{ $include_coins == 'yes' ? 'selected' : '' }}>
                                {{ Lang::txt('Yes') }}
                            </option>
                        </select>
                    </label>

                    <label class="form-control" for="coins-only">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_COINS_ONLY') }}</span>
                        </div>
                        <select name="coins_only" id="coins-only" class="select select-bordered w-full">
                            <option value="no" {{ $coins_only == 'no' ? 'selected' : '' }}>
                                {{ Lang::txt('No') }}
                            </option>
                            <option value="yes" {{ $coins_only == 'yes' ? 'selected' : '' }}>
                                {{ Lang::txt('Yes') }}
                            </option>
                        </select>
                    </label>
                </div>
            </div>
        </div>

        {{-- Citation Format --}}
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_CITATION_FORMAT') }}</h2>
                <p class="text-sm opacity-70 mb-4">{{ Lang::txt('PLG_GROUPS_CITATIONS_FORMAT_EXPLAIN') }}</p>

                <label class="form-control mb-4" for="format-selector">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_CITATION_FORMAT') }}</span>
                    </div>
                    <select name="citation-format"
                        id="format-selector"
                        data-cn="{{ $group->get('cn') }}"
                        class="select select-bordered w-full">
                        @foreach ($formats as $format)
                            @if ($format->style != 'custom-group-' . $group->get('cn'))
                                <option value="{{ $format->id }}"
                                    data-format="{{ $format->format }}"
                                    {{ $currentFormat->id == $format->id ? 'selected' : '' }}>
                                    {{ $format->style }}
                                </option>
                            @elseif ($format->style == 'custom-group-' . $group->get('cn'))
                                <option value="{{ $format->id }}"
                                    data-format="{{ $format->format }}"
                                    {{ $currentFormat->id == $format->id ? 'selected' : '' }}>
                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_CUSTOM_FORMAT') }}
                                </option>
                            @endif
                        @endforeach
                        @if ($customFormat === false)
                            <option value="custom" data-format="">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_CUSTOM_FORMAT') }}
                            </option>
                        @endif
                    </select>
                    <div class="label">
                        <span class="label-text-alt opacity-60">
                            {{ Lang::txt('PLG_GROUPS_CITATIONS_SETTINGS_FORMAT_EXPLAINATION') }}
                        </span>
                    </div>
                </label>

                <label class="form-control mb-4" for="format-string">
                    <div class="label">
                        <span class="label-text">Format Template</span>
                    </div>
                    <textarea name="template"
                        rows="10"
                        id="format-string"
                        class="textarea textarea-bordered w-full font-mono text-sm">{{ $currentFormat->format }}</textarea>
                </label>

                {{-- Template keys reference --}}
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm templateTable">
                        <caption class="text-left text-sm opacity-70 mb-2">
                            {{ Lang::txt('PLG_GROUPS_CITATIONS_CLICK_TABLE') }}
                        </caption>
                        <thead>
                            <tr>
                                <th>{{ Lang::txt('Key') }}</th>
                                <th>{{ Lang::txt('Value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($templateKeys as $k => $v)
                                <tr id="{{ $v }}" class="cursor-pointer hover">
                                    <td class="font-mono text-sm">{{ $v }}</td>
                                    <td>{{ $k }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-2">
            <button type="submit" name="create" class="btn btn-primary">
                {{ Lang::txt('PLG_GROUPS_CITATIONS_SAVE') }}
            </button>
            <a href="{{ Route::url($base) }}" class="btn btn-ghost">
                {{ Lang::txt('JCANCEL') }}
            </a>
        </div>
    </form>
</div>
