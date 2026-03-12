{{--
  Citation Type — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Citations\Helpers\Permissions::getActions('type');
  $text  = $type->id ? Lang::txt('EDIT') : Lang::txt('NEW');

  Toolbar::title(Lang::txt('CITATIONS') . ' ' . Lang::txt('CITATION_TYPES') . ': ' . $text, 'citation');
  if ($canDo->get('core.edit')) {
      Toolbar::save();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('type');


  $__view->js();

  $fieldMap = [
      'cite'            => 'Cite Key',
      'ref_type'        => 'Ref Type',
      'date_submit'     => 'Date Submitted',
      'date_accept'     => 'Date Accepted',
      'date_publish'    => 'Date Published',
      'year'            => 'Year',
      'author'          => 'Authors',
      'author_address'  => 'Author Address',
      'editor'          => 'Editors',
      'booktitle'       => 'Book Title',
      'shorttitle'      => 'Short Title',
      'journal'         => 'Journal',
      'volume'          => 'Volume',
      'issue'           => 'Issue/Number',
      'pages'           => 'Pages',
      'isbn'            => 'ISBN/ISSN',
      'doi'             => 'DOI',
      'callnumber'      => 'Call Number',
      'accessionnumber' => 'Accession Number',
      'series'          => 'Series',
      'edition'         => 'Edition',
      'school'          => 'School',
      'publisher'       => 'Publisher',
      'institution'     => 'Institution',
      'address'         => 'Address',
      'location'        => 'Location',
      'howpublished'    => 'How Published',
      'uri'             => 'URL',
      'eprint'          => 'E-print',
      'abstract'        => 'Abstract',
      'note'            => 'Text Snippet/ Notes',
      'keywords'        => 'Keywords',
      'research_notes'  => 'Research Notes',
      'language'        => 'Language',
      'label'           => 'Label',
  ];
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('CITATION_TYPES') }}">

      <div class="admin-field">
        <label for="field-type" class="label">{{ Lang::txt('CITATION_TYPES_ALIAS') }}</label>
        <input type="text" name="type[type]" id="field-type"
               class="input input-bordered w-full"
               value="{{ $type->type ?? '' }}" />
      </div>

      <div class="admin-field">
        <label for="field-type_title" class="label">{{ Lang::txt('CITATION_TYPES_TITLE') }}</label>
        <input type="text" name="type[type_title]" id="field-type_title"
               class="input input-bordered w-full"
               value="{{ $type->type_title ?? '' }}" />
      </div>

      <div class="admin-field">
        <label for="field-type_desc" class="label">{{ Lang::txt('CITATION_TYPES_DESC') }}</label>
        <textarea name="type[type_desc]" id="field-type_desc"
                  class="textarea textarea-bordered w-full" rows="5">{{ $type->type_desc ?? '' }}</textarea>
      </div>

      <div class="admin-field">
        <label for="field-fields" class="label">{{ Lang::txt('CITATION_TYPES_FIELDS') }}</label>
        <textarea name="type[fields]" id="field-fields"
                  class="textarea textarea-bordered w-full font-mono text-sm" rows="20">{{ $type->fields ?? '' }}</textarea>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('CITATION_TYPES_FIELDS_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('ID') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <th scope="row">{{ Lang::txt('ID') }}</th>
              <td>{{ $type->id ?: 0 }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Field Reference --}}
    <x-admin-fieldset legend="{{ Lang::txt('CITATION_TYPES_PLACEHOLDER') }}">
        <table class="admin-table text-sm">
          <thead>
            <tr>
              <th scope="col">{{ Lang::txt('CITATION_TYPES_PLACEHOLDER') }}</th>
              <th scope="col">{{ Lang::txt('CITATION_TYPES_FIELD') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fieldMap as $k => $v)
              <tr>
                <td class="font-mono text-xs">{{ $k }}</td>
                <td>{{ $v }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="type[id]" value="{{ $type->id }}" />
</x-admin-edit>
