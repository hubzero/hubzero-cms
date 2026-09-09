{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css()->js();
$base = Route::url($member->link() . '&active=' . $_name);
@endphp

<div id="browsebox">
    <h3>{{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS') }}</h3>

    @if ($__view->getError())
        <div class="alert alert-error">{{ $__view->getError() }}</div>
    @endif

    <form action="{{ Route::url($base . '?action=settings') }}"
        method="post"
        id="hubForm"
        class="add-citation space-y-8">

        {{-- Badge and Tag options --}}
        <fieldset>
            <legend class="text-lg font-semibold mb-4">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_BADGE_OPTIONS') }}
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="form-control w-full" for="show_tags">
                    <div class="label">
                        <span class="label-text">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DISPLAY_TAGS') }}
                        </span>
                    </div>
                    <select name="citations_show_tags"
                        id="show_tags"
                        class="select select-bordered w-full">
                        <option value="yes" {{ $citations_show_tags == 'yes' ? 'selected' : '' }}>
                            {{ Lang::txt('Yes') }}
                        </option>
                        <option value="no" {{ $citations_show_tags == 'no' ? 'selected' : '' }}>
                            {{ Lang::txt('No') }}
                        </option>
                    </select>
                </label>

                <label class="form-control w-full" for="show_badges">
                    <div class="label">
                        <span class="label-text">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DISPLAY_BADGES') }}
                        </span>
                    </div>
                    <select name="citations_show_badges"
                        id="show_badges"
                        class="select select-bordered w-full">
                        <option value="yes" {{ $citations_show_badges == 'yes' ? 'selected' : '' }}>
                            {{ Lang::txt('Yes') }}
                        </option>
                        <option value="no" {{ $citations_show_badges == 'no' ? 'selected' : '' }}>
                            {{ Lang::txt('No') }}
                        </option>
                    </select>
                </label>
            </div>
        </fieldset>

        {{-- COinS options --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3">
                <fieldset>
                    <legend class="text-lg font-semibold mb-4">
                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_COINS_OPTIONS') }}
                    </legend>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="form-control w-full" for="include-coins">
                            <div class="label">
                                <span class="label-text">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_INCLUDE_COINS') }}
                                </span>
                            </div>
                            <select name="include_coins"
                                id="include-coins"
                                class="select select-bordered w-full">
                                <option value="no" {{ $include_coins == 'no' ? 'selected' : '' }}>
                                    {{ Lang::txt('No') }}
                                </option>
                                <option value="yes" {{ $include_coins == 'yes' ? 'selected' : '' }}>
                                    {{ Lang::txt('Yes') }}
                                </option>
                            </select>
                        </label>

                        <label class="form-control w-full" for="coins-only">
                            <div class="label">
                                <span class="label-text">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_COINS_ONLY') }}
                                </span>
                            </div>
                            <select name="coins_only"
                                id="coins-only"
                                class="select select-bordered w-full">
                                <option value="no" {{ $coins_only == 'no' ? 'selected' : '' }}>
                                    {{ Lang::txt('No') }}
                                </option>
                                <option value="yes" {{ $coins_only == 'yes' ? 'selected' : '' }}>
                                    {{ Lang::txt('Yes') }}
                                </option>
                            </select>
                        </label>
                    </div>
                </fieldset>
            </div>
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body text-sm text-base-content/70">
                        <p>
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_WHAT_ARE_COINS') }}
                        </p>
                        <a href="http://ocoins.info/"
                            rel="nofollow external"
                            class="link link-primary">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_READ_MORE_COINS') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Citation format --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3">
                <fieldset class="space-y-4">
                    <legend class="text-lg font-semibold mb-4">
                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_FORMAT') }}
                    </legend>

                    <label class="form-control w-full" for="format-selector">
                        <div class="label">
                            <span class="label-text">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_FORMAT') }}:
                            </span>
                        </div>
                        @php
                            $memberId = $member->get('id');
                            $customStyle = 'custom-member-' . $memberId;
                            $customLabel = Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_CUSTOM_FORMAT');
                        @endphp
                        <select name="citation-format"
                            id="format-selector"
                            class="select select-bordered w-full"
                            data-uid="{{ $memberId }}">
                            @foreach ($formats as $format)
                                @if ($format->style != $customStyle)
                                    <option value="{{ $format->id }}"
                                        data-format="{{ $format->format }}"
                                        {{ $currentFormat->id == $format->id ? 'selected' : '' }}>
                                        {{ $format->style }}
                                    </option>
                                @elseif ($format->style == $customStyle)
                                    <option value="{{ $format->id }}"
                                        data-format="{{ $format->format }}"
                                        {{ $currentFormat->id == $format->id ? 'selected' : '' }}>
                                        {{ $customLabel }}
                                    </option>
                                @endif
                            @endforeach
                            @if ($customFormat === false)
                                <option value="custom" data-format="">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_CUSTOM_FORMAT') }}
                                </option>
                            @endif
                        </select>
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_FORMAT_EXPLAINATION') }}
                            </span>
                        </div>
                    </label>

                    <label class="form-control w-full" for="format-string">
                        <textarea name="template"
                            rows="10"
                            id="format-string"
                            class="textarea textarea-bordered w-full font-mono text-sm">{{ addslashes($currentFormat->format) }}</textarea>
                    </label>

                    {{-- Template key reference table --}}
                    <div class="overflow-x-auto">
                        <table class="table table-compact w-full">
                            <caption class="text-sm text-base-content/70 mb-2">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_CLICK_TABLE') }}
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
                </fieldset>
            </div>
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body text-sm text-base-content/70">
                        <p>{{ Lang::txt('PLG_MEMBERS_CITATIONS_FORMAT_EXPLAIN') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-6">
            <button type="submit" name="create" class="btn btn-primary">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_SAVE') }}
            </button>
        </div>
    </form>
</div>
