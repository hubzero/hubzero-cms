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
$badges_list = Event::trigger(
    'hubzero.onGetMultiEntry',
    [['tags', 'badges', 'actags1', '', implode(',', $b)]]
);

$backLink = Route::url('index.php?option=' . $__view->getName());
if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)) {
    $backLink = $_SERVER['HTTP_REFERER'];
}
@endphp

<div id="browsebox">
    @if ($__view->getError())
        <div class="alert alert-error">
            <p>{{ $__view->getError() }}</p>
        </div>
    @endif

    <form action="{{ Route::url($base . '?action=save') }}"
        method="post"
        id="hubForm"
        class="add-citation">

        {{-- Citation Details --}}
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_DETAILS') }}</h2>
                <p class="text-sm opacity-70 mb-4">{{ Lang::txt('PLG_GROUPS_CITATIONS_DETAILS_DESC') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="form-control" for="type">
                        <div class="label">
                            <span class="label-text">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_TYPE') }}
                                <span class="text-error">*</span>
                            </span>
                        </div>
                        <select name="type" id="type" class="select select-bordered w-full">
                            <option value="">{{ Lang::txt('PLG_GROUPS_CITATIONS_TYPE_SELECT') }}</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}"
                                    {{ $row->type == $t->id ? 'selected' : '' }}>
                                    {{ $t->type_title }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="form-control" for="cite">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_CITE_KEY') }}</span>
                        </div>
                        <input type="text"
                            name="cite"
                            id="cite"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->cite) }}" />
                        <div class="label">
                            <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_CITE_KEY_EXPLANATION') }}</span>
                        </div>
                    </label>
                </div>

                <label class="form-control" for="ref_type">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_REF_TYPE') }}</span>
                    </div>
                    <input type="text"
                        name="ref_type"
                        id="ref_type"
                        class="input input-bordered w-full"
                        maxlength="50"
                        value="{{ e($row->ref_type) }}" />
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="form-control" for="date_submit">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_DATE_SUBMITTED') }}</span>
                        </div>
                        <input type="text"
                            name="date_submit"
                            id="date_submit"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->date_submit) }}" />
                        <div class="label">
                            <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_DATE_HINT') }}</span>
                        </div>
                    </label>

                    <label class="form-control" for="date_accept">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_DATE_ACCEPTED') }}</span>
                        </div>
                        <input type="text"
                            name="date_accept"
                            id="date_accept"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->date_accept) }}" />
                        <div class="label">
                            <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_DATE_HINT') }}</span>
                        </div>
                    </label>

                    <label class="form-control" for="date_publish">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_DATE_PUBLISHED') }}</span>
                        </div>
                        <input type="text"
                            name="date_publish"
                            id="date_publish"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->date_publish) }}" />
                        <div class="label">
                            <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_DATE_HINT') }}</span>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="form-control" for="year">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_YEAR') }}</span>
                        </div>
                        <input type="text"
                            name="year"
                            id="year"
                            class="input input-bordered w-full"
                            maxlength="4"
                            value="{{ e($row->year) }}" />
                    </label>

                    <label class="form-control" for="month">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_MONTH') }}</span>
                        </div>
                        <input type="text"
                            name="month"
                            id="month"
                            class="input input-bordered w-full"
                            maxlength="50"
                            value="{{ e($row->month) }}" />
                    </label>
                </div>

                {{-- Author Manager --}}
                @php
                $citationId = $row->id;
                $token = Session::getFormToken();
                $authorBase = 'index.php?option=com_citations&controller=authors&citation=' . $citationId;
                $addUrl = Route::url($authorBase . '&task=add&' . $token . '=1');
                $updateUrl = Route::url($authorBase . '&task=update&' . $token . '=1');
                $listUrl = Route::url($authorBase . '&task=display&' . $token . '=1');
                @endphp
                <fieldset class="border border-base-300 rounded-lg p-4 mt-4 author-manager"
                    data-add="{{ $addUrl }}"
                    data-update="{{ $updateUrl }}"
                    data-list="{{ $listUrl }}">
                    <legend class="font-semibold px-2">{{ Lang::txt('PLG_GROUPS_CITATIONS_AUTHORS') }}</legend>

                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label for="field-author" class="form-control">
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
                        <button class="btn btn-primary add-author">
                            {{ Lang::txt('PLG_GROUPS_CITATIONS_ADD') }}
                        </button>
                    </div>

                    <div class="field-wrap author-list mt-3">
                        @if (isset($authors) && count($authors))
                            @foreach ($authors as $i => $author)
                                <div class="flex items-center gap-2 p-2 bg-base-200 rounded mb-1 citation-author"
                                    id="author_{{ e($author->id) }}">
                                    <span class="author-handle cursor-move opacity-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                                    </span>
                                    <span class="author-name flex-1">{{ e($author->author) }}</span>
                                    <span class="author-description">
                                        <input type="hidden"
                                            name="author[{{ $i }}][id]"
                                            value="{{ e($author->id) }}" />
                                        @php
                                        $removeUrl = Route::url(
                                            $authorBase . '&task=remove&author='
                                            . $author->id . '&' . $token . '=1'
                                        );
                                        @endphp
                                        <a class="btn btn-ghost btn-xs text-error delete"
                                            data-id="{{ e($author->id) }}"
                                            href="{{ $removeUrl }}">
                                            {{ Lang::txt('JDELETE') }}
                                        </a>
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm opacity-60 author-instructions"></p>
                        @endif
                    </div>
                </fieldset>

                <label class="form-control" for="authoraddress">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_AUTHOR_ADDRESS') }}</span>
                    </div>
                    <input type="text"
                        name="author_address"
                        id="authoraddress"
                        class="input input-bordered w-full"
                        value="{{ e($row->author_address) }}" />
                </label>

                <label class="form-control" for="editor">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_EDITORS') }}</span>
                    </div>
                    <input type="text"
                        name="editor"
                        id="editor"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->editor) }}" />
                    <div class="label">
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_AUTHORS_HINT') }}</span>
                    </div>
                </label>

                <label class="form-control" for="title">
                    <div class="label">
                        <span class="label-text">
                            {{ Lang::txt('PLG_GROUPS_CITATIONS_TITLE_CHAPTER') }}
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

                <label class="form-control" for="booktitle">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_BOOK_TITLE') }}</span>
                    </div>
                    <input type="text"
                        name="booktitle"
                        id="booktitle"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->booktitle) }}" />
                </label>

                <label class="form-control" for="shorttitle">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SHORT_TITLE') }}</span>
                    </div>
                    <input type="text"
                        name="short_title"
                        id="shorttitle"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->short_title) }}" />
                </label>

                <label class="form-control" for="journal">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_JOURNAL') }}</span>
                    </div>
                    <input type="text"
                        name="journal"
                        id="journal"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->journal) }}" />
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="form-control" for="volume">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_VOLUME') }}</span>
                        </div>
                        <input type="text"
                            name="volume"
                            id="volume"
                            class="input input-bordered w-full"
                            maxlength="11"
                            value="{{ e($row->volume) }}" />
                    </label>

                    <label class="form-control" for="number">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_ISSUE') }}</span>
                        </div>
                        <input type="text"
                            name="number"
                            id="number"
                            class="input input-bordered w-full"
                            maxlength="50"
                            value="{{ e($row->number) }}" />
                    </label>

                    <label class="form-control" for="pages">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_PAGES') }}</span>
                        </div>
                        <input type="text"
                            name="pages"
                            id="pages"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->pages) }}" />
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="form-control" for="isbn">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_ISBN') }}</span>
                        </div>
                        <input type="text"
                            name="isbn"
                            id="isbn"
                            class="input input-bordered w-full"
                            maxlength="50"
                            value="{{ e($row->isbn) }}" />
                    </label>

                    <label class="form-control" for="doi">
                        <div class="label">
                            <span class="label-text">
                                <abbr title="{{ Lang::txt('PLG_GROUPS_CITATIONS_DOI_FULL') }}">
                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_DOI') }}
                                </abbr>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="form-control" for="callnumber">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_CALL_NUMBER') }}</span>
                        </div>
                        <input type="text"
                            name="call_number"
                            id="callnumber"
                            class="input input-bordered w-full"
                            value="{{ e($row->call_number) }}" />
                    </label>

                    <label class="form-control" for="accessionnumber">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_ACCESSION_NUMBER') }}</span>
                        </div>
                        <input type="text"
                            name="accession_number"
                            id="accessionnumber"
                            class="input input-bordered w-full"
                            value="{{ e($row->accession_number) }}" />
                    </label>
                </div>

                <label class="form-control" for="series">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SERIES') }}</span>
                    </div>
                    <input type="text"
                        name="series"
                        id="series"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->series) }}" />
                </label>

                <label class="form-control" for="edition">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_EDITION') }}</span>
                    </div>
                    <input type="text"
                        name="edition"
                        id="edition"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->edition) }}" />
                    <div class="label">
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_EDITION_EXPLANATION') }}</span>
                    </div>
                </label>

                <label class="form-control" for="school">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SCHOOL') }}</span>
                    </div>
                    <input type="text"
                        name="school"
                        id="school"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->school) }}" />
                </label>

                <label class="form-control" for="publisher">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_PUBLISHER') }}</span>
                    </div>
                    <input type="text"
                        name="publisher"
                        id="publisher"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->publisher) }}" />
                </label>

                <label class="form-control" for="institution">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_INSTITUTION') }}</span>
                    </div>
                    <input type="text"
                        name="institution"
                        id="institution"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->institution) }}" />
                    <div class="label">
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_INSTITUTION_EXPLANATION') }}</span>
                    </div>
                </label>

                <label class="form-control" for="address">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_ADDRESS') }}</span>
                    </div>
                    <input type="text"
                        name="address"
                        id="address"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->address) }}" />
                </label>

                <label class="form-control" for="location">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_LOCATION') }}</span>
                    </div>
                    <input type="text"
                        name="location"
                        id="location"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->location) }}" />
                    <div class="label">
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_LOCATION_EXPLANATION') }}</span>
                    </div>
                </label>

                <label class="form-control" for="howpublished">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH_METHOD') }}</span>
                    </div>
                    <input type="text"
                        name="howpublished"
                        id="howpublished"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->howpublished) }}" />
                    <div class="label">
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH_METHOD_EXPLANATION') }}</span>
                    </div>
                </label>

                <label class="form-control" for="uri">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_URL') }}</span>
                    </div>
                    <input type="text"
                        name="uri"
                        id="uri"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->url) }}" />
                </label>

                <label class="form-control" for="eprint">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_EPRINT') }}</span>
                    </div>
                    <input type="text"
                        name="eprint"
                        id="eprint"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ e($row->eprint) }}" />
                    <div class="label">
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_EPRINT_EXPLANATION') }}</span>
                    </div>
                </label>

                <label class="form-control" for="abstract">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_ABSTRACT') }}</span>
                    </div>
                    <textarea name="abstract"
                        id="abstract"
                        class="textarea textarea-bordered w-full"
                        rows="6">{{ e(stripslashes($row->abstract)) }}</textarea>
                </label>

                <label class="form-control" for="note">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_NOTES') }}</span>
                    </div>
                    <textarea name="note"
                        id="note"
                        class="textarea textarea-bordered w-full"
                        rows="6">{{ e(stripslashes($row->note)) }}</textarea>
                </label>

                <label class="form-control" for="keywords">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_KEYWORDS') }}</span>
                    </div>
                    <textarea name="keywords"
                        id="keywords"
                        class="textarea textarea-bordered w-full"
                        rows="4">{{ e(stripslashes($row->keywords)) }}</textarea>
                </label>

                <label class="form-control" for="research_notes">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_RESEARCH_NOTES') }}</span>
                    </div>
                    <textarea name="research_notes"
                        id="research_notes"
                        class="textarea textarea-bordered w-full"
                        rows="4">{{ e(stripslashes($row->research_notes)) }}</textarea>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="form-control" for="language">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_LANGUAGE') }}</span>
                        </div>
                        <input type="text"
                            name="language"
                            id="language"
                            class="input input-bordered w-full"
                            maxlength="50"
                            value="{{ e($row->language) }}" />
                    </label>

                    <label class="form-control" for="label">
                        <div class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_LABEL') }}</span>
                        </div>
                        <input type="text"
                            name="label"
                            id="label"
                            class="input input-bordered w-full"
                            maxlength="250"
                            value="{{ e($row->label) }}" />
                    </label>
                </div>
            </div>
        </div>

        {{-- Tags & Badges --}}
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_TAGS') }}</h2>
                <p class="text-sm opacity-70 mb-4">{{ Lang::txt('PLG_GROUPS_CITATIONS_TAGS_EXPLAINATION') }}</p>

                <label class="form-control mb-3">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_TAGS') }}</span>
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_OPTIONAL') }}</span>
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
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_TAGS_HINT') }}</span>
                    </div>
                </label>

                <label class="form-control">
                    <div class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_BADGES') }}</span>
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_OPTIONAL') }}</span>
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
                        <span class="label-text-alt opacity-60">{{ Lang::txt('PLG_GROUPS_CITATIONS_BADGES_HINT') }}</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Links --}}
        <div class="card bg-base-200 mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_LINKS') }}</h2>
                <p class="text-sm opacity-70 mb-4">{{ Lang::txt('PLG_GROUPS_CITATIONS_LINKS_EXPLAINATION') }}</p>

                <div class="link-manager space-y-3">
                    @php
                    $i = 0;
                    $links = $row->links()->rows();
                    @endphp
                    @foreach ($links as $link)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 bg-base-300 rounded-lg">
                            <label class="form-control" for="links-{{ $i }}-title">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE') }}</span>
                                </div>
                                <input type="text"
                                    name="links[{{ $i }}][title]"
                                    id="links-{{ $i }}-title"
                                    class="input input-bordered w-full"
                                    value="{{ e($link->title) }}"
                                    placeholder="{{ Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE_PLACEHOLDER') }}" />
                            </label>
                            <label class="form-control" for="links-{{ $i }}-url">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_LINK_URL') }}</span>
                                </div>
                                <input type="text"
                                    name="links[{{ $i }}][url]"
                                    id="links-{{ $i }}-url"
                                    class="input input-bordered w-full"
                                    value="{{ e($link->url) }}"
                                    placeholder="http://" />
                                <input type="hidden" name="links[{{ $i }}][id]" value="{{ $link->id }}" />
                                <input type="hidden" name="links[{{ $i }}][citation_id]" value="{{ $link->citation_id }}" />
                            </label>
                        </div>
                        @php $i++; @endphp
                    @endforeach

                    {{-- Empty row for new link --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 bg-base-300 rounded-lg">
                        <label class="form-control" for="links-{{ $i }}-title">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE') }}</span>
                            </div>
                            <input type="text"
                                name="links[{{ $i }}][title]"
                                id="links-{{ $i }}-title"
                                class="input input-bordered w-full"
                                value=""
                                placeholder="{{ Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE_PLACEHOLDER') }}" />
                        </label>
                        <label class="form-control" for="links-{{ $i }}-url">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_LINK_URL') }}</span>
                            </div>
                            <input type="text"
                                name="links[{{ $i }}][url]"
                                id="links-{{ $i }}-url"
                                class="input input-bordered w-full"
                                value=""
                                placeholder="http://" />
                            <input type="hidden" name="links[{{ $i }}][id]" value="" />
                            <input type="hidden" name="links[{{ $i }}][citation_id]" value="{{ $row->id }}" />
                        </label>
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
        <input type="hidden" name="id" value="{{ $row->id }}" />
        <input type="hidden" name="option" value="com_groups" />
        <input type="hidden" name="active" value="citations" />
        <input type="hidden" name="action" value="save" />

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
