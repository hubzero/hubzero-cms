{{--
  Citations — add / edit citation form.

  Variables from controller (editTask):
    $title     — Page title string
    $row       — Citation model (new or existing)
    $types     — Array of citation type arrays (with id, type, type_title)
    $assocs    — Array of Association objects
    $config    — Component configuration (Registry)
    $tags      — Array of tags (if citation_allow_tags)
    $badges    — Array of badges (if citation_allow_badges)
    $token     — CSRF form token

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_CITATIONS'),
          'index.php?option=' . $option
      );
  }
  if ($row->id && $row->id > 0) {
      Pathway::append(
          Illuminate\Support\Str::limit($row->title, 30),
          'index.php?option=' . $option . '&task=view&id=' . $row->id
      );
  }
  Pathway::append(
      Lang::txt('JACTION_EDIT'),
      'index.php?option=' . $option . '&task=edit&id=' . $row->id
  );

  Document::setTitle($title);

  $actionUrl = Route::url('index.php?option=' . $option, false);
  $cancelUrl = ($row->id && $row->id > 0)
      ? Route::url('index.php?option=' . $option . '&task=view&id=' . $row->id, false)
      : Route::url('index.php?option=' . $option, false);

  $pid = Request::getInt('publication', 0);

  $allowTags = $config->get('citation_allow_tags', 'no') == 'yes';
  $allowBadges = $config->get('citation_allow_badges', 'no') == 'yes';
@endphp

