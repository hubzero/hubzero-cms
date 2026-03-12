{{--
  Tagged items — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Tags\Helpers\Permissions::getActions();
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';
  $tagId   = $filters['tagid'] ?? 0;
  $colSpan = $tagId ? 6 : 7;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_TAGS') }}: {{ Lang::txt('COM_TAGS_TAGGED') }}"
    icon="tags"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter-tbl" class="sr-only">{{ Lang::txt('COM_TAGS_FILTER_TYPE') }}</label>
        <select name="tbl"
                id="filter-tbl"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="" @selected(!($filters['tbl'] ?? ''))>
            {{ Lang::txt('COM_TAGS_FILTER_TYPE') }}
          </option>
          @foreach ($types as $type)
            <option value="{{ $type->get('tbl') }}"
                    @selected(($filters['tbl'] ?? '') == $type->get('tbl'))>
              {{ $type->get('tbl') }}
            </option>
          @endforeach
        </select>
      @endslot
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    @if ($tagId)
      @php
        $tagObj  = \Components\Tags\Models\Tag::oneOrFail($tagId);
        $rawTag  = e($tagObj->get('raw_tag'));
        $tagNorm = $tagObj->get('tag');
      @endphp
      <caption>
        {{ Lang::txt('COM_TAGS_TAG') }}: {{ $rawTag }} ({{ $tagNorm }})
      </caption>
    @endif
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox" data-check-all aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_TAGS_COL_ID', 'id', $sortDir, $sort) !!}
        </th>
        @if (!$tagId)
          <th scope="col">
            {!! Html::grid('sort', 'COM_TAGS_COL_TAGID', 'tagid', $sortDir, $sort) !!}
          </th>
        @endif
        <th scope="col">
          {!! Html::grid('sort', 'COM_TAGS_COL_TBL', 'tbl', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_TAGS_COL_OBJECTID', 'objectid', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_TAGS_COL_CREATED', 'taggedon', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_TAGS_COL_CREATED_BY', 'taggerid', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->get('id'),
              false, false
          );
          $taggedon = $row->get('taggedon');
        @endphp
        <tr>
          <td>
            @if ($canDo->get('core.edit'))
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     aria-label="{{ $row->get('tbl') }} #{{ $row->get('objectid') }}"
                     data-check-item />
            @endif
          </td>
          <td class="priority-5">
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">{{ $row->get('id') }}</a>
            @else
              {{ $row->get('id') }}
            @endif
          </td>
          @if (!$tagId)
            <td>
              @if ($canDo->get('core.edit'))
                <a href="{{ $editUrl }}">{{ $row->get('tagid') }}</a>
              @else
                {{ $row->get('tagid') }}
              @endif
            </td>
          @endif
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">{{ $row->get('tbl') }}</a>
            @else
              {{ $row->get('tbl') }}
            @endif
          </td>
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">{{ $row->get('objectid') }}</a>
            @else
              {{ $row->get('objectid') }}
            @endif
          </td>
          <td class="priority-3">
            <time datetime="{{ $taggedon }}">
              {{ ($taggedon && $taggedon != '0000-00-00 00:00:00')
                  ? $taggedon : Lang::txt('COM_TAGS_UNKNOWN') }}
            </time>
          </td>
          <td class="priority-4">
            @if ($row->get('taggerid'))
              @php
                $memberUrl = Route::url(
                    'index.php?option=com_members&controller=members&task=edit&id='
                    . $row->get('taggerid'),
                    false, false
                );
              @endphp
              <a href="{{ $memberUrl }}">
                {{ $row->creator->get('name', Lang::txt('COM_TAGS_UNKNOWN')) }}
              </a>
            @else
              {{ Lang::txt('COM_TAGS_UNKNOWN') }}
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="{{ $colSpan }}">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="tag" value="{{ $tagId }}" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
</x-admin-form>
