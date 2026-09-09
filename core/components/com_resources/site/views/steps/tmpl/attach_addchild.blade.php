{{--
  Resources contribution — add child resource by search (collection type).

  Variables:
    $id — parent resource id

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Route;

  $baseUrl = '/resources/draft?controller=attachments&no_html=1';
  $actionUrl = $baseUrl . '&task=create';
  $autocompleteUrl = '/api/v1.1' . Route::url('index.php?option=com_resources&task=autocomplete');
@endphp

<div>
  <label for="resource-finder" class="sr-only">
    Search Resources by ID or Title
  </label>
  <div class="flex gap-2">
    <input id="resource-finder"
           type="text"
           class="input input-bordered flex-1"
           autocomplete="off"
           placeholder="Search Resources by ID or Title."
           aria-label="Search Resources by ID or Title"
           data-script="{{ $autocompleteUrl }}" />
    <a href="{{ $actionUrl }}"
       class="btn btn-outline"
       id="add-child"
       data-pid="{{ $id }}"
       data-childid=""
       aria-disabled="true">
      Add
    </a>
  </div>
</div>
