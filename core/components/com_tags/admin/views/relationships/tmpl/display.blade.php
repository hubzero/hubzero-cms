{{--
  Tag Relationships — Admin graph view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo = \Components\Tags\Helpers\Permissions::getActions();

  $__view->css('tag_graph.css');
  $__view->js('d3.js', 'system')
      ->js('tag_graph.blade.js');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_TAGS') }}: {{ Lang::txt('COM_TAGS_RELATIONSHIPS') }}"
    icon="tags"
    option="{{ $option }}"
/>

{{-- Tag lookup --}}
<div class="admin-fieldset mb-4">
  <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_TAGS_FIND_TAG') }}</h3>
  <div class="admin-fieldset-body">
    @php
      $tagSelAction = Route::url(
          'index.php?option=' . $option . '&controller=' . $controller, false
      );
    @endphp
    <form id="tag-sel" action="{{ $tagSelAction }}" method="get">
      <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
          <div class="admin-field">
            <label for="center-node" class="label">
              {{ Lang::txt('COM_TAGS_TAG') }}
            </label>
            <input type="text"
                   id="center-node"
                   class="input input-bordered w-full tag-entry"
                   value="{{ $__view->get('preload') }}" />
          </div>
          <div class="admin-field">
            <span class="text-sm text-muted-foreground">
              {{ Lang::txt('COM_TAGS_TAG_RELATIONSHIP') }}
            </span>
          </div>
          <div class="admin-field">
            <button type="submit" id="center" class="btn btn-sm btn-primary">
              {{ Lang::txt('COM_TAGS_LOOKUP') }}
            </button>
          </div>

        <fieldset class="admin-field">
          <legend class="label text-base-content">{{ Lang::txt('COM_TAGS_SHOW_RELATIONSHIPS') }}</legend>
          <div class="flex gap-4 mt-1">
            <label class="label cursor-pointer justify-start gap-2">
              <input type="radio"
                     name="relationship"
                     id="hierarchical"
                     class="radio radio-sm"
                     checked />
              <span>{{ Lang::txt('COM_TAGS_RELATIONSHIP_HIERARCHICAL') }}</span>
            </label>
            <label class="label cursor-pointer justify-start gap-2">
              <input type="radio"
                     name="relationship"
                     id="implicit"
                     class="radio radio-sm" />
              <span>{{ Lang::txt('COM_TAGS_RELATIONSHIP_IMPLICIT') }}</span>
            </label>
          </div>
        </fieldset>
      </div>
    </form>
  </div>
</div>

{{-- Graph --}}
<div class="admin-fieldset mb-4">
  <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_TAGS_RELATIONSHIP_GRAPH') }}</h3>
  <div class="admin-fieldset-body">
    <div id="graph"></div>
  </div>
</div>

{{-- Metadata form --}}
<div id="metadata-cont">
  @php
    $metadataAction = Route::url(
        'index.php?option=' . $option . '&controller=' . $controller, false
    );
  @endphp
  <form id="metadata" action="{{ $metadataAction }}" method="post">
    <x-admin-fieldset legend="{{ Lang::txt('COM_TAGS_RELATIONSHIP_METADATA') }}">

        <div class="admin-field">
          <label for="description" class="label">
            {{ Lang::txt('COM_TAGS_RELATIONSHIP_DESCRIPTION') }}
          </label>
          <textarea id="description"
                    name="description"
                    class="textarea textarea-bordered w-full"
                    rows="4"></textarea>
        </div>

        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_TAGS_RELATIONSHIP_LABELED') }}</label>
          <ul id="labeled" class="textboxlist-holder act"></ul>
        </div>

        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_TAGS_RELATIONSHIP_LABELS') }}</label>
          <ul id="labels" class="textboxlist-holder act"></ul>
        </div>

        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_TAGS_RELATIONSHIP_PARENTS') }}</label>
          <ul id="parents" class="textboxlist-holder act"></ul>
        </div>

        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_TAGS_RELATIONSHIP_CHILDREN') }}</label>
          <ul id="children" class="textboxlist-holder act"></ul>
        </div>

        <div>
          <input type="hidden" class="tag-id" name="tag" value="" />
          <input type="hidden" name="task" value="update" />
          <button type="submit" class="btn btn-sm btn-primary">
            {{ Lang::txt('COM_TAGS_RELATIONSHIP_UPDATE') }}
          </button>
        </div>

    </x-admin-fieldset>
  </form>
</div>

@php
  $adminFormAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
@endphp
<form name="adminForm" method="get" action="{{ $adminFormAction }}">
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
</form>
