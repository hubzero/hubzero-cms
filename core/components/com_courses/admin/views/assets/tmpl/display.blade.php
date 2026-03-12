{{--
  Courses — Assets iframe view (tmpl=component)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $__view->js();

  $ids = [];
  foreach ($rows as $row) {
      $ids[] = $row->id;
  }

  $pageNav = $__view->pagination(
      $total,
      $filters['start'],
      $filters['limit']
  );

  $routeUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );

  $createUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=add&scope=' . $filters['asset_scope']
      . '&scope_id=' . $filters['asset_scope_id']
      . '&course_id=' . $filters['course_id']
      . '&tmpl=' . $filters['tmpl'], false
  );

  $n = count($rows);
@endphp

<form action="{{ $routeUrl }}"
      method="post"
      name="adminForm"
      id="adminForm"
      class="p-2">
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="table table-sm w-full">
      <thead>
        <tr>
          <th colspan="4">
            <div class="flex items-center gap-2">
              <label for="filter-asset" class="sr-only">{{ Lang::txt('COM_COURSES_SELECT') }}</label>
              <select name="asset" id="filter-asset" class="select select-bordered select-sm">
                <option value="0">{{ Lang::txt('COM_COURSES_SELECT') }}</option>
                @if($assets)
                  @foreach($assets as $asset)
                    @if(!in_array($asset->id, $ids))
                      <option value="{{ $asset->id }}">
                        {{ $asset->title }} ({{ $asset->type }})
                      </option>
                    @endif
                  @endforeach
                @endif
              </select>
              <button type="submit" id="btn-attach" class="btn btn-sm btn-primary">
                {{ Lang::txt('COM_COURSES_ATTACH_ASSET') }}
              </button>
            </div>
          </th>
          <th colspan="4" class="text-right">
            <a href="{{ $createUrl }}"
               class="btn btn-sm btn-outline edit-asset"
               data-handler="iframe"
               data-width="570"
               data-height="550">
              {{ Lang::txt('COM_COURSES_CREATE_ASSET') }}
            </a>
          </th>
        </tr>
        <tr>
          <th scope="col">{{ Lang::txt('COM_COURSES_COL_ID') }}</th>
          <th scope="col">{{ Lang::txt('COM_COURSES_COL_TITLE') }}</th>
          <th scope="col">{{ Lang::txt('COM_COURSES_COL_TYPE') }}</th>
          <th scope="col">{{ Lang::txt('COM_COURSES_COL_STATE') }}</th>
          <th scope="col" colspan="3">{{ Lang::txt('COM_COURSES_COL_ORDERING') }}</th>
          <th scope="col">X</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id
                . '&scope=' . $filters['asset_scope']
                . '&scope_id=' . $filters['asset_scope_id']
                . '&course_id=' . $filters['course_id']
                . '&tmpl=' . $filters['tmpl'], false
            );
            $unlinkUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=unlink&asset=' . $row->id
                . '&scope=' . $filters['asset_scope']
                . '&scope_id=' . $filters['asset_scope_id']
                . '&course_id=' . $filters['course_id']
                . '&tmpl=' . $filters['tmpl']
                . '&' . Session::getFormToken() . '=1', false
            );
          @endphp
          <tr>
            <td>
              {{ $row->id }}
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm hidden" />
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="edit-asset link link-hover text-primary"
                   data-handler="iframe"
                   data-width="570"
                   data-height="550">
                  {{ $row->title }}
                </a>
              @else
                {{ $row->title }}
              @endif
            </td>
            <td>
              {{ $row->type }}
            </td>
            <td>
              @if($row->state == 2)
                <span class="badge badge-sm badge-error">
                  {{ Lang::txt('COM_COURSES_TRASHED') }}
                </span>
              @elseif($row->state == 1)
                <span class="badge badge-sm badge-success">
                  {{ Lang::txt('COM_COURSES_PUBLISHED') }}
                </span>
              @else
                <span class="badge badge-sm badge-ghost">
                  {{ Lang::txt('COM_COURSES_UNPUBLISHED') }}
                </span>
              @endif
            </td>
            <td>
              {!! $pageNav->orderUpIcon(
                  $i, ($row->ordering != ($rows[$i - 1]->ordering ?? null))
              ) !!}
            </td>
            <td>
              {!! $pageNav->orderDownIcon(
                  $i, $n, ($row->ordering != ($rows[$i + 1]->ordering ?? null))
              ) !!}
            </td>
            <td>
              {{ $row->ordering }}
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $unlinkUrl }}" class="btn btn-xs btn-error btn-outline">
                  <span>{{ Lang::txt('COM_COURSES_REMOVE') }}</span>
                </a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="course_id" value="{{ $filters['course_id'] }}" />
  <input type="hidden" name="tmpl" value="{{ $filters['tmpl'] }}" />
  <input type="hidden" name="scope" value="{{ $filters['asset_scope'] }}" />
  <input type="hidden" name="scope_id" value="{{ $filters['asset_scope_id'] }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" id="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />

  {!! Html::input('token') !!}
</form>
