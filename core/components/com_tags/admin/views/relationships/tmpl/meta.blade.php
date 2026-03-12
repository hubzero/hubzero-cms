{{--
  Tag Focus Areas — Admin management

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo = \Components\Tags\Helpers\Permissions::getActions();

  $__view->css('tag_graph.css');
  $__view->js('d3.js', 'system')
      ->js('tag_graph.blade.js');

  $dbh = App::get('db');
  $dbh->setQuery(
      'SELECT *, (SELECT group_concat(resource_type_id)'
      . ' FROM `#__focus_area_resource_type_rel` WHERE focus_area_id = fa.id) AS types'
      . ' FROM `#__tags` t'
      . ' INNER JOIN `#__focus_areas` fa ON fa.tag_id = t.id'
      . ' ORDER BY raw_tag'
  );
  $fas = $dbh->loadAssocList();

  $dbh->setQuery(
      'SELECT DISTINCT id, type FROM `#__resource_types`'
      . ' WHERE category = (SELECT id FROM `#__resource_types` WHERE type = \'Main Types\')'
      . ' AND contributable ORDER BY type'
  );
  $types = $dbh->loadAssocList('id');

  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_TAGS') }}: {{ Lang::txt('COM_TAGS_FOCUS_AREAS') }}"
    icon="tags"
    option="{{ $option }}"
    :edit="true"
/>

<template id="resource-types">
  { "types": {!! json_encode(array_values($types)) !!} }
</template>

<form action="{{ $formAction }}" method="post" id="item-form" name="adminForm">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2">
      <div id="fas" class="space-y-4">
        @foreach ($fas as $i => $fa)
          @php
            $type_ids = array_flip(explode(',', $fa['types']));
          @endphp
          <div class="admin-fieldset" id="group-{{ $i }}">
            <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_TAGS_GROUP') }}</h3>
            <x-admin-fieldset>

              <div class="admin-field">
                <label for="name-{{ $fa['id'] }}" class="label">
                  {{ Lang::txt('COM_TAGS_GROUP_NAME') }}
                </label>
                <input type="text"
                       name="name-{{ $fa['id'] }}"
                       id="name-{{ $fa['id'] }}"
                       class="input input-bordered w-full"
                       value="{{ $fa['raw_tag'] }}" />
              </div>

              {{-- Resource types --}}
              <fieldset>
                <legend class="label">{{ Lang::txt('COM_TAGS_GROUP_RESOURCE_TYPES') }}</legend>
                <div class="space-y-2">
                  <select id="types-{{ $fa['id'] }}"
                          name="types-{{ $fa['id'] }}[]"
                          class="select select-bordered w-full"
                          multiple
                          size="{{ count($types) }}">
                    @foreach ($types as $type)
                      <option value="{{ $type['id'] }}"
                              @selected(isset($type_ids[$type['id']]))>
                        {{ $type['type'] }}
                      </option>
                    @endforeach
                  </select>

                  <div class="flex flex-col gap-1">
                    <label class="label cursor-pointer justify-start gap-2">
                      <input type="radio"
                             name="mandatory-{{ $fa['id'] }}"
                             value="optional"
                             class="radio radio-sm"
                             @checked(is_null($fa['mandatory_depth'])) />
                      <span>{{ Lang::txt('COM_TAGS_OPTIONAL') }}</span>
                    </label>
                    <label class="label cursor-pointer justify-start gap-2">
                      <input type="radio"
                             name="mandatory-{{ $fa['id'] }}"
                             value="mandatory"
                             class="radio radio-sm"
                             @checked(!is_null($fa['mandatory_depth']) && $fa['mandatory_depth'] < 2) />
                      <span>{{ Lang::txt('COM_TAGS_MANDATORY') }}</span>
                    </label>
                    <div class="flex items-center gap-2">
                      <label class="label cursor-pointer justify-start gap-2">
                        <input type="radio"
                               name="mandatory-{{ $fa['id'] }}"
                               value="depth"
                               class="radio radio-sm"
                               @checked($fa['mandatory_depth'] > 1) />
                        <span>{{ Lang::txt('COM_TAGS_MANDATORY') }}</span>
                      </label>
                      <span class="text-sm">{{ Lang::txt('COM_TAGS_GROUP_UNTIL_DEPTH') }}:</span>
                      <input type="text"
                             name="mandatory-depth-{{ $fa['id'] }}"
                             class="input input-bordered input-sm w-16"
                             value="{{ ($fa['mandatory_depth'] > 1) ? $fa['mandatory_depth'] : '' }}" />
                    </div>
                </div>
              </fieldset>

              {{-- Selection type --}}
              <fieldset>
                <legend class="label">{{ Lang::txt('COM_TAGS_GROUP_SELECTION_TYPE') }}</legend>
                <div class="flex flex-col gap-1">
                  <label class="label cursor-pointer justify-start gap-2">
                    <input type="radio"
                           name="multiple-{{ $fa['id'] }}"
                           value="multiple"
                           class="radio radio-sm"
                           @checked(!is_null($fa['multiple_depth']) && $fa['multiple_depth'] < 2) />
                    <span>{{ Lang::txt('COM_TAGS_GROUP_MULTI_SELECT') }}</span>
                  </label>
                  <label class="label cursor-pointer justify-start gap-2">
                    <input type="radio"
                           name="multiple-{{ $fa['id'] }}"
                           value="single"
                           class="radio radio-sm"
                           @checked(is_null($fa['multiple_depth'])) />
                    <span>{{ Lang::txt('COM_TAGS_GROUP_SINGLE_SELECT_RADIO') }}</span>
                  </label>
                  <div class="flex items-center gap-2">
                    <label class="label cursor-pointer justify-start gap-2">
                      <input type="radio"
                             name="multiple-{{ $fa['id'] }}"
                             value="depth"
                             class="radio radio-sm"
                             @checked($fa['multiple_depth'] > 1) />
                      <span>{{ Lang::txt('COM_TAGS_GROUP_SINGLE_SELECT') }}</span>
                    </label>
                    <span class="text-sm">{{ Lang::txt('COM_TAGS_GROUP_UNTIL_DEPTH') }}:</span>
                    <input type="text"
                           name="multiple-depth-{{ $fa['id'] }}"
                           class="input input-bordered input-sm w-16"
                           value="{{ ($fa['multiple_depth'] > 1) ? $fa['multiple_depth'] : '' }}" />
                    </div>
              </fieldset>

              <div>
                <button class="btn btn-sm btn-error delete-group"
                        id="delete-{{ $i }}"
                        rel="group-{{ $i }}">
                  {{ Lang::txt('COM_TAGS_DELETE_GROUP') }}
                </button>
              </div>

            </x-admin-fieldset>
          </div>
        @endforeach
      </div>

      <p class="mt-4">
        <button id="add_group" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_TAGS_ADD_GROUP') }}
        </button>
      </p>

      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />
      <input type="hidden" name="task" value="updatefocusareas" />
    </div>

    <div>
      <x-admin-fieldset legend="{{ Lang::txt('COM_TAGS_FOCUS_AREAS') }}" body-class="prose prose-sm max-w-none">
          {!! Lang::txt('COM_TAGS_GROUP_EXPLANATION') !!}
      </x-admin-fieldset>
    </div>
  </div>
</form>
