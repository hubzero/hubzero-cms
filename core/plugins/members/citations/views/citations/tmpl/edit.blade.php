{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css()->js();
$base = 'index.php?option=com_members&id=' . $member->get('id') . '&active=citations';

$allow_tags = 'no';
$allow_badges = 'no';

$t = [];
$b = [];
foreach ($tags as $tag) {
    $t[] = $tag;
}
foreach ($badges as $badge) {
    $b[] = $badge;
}

$tags_list = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', implode(',', $t)]]);
$badges_list = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'badges', 'actags1', '', implode(',', $b)]]);

$backLink = Route::url('index.php?option=' . $_name);
if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)) {
    $backLink = $_SERVER['HTTP_REFERER'];
}
@endphp

<div id="browsebox">
    @if ($__view->getError())
        <div class="alert alert-error">{{ $__view->getError() }}</div>
    @endif

    <form action="{{ Route::url($base . '?action=save') }}" method="post" id="hubForm" class="add-citation">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3">
                <fieldset class="space-y-6">
                    <legend class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_CITATIONS_DETAILS') }}</legend>

                    {{-- Type / Cite key --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="form-control w-full" for="type">
                            <div class="label">
                                <span class="label-text">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_TYPE') }}:
                                    <span class="text-error">*</span>
                                </span>
                            </div>
                            <select name="type" id="type" class="select select-bordered w-full">
                                <option value="">{{ Lang::txt('PLG_MEMBERS_CITATIONS_TYPE_SELECT') }}</option>
                                @foreach ($types as $tp)
                                    <option value="{{ $tp->id }}" {{ $row->type == $tp->id ? 'selected' : '' }}>
                                        {{ $tp->type_title }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="form-control w-full" for="cite">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_CITE_KEY') }}:</span>
                            </div>
                            <input type="text"
                                name="cite"
                                id="cite"
                                class="input input-bordered w-full"
                                maxlength="250"
                                value="{{ e($row->cite) }}" />
                            <div class="label">
                                <span class="label-text-alt text-base-content/50">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_CITE_KEY_EXPLANATION') }}
                                </span>
                            </div>
                        </label>
                    </div>

                    {{-- Ref type --}}
                    <label class="form-control w-full" for="ref_type">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_REF_TYPE') }}:</span>
                        </div>
                        <input type="text"
                            name="ref_type"
                            id="ref_type"
                            class="input input-bordered w-full"
                            maxlength="50"
                            value="{{ e($row->ref_type) }}" />
                    </label>

                    {{-- Dates --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="form-control w-full" for="date_submit">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_DATE_SUBMITTED') }}:</span>
                            </div>
                            <input type="text"
                                name="date_submit"
                                id="date_submit"
                                class="input input-bordered w-full"
                                maxlength="250"
                                value="{{ e($row->date_submit) }}" />
                            <div class="label">
                                <span class="label-text-alt text-base-content/50">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_DATE_HINT') }}
                                </span>
                            </div>
                        </label>

                        <label class="form-control w-full" for="date_accept">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_DATE_ACCEPTED') }}:</span>
                            </div>
                            <input type="text"
                                name="date_accept"
                                id="date_accept"
                                class="input input-bordered w-full"
                                maxlength="250"
                                value="{{ e($row->date_accept) }}" />
                            <div class="label">
                                <span class="label-text-alt text-base-content/50">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_DATE_HINT') }}
                                </span>
                            </div>
                        </label>

                        <label class="form-control w-full" for="date_publish">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_DATE_PUBLISHED') }}:</span>
                            </div>
                            <input type="text"
                                name="date_publish"
                                id="date_publish"
                                class="input input-bordered w-full"
                                maxlength="250"
                                value="{{ e($row->date_publish) }}" />
                            <div class="label">
                                <span class="label-text-alt text-base-content/50">
                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_DATE_HINT') }}
                                </span>
                            </div>
                        </label>
                    </div>

                    {{-- Year / Month --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="form-control w-full" for="year">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_YEAR') }}:</span>
                            </div>
                            <input type="text"
                                name="year"
                                id="year"
                                class="input input-bordered w-full"
                                maxlength="4"
                                value="{{ e($row->year) }}" />
                        </label>

                        <label class="form-control w-full" for="month">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_MONTH') }}:</span>
                            </div>
                            <input type="text"
                                name="month"
                                id="month"
                                class="input input-bordered w-full"
                                maxlength="50"
                                value="{{ e($row->month) }}" />
                        </label>
                    </div>

                    {{-- Author manager --}}
                    @php
                        $citationId = $row->id;
                        $token = Session::getFormToken();
                        $authorBase = 'index.php?option=com_citations&controller=authors&citation=' . $citationId;
                        $addAuthorUrl = Route::url($authorBase . '&task=add&' . $token . '=1');
                        $updateAuthorUrl = Route::url($authorBase . '&task=update&' . $token . '=1');
                        $listAuthorUrl = Route::url($authorBase . '&task=display&' . $token . '=1');
                    @endphp
                    <fieldset class="author-manager border border-base-300 rounded-lg p-4"
                        data-add="{{ $addAuthorUrl }}"
                        data-update="{{ $updateAuthorUrl }}"
                        data-list="{{ $listAuthorUrl }}">
                        <div class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label class="form-control w-full" for="field-author">
                                    <div class="label">
                                        <span class="label-text">
                                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_AUTHORS') }}
                                            <span class="text-error">*</span>
                                        </span>
                                    </div>
                                    @php
                                        $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'author', 'field-author', '', (isset($authorString) ? $authorString : '')]]);
                                    @endphp
                                    @if (count($mc) > 0)
                                        {!! $mc[0] !!}
                                    @else
                                        <input type="text"
                                            name="author"
                                            id="field-author"
                                            class="input input-bordered w-full"
                                            value="" />
                                    @endif
                                </label>
                            </div>
                            <button class="btn btn-primary btn-sm add-author">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_ADD') }}
                            </button>
                        </div>

                        <div class="author-list mt-4 space-y-2">
                            @if (isset($authors) && count($authors))
                                @foreach ($authors as $i => $author)
                                    <div class="citation-author flex items-center gap-2 p-2 bg-base-200 rounded"
                                        id="author_{{ e($author->id) }}">
                                        <span class="author-handle cursor-move">&#8597;</span>
                                        <span class="author-name flex-1">{{ e($author->author) }}</span>
                                        <span class="author-description">
                                            <input type="hidden"
                                                name="author[{{ $i }}][id]"
                                                value="{{ e($author->id) }}" />
                                            @php
                                                $removeUrl = Route::url(
                                                    $authorBase . '&task=remove&author=' . $author->id . '&' . $token . '=1'
                                                );
                                            @endphp
                                            <a class="btn btn-ghost btn-xs text-error delete"
                                                data-confirm="{{ Lang::txt('PLG_MEMBERS_CITATIONS_CONFIRM_DELETE') }}"
                                                data-id="{{ e($author->id) }}"
                                                href="{{ $removeUrl }}">
                                                {{ Lang::txt('JDELETE') }}
                                            </a>
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <p class="author-instructions text-base-content/50 text-sm"></p>
                            @endif
                        </div>
                    </fieldset>

                    {{-- Author address --}}
                    <label class="form-control w-full" for="authoraddress">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_AUTHOR_ADDRESS') }}:</span>
                        </div>
                        <input type="text"
                            name="author_address"
                            id="authoraddress"
                            class="input input-bordered w-full"
                            value="{{ e($row->author_address) }}" />
                    </label>

                    {{-- Editor --}}
                    <label class="form-control w-full" for="editor">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_EDITORS') }}:</span>
                        </div>
                        <input type="text"
                            name="editor"
                            id="editor"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->editor) }}" />
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_AUTHORS_HINT') }}
                            </span>
                        </div>
                    </label>

                    {{-- Title (required) --}}
                    <label class="form-control w-full" for="title">
                        <div class="label">
                            <span class="label-text">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_TITLE_CHAPTER') }}:
                                <span class="text-error">*</span>
                            </span>
                        </div>
                        <input type="text"
                            name="title"
                            id="title"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->title) }}" />
                    </label>

                    {{-- Book title --}}
                    <label class="form-control w-full" for="booktitle">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_BOOK_TITLE') }}:</span>
                        </div>
                        <input type="text"
                            name="booktitle"
                            id="booktitle"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->booktitle) }}" />
                    </label>

                    {{-- Short title --}}
                    <label class="form-control w-full" for="shorttitle">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_SHORT_TITLE') }}:</span>
                        </div>
                        <input type="text"
                            name="short_title"
                            id="shorttitle"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->short_title) }}" />
                    </label>

                    {{-- Journal --}}
                    <label class="form-control w-full" for="journal">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_JOURNAL') }}:</span>
                        </div>
                        <input type="text"
                            name="journal"
                            id="journal"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->journal) }}" />
                    </label>

                    {{-- Volume / Issue / Pages --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="form-control w-full" for="volume">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_VOLUME') }}:</span>
                            </div>
                            <input type="text"
                                name="volume"
                                id="volume"
                                class="input input-bordered w-full"
                                maxlength="11"
                                value="{{ e($row->volume) }}" />
                        </label>

                        <label class="form-control w-full" for="number">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_ISSUE') }}:</span>
                            </div>
                            <input type="text"
                                name="number"
                                id="number"
                                class="input input-bordered w-full"
                                maxlength="50"
                                value="{{ e($row->number) }}" />
                        </label>

                        <label class="form-control w-full" for="pages">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_PAGES') }}:</span>
                            </div>
                            <input type="text"
                                name="pages"
                                id="pages"
                                class="input input-bordered w-full"
                                maxlength="250"
                                value="{{ e($row->pages) }}" />
                        </label>
                    </div>

                    {{-- ISBN / DOI --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="form-control w-full" for="isbn">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_ISBN') }}:</span>
                            </div>
                            <input type="text"
                                name="isbn"
                                id="isbn"
                                class="input input-bordered w-full"
                                maxlength="50"
                                value="{{ e($row->isbn) }}" />
                        </label>

                        <label class="form-control w-full" for="doi">
                            <div class="label">
                                <span class="label-text">
                                    <abbr title="{{ Lang::txt('PLG_MEMBERS_CITATIONS_DOI_FULL') }}">
                                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_DOI') }}
                                    </abbr>:
                                </span>
                            </div>
                            <input type="text"
                                name="doi"
                                id="doi"
                                class="input input-bordered w-full"
                                maxlength="250"
                                value="{{ e($row->doi) }}" />
                        </label>
                    </div>

                    {{-- Call number / Accession number --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="form-control w-full" for="callnumber">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_CALL_NUMBER') }}:</span>
                            </div>
                            <input type="text"
                                name="call_number"
                                id="callnumber"
                                class="input input-bordered w-full"
                                value="{{ e($row->call_number) }}" />
                        </label>

                        <label class="form-control w-full" for="accessionnumber">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_ACCESSION_NUMBER') }}:</span>
                            </div>
                            <input type="text"
                                name="accession_number"
                                id="accessionnumber"
                                class="input input-bordered w-full"
                                value="{{ e($row->accession_number) }}" />
                        </label>
                    </div>

                    {{-- Series --}}
                    <label class="form-control w-full" for="series">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_SERIES') }}:</span>
                        </div>
                        <input type="text"
                            name="series"
                            id="series"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->series) }}" />
                    </label>

                    {{-- Edition --}}
                    <label class="form-control w-full" for="edition">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_EDITION') }}:</span>
                        </div>
                        <input type="text"
                            name="edition"
                            id="edition"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->edition) }}" />
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_EDITION_EXPLANATION') }}
                            </span>
                        </div>
                    </label>

                    {{-- School --}}
                    <label class="form-control w-full" for="school">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_SCHOOL') }}:</span>
                        </div>
                        <input type="text"
                            name="school"
                            id="school"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->school) }}" />
                    </label>

                    {{-- Publisher --}}
                    <label class="form-control w-full" for="publisher">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISHER') }}:</span>
                        </div>
                        <input type="text"
                            name="publisher"
                            id="publisher"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->publisher) }}" />
                    </label>

                    {{-- Institution --}}
                    <label class="form-control w-full" for="institution">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_INSTITUTION') }}:</span>
                        </div>
                        <input type="text"
                            name="institution"
                            id="institution"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->institution) }}" />
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_INSTITUTION_EXPLANATION') }}
                            </span>
                        </div>
                    </label>

                    {{-- Address --}}
                    <label class="form-control w-full" for="address">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_ADDRESS') }}:</span>
                        </div>
                        <input type="text"
                            name="address"
                            id="address"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->address) }}" />
                    </label>

                    {{-- Location --}}
                    <label class="form-control w-full" for="location">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_LOCATION') }}:</span>
                        </div>
                        <input type="text"
                            name="location"
                            id="location"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->location) }}" />
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_LOCATION_EXPLANATION') }}
                            </span>
                        </div>
                    </label>

                    {{-- How published --}}
                    <label class="form-control w-full" for="howpublished">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISH_METHOD') }}:</span>
                        </div>
                        <input type="text"
                            name="howpublished"
                            id="howpublished"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->howpublished) }}" />
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISH_METHOD_EXPLANATION') }}
                            </span>
                        </div>
                    </label>

                    {{-- URL --}}
                    <label class="form-control w-full" for="uri">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_URL') }}:</span>
                        </div>
                        <input type="text"
                            name="uri"
                            id="uri"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->url) }}" />
                    </label>

                    {{-- Eprint --}}
                    <label class="form-control w-full" for="eprint">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_EPRINT') }}:</span>
                        </div>
                        <input type="text"
                            name="eprint"
                            id="eprint"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->eprint) }}" />
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_EPRINT_EXPLANATION') }}
                            </span>
                        </div>
                    </label>

                    {{-- Abstract --}}
                    <label class="form-control w-full" for="abstract">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_ABSTRACT') }}:</span>
                        </div>
                        <textarea name="abstract"
                            id="abstract"
                            class="textarea textarea-bordered w-full"
                            rows="8">{{ e(stripslashes($row->abstract)) }}</textarea>
                    </label>

                    {{-- Note --}}
                    <label class="form-control w-full" for="note">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_NOTES') }}:</span>
                        </div>
                        <textarea name="note"
                            id="note"
                            class="textarea textarea-bordered w-full"
                            rows="8">{{ e(stripslashes($row->note)) }}</textarea>
                    </label>

                    {{-- Keywords --}}
                    <label class="form-control w-full" for="keywords">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_KEYWORDS') }}:</span>
                        </div>
                        <textarea name="keywords"
                            id="keywords"
                            class="textarea textarea-bordered w-full"
                            rows="8">{{ e(stripslashes($row->keywords)) }}</textarea>
                    </label>

                    {{-- Research notes --}}
                    <label class="form-control w-full" for="research_notes">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_RESEARCH_NOTES') }}:</span>
                        </div>
                        <textarea name="research_notes"
                            id="research_notes"
                            class="textarea textarea-bordered w-full"
                            rows="8">{{ e(stripslashes($row->research_notes)) }}</textarea>
                    </label>

                    {{-- Language / Label --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="form-control w-full" for="language">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_LANGUAGE') }}:</span>
                            </div>
                            <input type="text"
                                name="language"
                                id="language"
                                class="input input-bordered w-full"
                                maxlength="50"
                                value="{{ e($row->language) }}" />
                        </label>

                        <label class="form-control w-full" for="label">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_LABEL') }}:</span>
                            </div>
                            <input type="text"
                                name="label"
                                id="label"
                                class="input input-bordered w-full"
                                maxlength="250"
                                value="{{ e($row->label) }}" />
                        </label>
                    </div>
                </fieldset>
            </div>

            {{-- Sidebar explanation --}}
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-sm sticky top-4">
                    <div class="card-body text-sm text-base-content/70">
                        <p>{{ Lang::txt('PLG_MEMBERS_CITATIONS_DETAILS_DESC') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tags and Badges fieldset --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
            <div class="lg:col-span-3">
                <fieldset class="space-y-4">
                    <legend class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_CITATIONS_TAGS') }}</legend>

                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_TAGS') }}:</span>
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_OPTIONAL') }}
                            </span>
                        </div>
                        @if (count($tags_list) > 0)
                            {!! $tags_list[0] !!}
                        @else
                            <input type="text"
                                name="tags"
                                class="input input-bordered w-full"
                                value="{{ e(implode(',', $t)) }}" />
                        @endif
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_TAGS_HINT') }}
                            </span>
                        </div>
                    </label>

                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_BADGES') }}:</span>
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_OPTIONAL') }}
                            </span>
                        </div>
                        @if (count($badges_list) > 0)
                            {!! $badges_list[0] !!}
                        @else
                            <input type="text"
                                name="badges"
                                class="input input-bordered w-full"
                                value="{{ e(implode(',', $b)) }}" />
                        @endif
                        <div class="label">
                            <span class="label-text-alt text-base-content/50">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_BADGES_HINT') }}
                            </span>
                        </div>
                    </label>
                </fieldset>
            </div>

            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body text-sm text-base-content/70">
                        <p>{{ Lang::txt('PLG_MEMBERS_CITATIONS_TAGS_EXPLAINATION') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden fields --}}
        <input type="hidden" name="scope" value="{{ e($row->scope) }}" />
        <input type="hidden" name="scope_id" value="{{ e($row->scope_id) }}" />
        <input type="hidden" name="published" value="{{ $row->id ? e($row->published) : 1 }}" />
        <input type="hidden" name="uid" value="{{ $row->uid }}" />
        <input type="hidden" name="created" value="{{ $row->created }}" />
        <input type="hidden" name="cid" value="{{ $row->id }}" />
        <input type="hidden" name="id" value="{{ $member->get('id') }}" />
        <input type="hidden" name="option" value="com_members" />
        <input type="hidden" name="active" value="citations" />
        <input type="hidden" name="action" value="save" />

        <div class="mt-6">
            <button type="submit" name="create" class="btn btn-primary">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_SAVE') }}
            </button>
        </div>
    </form>
</div>
