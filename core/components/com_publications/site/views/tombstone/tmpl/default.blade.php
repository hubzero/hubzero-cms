{{--
  Publication tombstone page — archived/retracted DOI landing.

  Variables from controller:
    $record — publication record with title, doi, unpublished_reason

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $__view->css()->js();

  $doiUrl = Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DOI_ORG') . $record->doi;
  $reason = Lang::txt('COM_PUBLICATIONS_TOMBSTONE_REASON')
      . lcfirst($record->unpublished_reason) . '.';
  $idLabel = Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DATASET_IDENTIFIER');
  $pageTitle = Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DATASET_TITLE') . $record->title;
@endphp

<x-page-container :title="$pageTitle">
  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">
      <h3 class="card-title">
        {{ Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DATASET_RETRACTION_DESCRIPTION') }}
      </h3>
      <p>
        {{ $idLabel }}
        <a target="_blank" rel="external" href="{{ $doiUrl }}">{{ $doiUrl }}</a>
        {{ $reason }}
      </p>
      <p>{{ Lang::txt('COM_PUBLICATIONS_TOMBSTONE_CONTACT_PURR') }}</p>
    </div>
  </div>
</x-page-container>
