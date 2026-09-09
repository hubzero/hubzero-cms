{{--
  Collections — add resource to collection form.

  Variables (from plugin):
    $resource  — object: resource model
    $type      — object: resource type (->type, ->id)
    $resources — collection: existing resources of same type

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $addLabel = Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD', $type->type);
@endphp

<p>
  <a class="btn primary" href="#collectionForm" id="add-collection">{{ $addLabel }}</a>
</p>

<form action="{{ Route::url($resource->link()) }}"
      method="post"
      id="collectionForm"
      class="full hide">
  <fieldset>
    <legend>{{ Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD', $type->type) }}</legend>
    <div class="grid">
      @if($resources->count() > 0)
        <div class="col span12">
          <label for="pid">
            {{ Lang::txt('PLG_RESOURCES_COLLECTIONS_SELECT', $type->type) }}
          </label>
          @php
            $placeholder = Lang::txt('PLG_RESOURCES_COLLECTIONS_SELECT_PLACEHOLDER', $type->type);
          @endphp
          <select name="pid" id="pid">
            <option value="" selected>{{ $placeholder }}</option>
            @foreach($resources as $entry)
              <option value="{{ $entry->id }}">{{ $entry->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col span12">
          <p class="or">{{ Lang::txt('OR') }}</p>
        </div>
      @endif
      <div class="col span12" id="new-series-add">
        <label>{{ Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD_NEW', $type->type) }}</label>
        <label for="resource-title">
          {{ Lang::txt('PLG_RESOURCES_COLLECTIONS_TITLE') }}
        </label>
        <input type="text" name="resource-title" id="resource-title" value="" />
      </div>
    </div>
  </fieldset>
  <p class="submit">
    <input type="hidden" name="childid" value="{{ $resource->id }}" />
    <input type="hidden" name="controller" value="attachments" />
    <input type="hidden" name="task" value="create" />
    <input type="hidden" name="type" value="{{ $type->id }}" />
    <input type="submit" value="{{ Lang::txt('Add') }}" />
  </p>
</form>
