{{--
  Citation — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Citations\Helpers\Permissions::getActions('citation');
  $text  = $row->id ? Lang::txt('EDIT') : Lang::txt('NEW');

  Toolbar::title(Lang::txt('CITATION') . ': ' . $text, 'citation');
  if ($canDo->get('core.edit')) {
      Toolbar::save();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('citation');


  $__view->js();

  $editorOpts = ['class' => 'minimal no-footer', 'buttons' => false];

  // Decode fields for display
  $author      = html_entity_decode($row->getAuthorString() ?? '');
  $ceditor     = html_entity_decode($row->editor ?? '');
  $titleVal    = html_entity_decode($row->title ?? '');
  $booktitle   = html_entity_decode($row->booktitle ?? '');
  $short_title = html_entity_decode($row->short_title ?? '');
  $journal     = html_entity_decode($row->journal ?? '');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('DETAILS') }}">

      {{-- Type --}}
      <div class="admin-field">
        <label for="type" class="label">{{ Lang::txt('TYPE') }}</label>
        <select name="citation[type]" id="type" class="select select-bordered w-full">
          @foreach($types as $t)
            <option value="{{ $t['id'] }}" @selected($t['id'] == $row->type)>
              {{ $t['type_title'] ?? '' }} ({{ $t['type'] ?? '' }})
            </option>
          @endforeach
        </select>
      </div>

      {{-- Cite Key / Ref Type --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="cite" class="label">{{ Lang::txt('CITE_KEY') }}</label>
          <input type="text" name="citation[cite]" id="cite"
                 class="input input-bordered w-full" maxlength="250"
                 value="{{ $row->get('cite', '') }}" />
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('CITE_KEY_EXPLANATION') }}</p>
        </div>
        <div class="admin-field">
          <label for="ref_type" class="label">{{ Lang::txt('REF_TYPE') }}</label>
          <input type="text" name="citation[ref_type]" id="ref_type"
                 class="input input-bordered w-full" maxlength="50"
                 value="{{ $row->get('ref_type', '') }}" />
        </div>
      </div>

      {{-- Dates --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="date_submit" class="label">{{ Lang::txt('DATE_SUBMITTED') }}</label>
          <input type="text" name="citation[date_submit]" id="date_submit"
                 class="input input-bordered w-full" maxlength="250"
                 value="{{ $row->get('date_submit', '') }}" />
        </div>
        <div class="admin-field">
          <label for="date_accept" class="label">{{ Lang::txt('DATE_ACCEPTED') }}</label>
          <input type="text" name="citation[date_accept]" id="date_accept"
                 class="input input-bordered w-full" maxlength="250"
                 value="{{ $row->get('date_accept', '') }}" />
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="date_publish" class="label">{{ Lang::txt('DATE_PUBLISHED') }}</label>
          <input type="text" name="citation[date_publish]" id="date_publish"
                 class="input input-bordered w-full" maxlength="250"
                 value="{{ $row->get('date_publish', '') }}" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="admin-field">
            <label for="year" class="label">{{ Lang::txt('YEAR') }}</label>
            <input type="text" name="citation[year]" id="year"
                   class="input input-bordered w-full" maxlength="4"
                   value="{{ $row->get('year', '') }}" />
          </div>
          <div class="admin-field">
            <label for="month" class="label">{{ Lang::txt('MONTH') }}</label>
            <input type="text" name="citation[month]" id="month"
                   class="input input-bordered w-full" maxlength="50"
                   value="{{ $row->get('month', '') }}" />
          </div>
        </div>
      </div>

      {{-- Author fields --}}
      <div class="admin-field">
        <label for="author" class="label">{{ Lang::txt('AUTHORS') }}</label>
        <input type="text" name="citation[author]" id="author"
               class="input input-bordered w-full"
               value="{{ $author }}" />
      </div>
      <div class="admin-field">
        <label for="author_address" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_AUTHOR_ADDRESS') }}</label>
        <input type="text" name="citation[author_address]" id="author_address"
               class="input input-bordered w-full"
               value="{{ $row->get('author_address', '') }}" />
      </div>
      <div class="admin-field">
        <label for="editor" class="label">{{ Lang::txt('EDITORS') }}</label>
        <input type="text" name="citation[editor]" id="editor"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $ceditor }}" />
      </div>

      {{-- Title fields --}}
      <div class="admin-field">
        <label for="title" class="label">{{ Lang::txt('TITLE_CHAPTER') }}</label>
        <input type="text" name="citation[title]" id="title"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $titleVal }}" />
      </div>
      <div class="admin-field">
        <label for="booktitle" class="label">{{ Lang::txt('BOOK_TITLE') }}</label>
        <input type="text" name="citation[booktitle]" id="booktitle"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $booktitle }}" />
      </div>
      <div class="admin-field">
        <label for="shorttitle" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_SHORT_TITLE') }}</label>
        <input type="text" name="citation[short_title]" id="shorttitle"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $short_title }}" />
      </div>
      <div class="admin-field">
        <label for="journal" class="label">{{ Lang::txt('JOURNAL') }}</label>
        <input type="text" name="citation[journal]" id="journal"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $journal }}" />
      </div>

      {{-- Volume / Issue --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="volume" class="label">{{ Lang::txt('VOLUME') }}</label>
          <input type="text" name="citation[volume]" id="volume"
                 class="input input-bordered w-full" maxlength="11"
                 value="{{ $row->get('volume', '') }}" />
        </div>
        <div class="admin-field">
          <label for="number" class="label">{{ Lang::txt('ISSUE') }}</label>
          <input type="text" name="citation[number]" id="number"
                 class="input input-bordered w-full" maxlength="50"
                 value="{{ $row->get('number', '') }}" />
        </div>
      </div>

      {{-- Pages / ISBN --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="pages" class="label">{{ Lang::txt('PAGES') }}</label>
          <input type="text" name="citation[pages]" id="pages"
                 class="input input-bordered w-full" maxlength="250"
                 value="{{ $row->get('pages', '') }}" />
        </div>
        <div class="admin-field">
          <label for="isbn" class="label">{{ Lang::txt('ISBN') }}</label>
          <input type="text" name="citation[isbn]" id="isbn"
                 class="input input-bordered w-full" maxlength="50"
                 value="{{ $row->get('isbn', '') }}" />
        </div>
      </div>

      {{-- DOI --}}
      <div class="admin-field">
        <label for="doi" class="label">{{ Lang::txt('DOI') }}</label>
        <input type="text" name="citation[doi]" id="doi"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('doi', '') }}" />
      </div>

      {{-- Call Number / Accession Number --}}
      <div class="admin-field">
        <label for="callnumber" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_CALL_NUMBER') }}</label>
        <input type="text" name="citation[call_number]" id="callnumber"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('call_number', '') }}" />
      </div>
      <div class="admin-field">
        <label for="accessionnumber" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_ACCESSION_NUMBER') }}</label>
        <input type="text" name="citation[accession_number]" id="accessionnumber"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('accession_number', '') }}" />
      </div>

      {{-- Series / Edition / School --}}
      <div class="admin-field">
        <label for="series" class="label">{{ Lang::txt('SERIES') }}</label>
        <input type="text" name="citation[series]" id="series"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('series', '') }}" />
      </div>
      <div class="admin-field">
        <label for="edition" class="label">{{ Lang::txt('EDITION') }}</label>
        <input type="text" name="citation[edition]" id="edition"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('edition', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('EDITION_EXPLANATION') }}</p>
      </div>
      <div class="admin-field">
        <label for="school" class="label">{{ Lang::txt('SCHOOL') }}</label>
        <input type="text" name="citation[school]" id="school"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('school', '') }}" />
      </div>

      {{-- Publisher / Institution --}}
      <div class="admin-field">
        <label for="publisher" class="label">{{ Lang::txt('PUBLISHER') }}</label>
        <input type="text" name="citation[publisher]" id="publisher"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('publisher', '') }}" />
      </div>
      <div class="admin-field">
        <label for="institution" class="label">{{ Lang::txt('INSTITUTION') }}</label>
        <input type="text" name="citation[institution]" id="institution"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('institution', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('INSTITUTION_EXPLANATION') }}</p>
      </div>

      {{-- Address / Location --}}
      <div class="admin-field">
        <label for="address" class="label">{{ Lang::txt('ADDRESS') }}</label>
        <input type="text" name="citation[address]" id="address"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('address', '') }}" />
      </div>
      <div class="admin-field">
        <label for="location" class="label">{{ Lang::txt('LOCATION') }}</label>
        <input type="text" name="citation[location]" id="location"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('location', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('LOCATION_EXPLANATION') }}</p>
      </div>

      {{-- How Published / URL / E-print --}}
      <div class="admin-field">
        <label for="howpublished" class="label">{{ Lang::txt('PUBLISH_METHOD') }}</label>
        <input type="text" name="citation[howpublished]" id="howpublished"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('howpublished', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('PUBLISH_METHOD_EXPLANATION') }}</p>
      </div>
      <div class="admin-field">
        <label for="url" class="label">{{ Lang::txt('URL') }}</label>
        <input type="text" name="citation[url]" id="url"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('url', '') }}" />
      </div>
      <div class="admin-field">
        <label for="eprint" class="label">{{ Lang::txt('EPRINT') }}</label>
        <input type="text" name="citation[eprint]" id="eprint"
               class="input input-bordered w-full" maxlength="250"
               value="{{ $row->get('eprint', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('EPRINT_EXPLANATION') }}</p>
      </div>

      {{-- WYSIWYG fields --}}
      <div class="admin-field">
        <label for="abstract" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_ABSTRACT') }}</label>
        {!! $__view->editor('citation[abstract]', $row->get('abstract', ''), 50, 10, 'abstract', $editorOpts) !!}
      </div>
      <div class="admin-field">
        <label for="note" class="label">{{ Lang::txt('NOTES') }}</label>
        {!! $__view->editor('citation[note]', $row->get('note', ''), 50, 10, 'note', $editorOpts) !!}
      </div>
      <div class="admin-field">
        <label for="keywords" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_KEYWORDS') }}</label>
        {!! $__view->editor('citation[keywords]', $row->get('keywords', ''), 50, 10, 'keywords', $editorOpts) !!}
      </div>
      <div class="admin-field">
        <label for="research_notes" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_RESEARCH_NOTES') }}</label>
        {!! $__view->editor('citation[research_notes]', $row->get('research_notes', ''), 50, 10, 'research_notes', $editorOpts) !!}
      </div>

  </x-admin-fieldset>

  {{-- Manual Format --}}
  <x-admin-fieldset legend="{{ Lang::txt('MANUAL_FORMAT') }}">
      <div class="admin-field">
        <label for="format_type" class="label">{{ Lang::txt('MANUAL_FORMAT_FORMAT') }}</label>
        <select id="format_type" name="citation[format]" class="select select-bordered w-full">
          <option value="apa" @selected($row->format == 'apa')>{{ Lang::txt('MANUAL_FORMAT_FORMAT_APA') }}</option>
          <option value="ieee" @selected($row->format == 'ieee')>{{ Lang::txt('MANUAL_FORMAT_FORMAT_IEEE') }}</option>
        </select>
      </div>
      <div class="admin-field">
        <label for="formatted" class="label">{{ Lang::txt('MANUAL_FORMAT_CITATION') }}</label>
        {!! $__view->editor('citation[formatted]', $row->get('formatted', ''), 50, 10, 'formatted', $editorOpts) !!}
      </div>
  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Citation For (Associations) --}}
    <x-admin-fieldset legend="{{ Lang::txt('CITATION_FOR') }}">
        <table class="table table-compact w-full" id="assocs">
          <thead>
            <tr>
              <th>{{ Lang::txt('TYPE') }}</th>
              <th>{{ Lang::txt('ID') }}</th>
              <th>{{ Lang::txt('COM_CITATIONS_CONTEXT') }}</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <td colspan="3">
                <button type="button" id="add_row" class="btn btn-sm btn-secondary">
                  {{ Lang::txt('ADD_A_ROW') }}
                </button>
              </td>
            </tr>
          </tfoot>
          <tbody>
            @php
              $n = max(count($assocs), 5);
            @endphp
            @for($i = 0; $i < $n; $i++)
              @php
                $a = $assocs[$i] ?? (object)['id' => null, 'cid' => null, 'oid' => null, 'type' => null, 'tbl' => null];
              @endphp
              <tr>
                <td>
                  <select name="assocs[{{ $i }}][tbl]" class="select select-bordered select-xs w-full"
                          aria-label="{{ Lang::txt('TYPE') }} {{ $i + 1 }}">
                    <option value="" @selected(($a->tbl ?? '') == '')>{{ Lang::txt('SELECT') }}</option>
                    <option value="resource" @selected(($a->tbl ?? '') == 'resource')>{{ Lang::txt('RESOURCE') }}</option>
                    <option value="publication" @selected(($a->tbl ?? '') == 'publication')>Publication</option>
                  </select>
                </td>
                <td>
                  <input type="text" name="assocs[{{ $i }}][oid]" class="input input-bordered input-xs w-16"
                         aria-label="{{ Lang::txt('ID') }} {{ $i + 1 }}"
                         value="{{ $a->oid ?? '' }}" />
                  <input type="hidden" name="assocs[{{ $i }}][id]" value="{{ $a->id ?? '' }}" />
                  <input type="hidden" name="assocs[{{ $i }}][cid]" value="{{ $a->cid ?? '' }}" />
                </td>
                <td>
                  <select name="assocs[{{ $i }}][type]" class="select select-bordered select-xs w-full"
                          aria-label="{{ Lang::txt('COM_CITATIONS_CONTEXT') }} {{ $i + 1 }}">
                    <option value="" @selected(($a->type ?? '') == '')>{{ Lang::txt('SELECT') }}</option>
                    <option value="references" @selected(($a->type ?? '') == 'references')>{{ Lang::txt('COM_CITATIONS_CONTEXT_REFERENCES') }}</option>
                    <option value="referencedby" @selected(($a->type ?? '') == 'referencedby')>{{ Lang::txt('COM_CITATIONS_CONTEXT_REFERENCEDBY') }}</option>
                  </select>
                </td>
              </tr>
            @endfor
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Affiliation --}}
    <x-admin-fieldset legend="{{ Lang::txt('AFFILIATION') }}" body-class="space-y-3">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="citation[affiliated]" value="1"
                 class="checkbox checkbox-sm"
                 @checked($row->affiliated) />
          <span class="text-sm">{{ Lang::txt('AFFILIATED_WITH_YOUR_ORG') }}</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="citation[fundedby]" value="1"
                 class="checkbox checkbox-sm"
                 @checked($row->fundedby) />
          <span class="text-sm">{{ Lang::txt('FUNDED_BY_YOUR_ORG') }}</span>
        </label>
    </x-admin-fieldset>

    {{-- Scope --}}
    <x-admin-fieldset legend="{{ Lang::txt('SCOPE') }}">
        <div class="admin-field">
          <label for="scope" class="label">{{ Lang::txt('SCOPE') }}</label>
          <select name="citation[scope]" id="scope" class="select select-bordered w-full">
            <option value="hub" @selected($row->scope == 'hub')>Hub</option>
            <option value="group" @selected($row->scope == 'group')>Group</option>
            <option value="member" @selected($row->scope == 'member')>Member</option>
          </select>
        </div>
        <div class="admin-field">
          <label for="scope_id" class="label">{{ Lang::txt('SCOPE_ID') }}</label>
          <input type="text" name="citation[scope_id]" id="scope_id"
                 class="input input-bordered w-full" maxlength="10"
                 value="{{ $row->get('scope_id', '') }}" />
        </div>
    </x-admin-fieldset>

    {{-- Options --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_CITATIONS_OPTIONS') }}">
        <div class="admin-field">
          <label for="field-sponsors" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_SPONSORS') }}</label>
          <select name="sponsors[]" id="field-sponsors"
                  class="select select-bordered w-full" multiple size="5">
            <option value="">- Select Citation Sponsor -</option>
            @foreach($sponsors as $s)
              <option value="{{ $s['id'] }}"
                      @selected(in_array($s->get('id'), $row_sponsors))>
                {{ $s['sponsor'] ?? '' }}
              </option>
            @endforeach
          </select>
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_CITATIONS_FIELD_SPONSORS_HINT') }}</p>
        </div>

        @if($config->get('citation_allow_tags', 'no') == 'yes')
          <div class="admin-field">
            <label for="field-tags" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_TAGS') }}</label>
            <textarea name="tags" id="field-tags" rows="4"
                      class="textarea textarea-bordered w-full">{{ implode(',', $tags) }}</textarea>
          </div>
        @endif

        @if($config->get('citation_allow_badges', 'no') == 'yes')
          <div class="admin-field">
            <label for="field-badges" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_BADGES') }}</label>
            <textarea name="badges" id="field-badges" rows="4"
                      class="textarea textarea-bordered w-full">{{ implode(',', $badges) }}</textarea>
          </div>
        @endif

        <div class="admin-field">
          <label for="field-exclude" class="label">{{ Lang::txt('COM_CITATIONS_FIELD_EXCLUDE_FROM_EXPORT') }}</label>
          <textarea name="exclude" id="field-exclude" rows="4"
                    class="textarea textarea-bordered w-full">{{ $params->get('exclude') }}</textarea>
        </div>

        @php
          $rollovers = $config->get('citation_rollover', 'no');
          $rollover  = $params->get('rollover');
          $ckd = ($rollovers == 'yes');
          if ($rollover == 1) {
              $ckd = true;
          } elseif ($rollover == 0 && is_numeric($rollover)) {
              $ckd = false;
          }
        @endphp
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="rollover" value="1"
                 class="checkbox checkbox-sm"
                 @checked($ckd) />
          <span class="text-sm">{{ Lang::txt('COM_CITATIONS_FIELD_ABSTRACT_ROLLOVER') }}</span>
        </label>
    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="citation[uid]" value="{{ $row->uid }}" />
  <input type="hidden" name="citation[created]" value="{{ $row->created }}" />
  <input type="hidden" name="citation[id]" value="{{ $row->id }}" />
  <input type="hidden" name="citation[published]" value="{{ $row->published }}" />
</x-admin-edit>