<x-page-container :title="$title" bodyClass="edit-form">
  @slot('actions')
    <a class="btn"
       href="{{ $cancelUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
      </svg>
      {{ Lang::txt('COM_CITATIONS_BACK') }}
    </a>
  @endslot

  <form action="{{ $actionUrl }}"
        method="post"
        id="hubForm">

    {{-- Citation Details --}}
    <x-form-section :heading="Lang::txt('COM_CITATIONS_DETAILS')">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="field-type"
                      :label="Lang::txt('COM_CITATIONS_TYPE')"
                      required>
          <select id="field-type"
                  name="fields[type]"
                  class="select w-full"
                  required>
            <option value="">{{ Lang::txt('COM_CITATIONS_TYPE_SELECT') }}</option>
            @foreach($types as $t)
              @if(!empty($t['id']))
                <option value="{{ $t['id'] }}"
                        {{ $row->type == $t['id'] ? 'selected' : '' }}>
                  {{ $t['type_title'] }}
                </option>
              @endif
            @endforeach
          </select>
        </x-form-field>

        <x-form-field name="field-cite"
                      :label="Lang::txt('COM_CITATIONS_CITE_KEY')"
                      :hint="Lang::txt('COM_CITATIONS_CITE_KEY_EXPLANATION')">
          <input type="text"
                 id="field-cite"
                 name="fields[cite]"
                 class="input w-full"
                 value="{{ $row->cite }}"
                 maxlength="250" />
        </x-form-field>
      </div>

      <x-form-field name="field-title"
                    :label="Lang::txt('COM_CITATIONS_TITLE_CHAPTER')"
                    required>
        <input type="text"
               id="field-title"
               name="fields[title]"
               class="input w-full"
               value="{{ $row->title }}"
               maxlength="250"
               required />
      </x-form-field>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-form-field name="field-year"
                      :label="Lang::txt('COM_CITATIONS_YEAR')">
          <input type="text"
                 id="field-year"
                 name="fields[year]"
                 class="input w-full"
                 value="{{ $row->year }}"
                 maxlength="4" />
        </x-form-field>

        <x-form-field name="field-month"
                      :label="Lang::txt('COM_CITATIONS_MONTH')">
          <input type="text"
                 id="field-month"
                 name="fields[month]"
                 class="input w-full"
                 value="{{ $row->month }}"
                 maxlength="50" />
        </x-form-field>

        <x-form-field name="field-ref-type"
                      :label="Lang::txt('COM_CITATIONS_REF_TYPE')">
          <input type="text"
                 id="field-ref-type"
                 name="fields[ref_type]"
                 class="input w-full"
                 value="{{ $row->ref_type }}"
                 maxlength="50" />
        </x-form-field>
      </div>

    </x-form-section>

    {{-- Authors --}}
    <x-form-section :heading="Lang::txt('COM_CITATIONS_AUTHORS')">

      <x-form-field name="field-author-address"
                    :label="Lang::txt('COM_CITATIONS_AUTHOR_ADDRESS')">
        <input type="text"
               id="field-author-address"
               name="fields[author_address]"
               class="input w-full"
               value="{{ $row->author_address }}" />
      </x-form-field>

      <x-form-field name="field-editor"
                    :label="Lang::txt('COM_CITATIONS_EDITORS')"
                    :hint="Lang::txt('COM_CITATIONS_AUTHORS_HINT')">
        <input type="text"
               id="field-editor"
               name="fields[editor]"
               class="input w-full"
               value="{{ $row->editor }}"
               maxlength="250" />
      </x-form-field>

    </x-form-section>

    {{-- Publication Details --}}
    <x-form-section :heading="Lang::txt('COM_CITATIONS_JOURNAL')">

      <x-form-field name="field-journal"
                    :label="Lang::txt('COM_CITATIONS_JOURNAL')">
        <input type="text"
               id="field-journal"
               name="fields[journal]"
               class="input w-full"
               value="{{ $row->journal }}"
               maxlength="250" />
      </x-form-field>

      <x-form-field name="field-booktitle"
                    :label="Lang::txt('COM_CITATIONS_BOOK_TITLE')">
        <input type="text"
               id="field-booktitle"
               name="fields[booktitle]"
               class="input w-full"
               value="{{ $row->booktitle }}"
               maxlength="250" />
      </x-form-field>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-form-field name="field-volume"
                      :label="Lang::txt('COM_CITATIONS_VOLUME')">
          <input type="text"
                 id="field-volume"
                 name="fields[volume]"
                 class="input w-full"
                 value="{{ $row->volume }}"
                 maxlength="11" />
        </x-form-field>

        <x-form-field name="field-number"
                      :label="Lang::txt('COM_CITATIONS_ISSUE')">
          <input type="text"
                 id="field-number"
                 name="fields[number]"
                 class="input w-full"
                 value="{{ $row->number }}"
                 maxlength="50" />
        </x-form-field>

        <x-form-field name="field-pages"
                      :label="Lang::txt('COM_CITATIONS_PAGES')">
          <input type="text"
                 id="field-pages"
                 name="fields[pages]"
                 class="input w-full"
                 value="{{ $row->pages }}"
                 maxlength="250" />
        </x-form-field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="field-isbn"
                      :label="Lang::txt('COM_CITATIONS_ISBN')">
          <input type="text"
                 id="field-isbn"
                 name="fields[isbn]"
                 class="input w-full"
                 value="{{ $row->isbn }}"
                 maxlength="50" />
        </x-form-field>

        <x-form-field name="field-doi"
                      label="DOI"
                      :hint="Lang::txt('COM_CITATIONS_DOI_FULL')">
          <input type="text"
                 id="field-doi"
                 name="fields[doi]"
                 class="input w-full"
                 value="{{ $row->doi }}"
                 maxlength="250" />
        </x-form-field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="field-series"
                      :label="Lang::txt('COM_CITATIONS_SERIES')">
          <input type="text"
                 id="field-series"
                 name="fields[series]"
                 class="input w-full"
                 value="{{ $row->series }}"
                 maxlength="250" />
        </x-form-field>

        <x-form-field name="field-edition"
                      :label="Lang::txt('COM_CITATIONS_EDITION')"
                      :hint="Lang::txt('COM_CITATIONS_EDITION_EXPLANATION')">
          <input type="text"
                 id="field-edition"
                 name="fields[edition]"
                 class="input w-full"
                 value="{{ $row->edition }}"
                 maxlength="250" />
        </x-form-field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="field-publisher"
                      :label="Lang::txt('COM_CITATIONS_PUBLISHER')">
          <input type="text"
                 id="field-publisher"
                 name="fields[publisher]"
                 class="input w-full"
                 value="{{ $row->publisher }}"
                 maxlength="250" />
        </x-form-field>

        <x-form-field name="field-school"
                      :label="Lang::txt('COM_CITATIONS_SCHOOL')">
          <input type="text"
                 id="field-school"
                 name="fields[school]"
                 class="input w-full"
                 value="{{ $row->school }}"
                 maxlength="250" />
        </x-form-field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="field-institution"
                      :label="Lang::txt('COM_CITATIONS_INSTITUTION')"
                      :hint="Lang::txt('COM_CITATIONS_INSTITUTION_EXPLANATION')">
          <input type="text"
                 id="field-institution"
                 name="fields[institution]"
                 class="input w-full"
                 value="{{ $row->institution }}"
                 maxlength="250" />
        </x-form-field>

        <x-form-field name="field-organization"
                      :label="Lang::txt('COM_CITATIONS_ORGANIZATION')">
          <input type="text"
                 id="field-organization"
                 name="fields[organization]"
                 class="input w-full"
                 value="{{ $row->organization }}"
                 maxlength="250" />
        </x-form-field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="field-address"
                      :label="Lang::txt('COM_CITATIONS_ADDRESS')">
          <input type="text"
                 id="field-address"
                 name="fields[address]"
                 class="input w-full"
                 value="{{ $row->address }}"
                 maxlength="250" />
        </x-form-field>

        <x-form-field name="field-location"
                      :label="Lang::txt('COM_CITATIONS_LOCATION')"
                      :hint="Lang::txt('COM_CITATIONS_LOCATION_EXPLANATION')">
          <input type="text"
                 id="field-location"
                 name="fields[location]"
                 class="input w-full"
                 value="{{ $row->location }}"
                 maxlength="250" />
        </x-form-field>
      </div>

    </x-form-section>

    {{-- URLs & Links --}}
    <x-form-section heading="Links">

      <x-form-field name="field-url"
                    :label="Lang::txt('COM_CITATIONS_URL')">
        <input type="url"
               id="field-url"
               name="fields[url]"
               class="input w-full"
               value="{{ $row->url }}"
               maxlength="250" />
      </x-form-field>

      <x-form-field name="field-eprint"
                    :label="Lang::txt('COM_CITATIONS_EPRINT')"
                    :hint="Lang::txt('COM_CITATIONS_EPRINT_EXPLANATION')">
        <input type="text"
               id="field-eprint"
               name="fields[eprint]"
               class="input w-full"
               value="{{ $row->eprint }}"
               maxlength="250" />
      </x-form-field>

      <x-form-field name="field-howpublished"
                    :label="Lang::txt('COM_CITATIONS_PUBLISH_METHOD')"
                    :hint="Lang::txt('COM_CITATIONS_PUBLISH_METHOD_EXPLANATION')">
        <input type="text"
               id="field-howpublished"
               name="fields[howpublished]"
               class="input w-full"
               value="{{ $row->howpublished }}"
               maxlength="250" />
      </x-form-field>

    </x-form-section>

    {{-- Content --}}
    <x-form-section :heading="Lang::txt('COM_CITATIONS_ABSTRACT')">

      <x-form-field name="field-abstract"
                    :label="Lang::txt('COM_CITATIONS_ABSTRACT')">
        <textarea id="field-abstract"
                  name="fields[abstract]"
                  class="textarea w-full"
                  rows="6">{{ $row->abstract ?? '' }}</textarea>
      </x-form-field>

      <x-form-field name="field-keywords"
                    :label="Lang::txt('COM_CITATIONS_KEYWORDS')">
        <textarea id="field-keywords"
                  name="fields[keywords]"
                  class="textarea w-full"
                  rows="3">{{ $row->keywords ?? '' }}</textarea>
      </x-form-field>

      <x-form-field name="field-note"
                    :label="Lang::txt('COM_CITATIONS_NOTES')">
        <textarea id="field-note"
                  name="fields[note]"
                  class="textarea w-full"
                  rows="4">{{ $row->note ?? '' }}</textarea>
      </x-form-field>

    </x-form-section>

    {{-- Dates --}}
    <x-form-section heading="Dates">

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-form-field name="field-date-submit"
                      :label="Lang::txt('COM_CITATIONS_DATE_SUBMITTED')"
                      :hint="Lang::txt('COM_CITATIONS_DATE_HINT')">
          <input type="text"
                 id="field-date-submit"
                 name="fields[date_submit]"
                 class="input w-full"
                 value="{{ $row->date_submit }}"
                 maxlength="250" />
        </x-form-field>

        <x-form-field name="field-date-accept"
                      :label="Lang::txt('COM_CITATIONS_DATE_ACCEPTED')"
                      :hint="Lang::txt('COM_CITATIONS_DATE_HINT')">
          <input type="text"
                 id="field-date-accept"
                 name="fields[date_accept]"
                 class="input w-full"
                 value="{{ $row->date_accept }}"
                 maxlength="250" />
        </x-form-field>

        <x-form-field name="field-date-publish"
                      :label="Lang::txt('COM_CITATIONS_DATE_PUBLISHED')"
                      :hint="Lang::txt('COM_CITATIONS_DATE_HINT')">
          <input type="text"
                 id="field-date-publish"
                 name="fields[date_publish]"
                 class="input w-full"
                 value="{{ $row->date_publish }}"
                 maxlength="250" />
        </x-form-field>
      </div>

    </x-form-section>

    {{-- Tags & Badges --}}
    @if($allowTags || $allowBadges)
      <x-form-section heading="{{ $allowTags && $allowBadges ? 'Tags and Badges' : ($allowTags ? 'Tags' : 'Badges') }}">
        @if($allowTags)
          <x-form-field name="field-tags"
                        :label="Lang::txt('COM_CITATIONS_TAGS')"
                        :hint="Lang::txt('COM_CITATIONS_TAGS_HINT')">
            <input type="text"
                   id="field-tags"
                   name="tags"
                   class="input w-full"
                   value="{{ is_array($tags) ? implode(', ', $tags) : $tags }}" />
          </x-form-field>
        @endif
        @if($allowBadges)
          <x-form-field name="field-badges"
                        :label="Lang::txt('COM_CITATIONS_BADGES')"
                        :hint="Lang::txt('COM_CITATIONS_BADGES_HINT')">
            <input type="text"
                   id="field-badges"
                   name="badges"
                   class="input w-full"
                   value="{{ is_array($badges) ? implode(', ', $badges) : $badges }}" />
          </x-form-field>
        @endif
      </x-form-section>
    @endif

    {{-- Associations --}}
    @if(!$pid)
      <x-form-section :heading="Lang::txt('COM_CITATIONS_CITATION_FOR')">
        <p class="text-sm text-base-content/60 mb-4">
          {{ Lang::txt('COM_CITATIONS_ASSOCIATION_DESC') }}
        </p>
        @php
          $numAssocs = max(count($assocs), 3);
        @endphp
        @for($i = 0; $i < $numAssocs; $i++)
          @php
            $a = isset($assocs[$i]) ? $assocs[$i] : (object)[
                'id' => null, 'cid' => $row->id,
                'oid' => null, 'type' => null, 'tbl' => null
            ];
          @endphp
          <div class="grid grid-cols-3 gap-2 mb-2">
            <select name="assocs[{{ $i }}][tbl]" class="select select-sm w-full">
              <option value="">{{ Lang::txt('COM_CITATIONS_SELECT') }}</option>
              <option value="resource" {{ ($a->tbl ?? '') == 'resource' ? 'selected' : '' }}>
                {{ Lang::txt('COM_CITATIONS_RESOURCE') }}
              </option>
              <option value="publication" {{ ($a->tbl ?? '') == 'publication' ? 'selected' : '' }}>
                {{ Lang::txt('COM_CITATIONS_PUBLICATION') }}
              </option>
            </select>
            <input type="text"
                   name="assocs[{{ $i }}][oid]"
                   class="input input-sm w-full"
                   value="{{ $a->oid }}"
                   placeholder="ID" />
            <select name="assocs[{{ $i }}][type]" class="select select-sm w-full">
              <option value="">{{ Lang::txt('COM_CITATIONS_SELECT') }}</option>
              <option value="references" {{ ($a->type ?? '') == 'references' ? 'selected' : '' }}>
                {{ Lang::txt('COM_CITATIONS_CONTEXT_REFERENCES') }}
              </option>
              <option value="referencedby" {{ ($a->type ?? '') == 'referencedby' ? 'selected' : '' }}>
                {{ Lang::txt('COM_CITATIONS_CONTEXT_REFERENCEDBY') }}
              </option>
            </select>
            <input type="hidden"
                   name="assocs[{{ $i }}][id]"
                   value="{{ $a->id }}" />
            <input type="hidden"
                   name="assocs[{{ $i }}][cid]"
                   value="{{ $a->cid ?? $row->id }}" />
          </div>
        @endfor
      </x-form-section>
    @else
      <input type="hidden" name="assocs[0][oid]" value="{{ $pid }}" />
      <input type="hidden" name="assocs[0][tbl]" value="publication" />
      <input type="hidden" name="assocs[0][id]" value="0" />
    @endif

    {{-- Affiliation --}}
    <x-form-section :heading="Lang::txt('COM_CITATIONS_AFFILIATION')">
      <label class="checkbox-label">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="fields[affiliated]"
               value="1"
               {{ $row->affiliated ? 'checked' : '' }} />
        {{ Lang::txt('COM_CITATIONS_AFFILIATED_WITH_YOUR_ORG') }}
      </label>
      <label class="checkbox-label">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="fields[fundedby]"
               value="1"
               {{ $row->fundedby ? 'checked' : '' }} />
        {{ Lang::txt('COM_CITATIONS_FUNDED_BY_YOUR_ORG') }}
      </label>
    </x-form-section>

    {{-- Hidden fields --}}
    <input type="hidden" name="fields[uid]" value="{{ $row->uid }}" />
    <input type="hidden" name="fields[created]" value="{{ $row->created }}" />
    <input type="hidden" name="fields[scope]" value="{{ $row->scope }}" />
    <input type="hidden" name="fields[scope_id]" value="{{ $row->scope_id }}" />
    @php $pubVal = ($row->id) ? $row->published : 1; @endphp
    <input type="hidden" name="fields[published]" value="{{ $pubVal }}" />
    <input type="hidden" name="id" value="{{ $row->id }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="task" value="save" />
    {!! Html::input('token') !!}

    {{-- Form actions --}}
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">
        {{ Lang::txt('COM_CITATIONS_SAVE') }}
      </button>
      <a class="btn btn-ghost" href="{{ $cancelUrl }}">
        {{ Lang::txt('JCANCEL') }}
      </a>
    </div>
  </form>

</x-page-container>
