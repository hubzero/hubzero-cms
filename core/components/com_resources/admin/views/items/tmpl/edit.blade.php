{{--
  Resource — Admin edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Resources\Helpers\Permissions::getActions('resource');
  $text  = $row->id
      ? Lang::txt('JACTION_EDIT') . ' #' . $row->id
      : Lang::txt('JACTION_CREATE');

  if ($row->standalone == 1) {
      $type = $row->type;
      $data = [];
      preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $row->fulltxt, $matches, PREG_SET_ORDER);
      if (count($matches) > 0) {
          foreach ($matches as $match) {
              $data[$match[1]] = $match[2];
          }
      }
      $row->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $row->fulltxt);
      $row->fulltxt = trim($row->fulltxt);
      $row->fulltxt = ($row->fulltxt) ? trim($row->fulltxt) : trim($row->introtext);
  }

  // Build file upload path
  $path = \Components\Resources\Helpers\Html::dateToPath($row->created);
  $dir_id = $row->id
      ? \Components\Resources\Helpers\Html::niceidformat($row->id)
      : time() . rand(0, 10000);

  $time = $row->attribs->get('timeof', '');
  $time = strtotime($time) === false ? null : $time;
@endphp

@include('com_resources::admin.views.items.tmpl._edit_script', ['rconfig' => $rconfig, 'row' => $row])

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ $text }}"
    icon="resources"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
      @if($row->type && $row->type->isForTools())
        @include('com_resources::admin.views.items.tmpl._edit_tool_fields', ['row' => $row])
      @else
        @include('com_resources::admin.views.items.tmpl._edit_non_tool_fields', [
            'row' => $row,
            'lists' => $lists,
            'licenses' => $licenses,
            'time' => $time,
        ])
      @endif
  </x-admin-fieldset>

  {{-- Custom fields (standalone, non-tool) --}}
  @if($row->standalone == 1 && $row->type && !$row->type->isForTools())
    <x-admin-fieldset legend="{{ Lang::txt('Custom fields') }}">
        <div id="resource-custom-fields">
          @php
            $elements = new \Components\Resources\Models\Elements($data, $type->customFields);
            $fields = $elements->getElements('nbtag');
          @endphp
          @if($fields && count($fields) > 0)
            @foreach($fields as $field)
              <div class="admin-field">
                @if($field->label)
                  {!! $field->label !!}
                @endif
                {!! $field->element !!}
              </div>
            @endforeach
          @endif
        </div>
    </x-admin-fieldset>
  @endif

  @slot('sidebar')
    {{-- Meta table --}}
    @if($row->id)
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_ID') }}</td>
                <td>{{ $row->id }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_CREATED') }}</td>
                <td>{{ Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_LC2')) }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_CREATOR') }}</td>
                <td>
                  {{ User::getInstance($row->created_by)->get('name') }}
                  <input type="hidden" name="created_by_id" value="{{ $row->created_by }}" />
                </td>
              </tr>
              @if($row->modified && $row->modified != '0000-00-00 00:00:00')
                <tr>
                  <td>{{ Lang::txt('COM_RESOURCES_FIELD_MODIFIED') }}</td>
                  <td>{{ Date::of($row->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2')) }}</td>
                </tr>
                <tr>
                  <td>{{ Lang::txt('COM_RESOURCES_FIELD_MODIFIER') }}</td>
                  <td>
                    {{ User::getInstance($row->modified_by)->get('name') }}
                    <input type="hidden" name="modified_by_id" value="{{ $row->modified_by }}" />
                  </td>
                </tr>
              @endif
              @if($row->standalone == 1)
                <tr>
                  <td>{{ Lang::txt('COM_RESOURCES_FIELD_RANKING') }}</td>
                  <td>
                    {{ $row->ranking }}/10
                    @if($row->ranking != '0')
                      <button type="button"
                              class="btn btn-xs btn-ghost"
                              name="reset_ranking"
                              id="reset_ranking"
                              data-task="resetranking">
                        Reset ranking
                      </button>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td>{{ Lang::txt('COM_RESOURCES_FIELD_RATING') }}</td>
                  <td>
                    {{ $row->rating }}/5.0 ({{ $row->times_rated }})
                    @if($row->rating != '0.0')
                      <button type="button"
                              class="btn btn-xs btn-ghost"
                              name="reset_rating"
                              id="reset_rating"
                              data-task="resetrating">
                        Reset rating
                      </button>
                      @php
                        $ratingsUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=ratings&id=' . $row->id
                            . '&no_html=1', false
                        );
                      @endphp
                      <a class="btn btn-xs btn-ghost" href="{{ $ratingsUrl }}">
                        {{ Lang::txt('COM_RESOURCES_VIEW') }}
                      </a>
                    @endif
                  </td>
                </tr>
              @endif
              <tr>
                <td>{{ Lang::txt('COM_RESOURCES_FIELD_HITS') }}</td>
                <td>
                  {{ $row->hits }}
                  @if($row->hits)
                    <button type="button"
                            class="btn btn-xs btn-ghost"
                            name="reset_hits"
                            id="reset_hits"
                            data-task="resethits">
                      Reset Hit Count
                    </button>
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
      </x-admin-fieldset>
    @endif

    {{-- Contributors (standalone, non-tool) --}}
    @if($row->standalone == 1 && $row->type && !$row->type->isForTools())
      <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_FIELDSET_CONTRIBUTORS') }}">
          <div id="resource-authors">
            {!! $lists['authors'] !!}
          </div>
      </x-admin-fieldset>
    @endif

    {{-- Publishing --}}
    <details class="collapse collapse-arrow bg-base-200 rounded-box mb-2" open>
      <summary class="collapse-title font-medium">
        {{ Lang::txt('COM_RESOURCES_FIELDSET_PUBLISHING') }}
      </summary>
      <div class="collapse-content">
        <input type="hidden" name="fields[standalone]" id="field-standalone" value="{{ $row->standalone }}" />

        <div class="admin-field">
          <label for="field-published" class="label">
            {{ Lang::txt('COM_RESOURCES_FIELD_STATUS') }}
          </label>
          <select name="fields[published]" id="field-published" class="select select-bordered w-full">
            <option value="2" @selected($row->published == 2)>{{ Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') }}</option>
            <option value="5" @selected($row->published == 5)>{{ Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') }}</option>
            <option value="3" @selected($row->published == 3)>{{ Lang::txt('COM_RESOURCES_PENDING') }}</option>
            <option value="0" @selected($row->published == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->published == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="4" @selected($row->published == 4)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        @if($row->standalone == 1)
          <div class="admin-field">
            <label for="field-group_owner" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_GROUP') }}</label>
            {!! $lists['groups'] !!}
          </div>
        @endif

        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_ACCESS') }}</label>
          {!! $lists['access'] !!}
        </div>

        <div class="admin-field">
          <label for="created_by" class="label">{{ Lang::txt('COM_RESOURCES_FIELD_CREATOR') }}</label>
          {!! $lists['created_by'] !!}
        </div>

        <div class="admin-field">
          <label for="fields-publish_up" class="label">
            {{ Lang::txt('COM_RESOURCES_FIELD_PUBLISH_UP') }}
          </label>
          @php
            $up = ($row->publish_up && $row->publish_up != '0000-00-00 00:00:00')
                ? Date::of($row->publish_up)->toLocal('Y-m-d H:i:s')
                : '';
          @endphp
          {!! Html::input('calendar', 'fields[publish_up]', $up) !!}
        </div>

        <div class="admin-field">
          <label for="fields-publish_down" class="label">
            {{ Lang::txt('COM_RESOURCES_FIELD_PUBLISH_DOWN') }}
          </label>
          @php
            $down = '';
            if ($row->publish_down
                && $row->publish_down != '0000-00-00 00:00:00'
                && $row->publish_down != Lang::txt('COM_RESOURCES_NEVER')
            ) {
                $down = Date::of($row->publish_down)->toLocal('Y-m-d H:i:s');
            }
          @endphp
          {!! Html::input('calendar', 'fields[publish_down]', $down, ['placeholder' => Lang::txt('COM_RESOURCES_NEVER')]) !!}
        </div>
      </div>
    </details>

    {{-- ACL --}}
    <details class="collapse collapse-arrow bg-base-200 rounded-box mb-2">
      <summary class="collapse-title font-medium">
        {{ Lang::txt('COM_RESOURCES_FIELDSET_ACL') }}
      </summary>
      <div class="collapse-content">
        <div id="resource-useracl">
          {!! $lists['aclusers'] !!}
        </div>
        <div id="resource-groupacl">
          {!! $lists['aclgroups'] !!}
        </div>
      </div>
    </details>

    {{-- Files --}}
    <details class="collapse collapse-arrow bg-base-200 rounded-box mb-2">
      <summary class="collapse-title font-medium">
        {{ Lang::txt('COM_RESOURCES_FIELDSET_FILES') }}
      </summary>
      <div class="collapse-content">
        <div class="admin-field">
          <label for="fileoptions" class="label">
            {{ Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED') }}:
          </label>
          <div class="flex gap-2">
            <select name="fileoptions" id="fileoptions" class="select select-bordered select-sm flex-1">
              <option value="2">{{ Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED_MAIN') }}</option>
              <option value="3">{{ Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED_IMG') }}</option>
              <option value="4">{{ Lang::txt('COM_RESOURCES_FIELD_WITH_SELECTED_LINKED') }}</option>
            </select>
            <button type="button"
                    class="btn btn-sm btn-ghost"
                    data-action="do-fileoptions">
              {{ Lang::txt('COM_RESOURCES_APPLY') }}
            </button>
          </div>
        </div>

        @php
          $srcUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=media&tmpl=component&listdir='
              . $path . DIRECTORY_SEPARATOR . $dir_id, false
          );
        @endphp
        <iframe width="100%" height="400" name="filer" id="filer" src="{{ $srcUrl }}"></iframe>
        <input type="hidden" name="tmpid" value="{{ $dir_id }}" />
      </div>
    </details>

    @if($row->standalone == 1)
      {{-- Tags --}}
      <details class="collapse collapse-arrow bg-base-200 rounded-box mb-2">
        <summary class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_FIELDSET_TAGS') }}
        </summary>
        <div class="collapse-content">
          <textarea name="tags"
                    id="tags"
                    class="textarea textarea-bordered w-full"
                    rows="6">{{ $lists['tags'] }}</textarea>
        </div>
      </details>

      {{-- Badges --}}
      <details class="collapse collapse-arrow bg-base-200 rounded-box mb-2">
        <summary class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_FIELDSET_BADGES') }}
        </summary>
        <div class="collapse-content">
          <textarea name="badges"
                    id="badges"
                    class="textarea textarea-bordered w-full"
                    rows="6">{{ $lists['badges'] ?? '' }}</textarea>
        </div>
      </details>

      {{-- Parameters --}}
      <details class="collapse collapse-arrow bg-base-200 rounded-box mb-2">
        <summary class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_FIELDSET_PARAMETERS') }}
        </summary>
        <div class="collapse-content">
          {!! $params->render() !!}
        </div>
      </details>
    @else
      {{-- Parameters (linked resource) --}}
      <details class="collapse collapse-arrow bg-base-200 rounded-box mb-2">
        <summary class="collapse-title font-medium">
          {{ Lang::txt('COM_RESOURCES_FIELDSET_PARAMETERS') }}
        </summary>
        <div class="collapse-content">
          <div class="admin-field">
            <label for="param-link_action" class="label">
              {{ Lang::txt('COM_RESOURCES_FIELD_LINK_ACTION_HINT') }}
            </label>
            @php $linkAction = $params->get('link_action'); @endphp
            <select name="params[link_action]" id="param-link_action" class="select select-bordered w-full">
              <option value="0" @selected(!$linkAction)>{{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_DEFAULT') }}</option>
              <option value="1" @selected($linkAction == 1)>{{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_NEW_WINDOW') }}</option>
              <option value="2" @selected($linkAction == 2)>{{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_LIGHTBOX') }}</option>
              <option value="3" @selected($linkAction == 3)>{{ Lang::txt('COM_RESOURCES_FIELD_LINKED_ACTION_DOWNLOAD') }}</option>
            </select>
          </div>

          <div class="admin-field">
            <label for="param-restrict_direct_access" class="label">
              {{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_HINT') }}
            </label>
            @php $rda = $params->get('restrict_direct_access'); @endphp
            <select name="params[restrict_direct_access]" id="param-restrict_direct_access" class="select select-bordered w-full">
              <option value="0" @selected(!$rda)>{{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_DEFAULT') }}</option>
              <option value="1" @selected($rda == 1)>{{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_NO') }}</option>
              <option value="2" @selected($rda == 2)>{{ Lang::txt('COM_RESOURCES_FIELD_RESTRICT_DIRECT_ACCESS_YES') }}</option>
            </select>
          </div>
        </div>
      </details>
    @endif
  @endslot

  <input type="hidden" name="id" id="id" value="{{ $row->id }}" />
  <input type="hidden" name="pid" value="{{ $pid }}" />
  <input type="hidden" name="isnew" value="{{ $isnew }}" />
  <input type="hidden" name="task" value="" />
</x-admin-edit>
