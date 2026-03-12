{{--
  Articles — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canAdmin       = User::authorise('core.admin', 'com_content');
  $canCreate      = User::authorise('core.create', 'com_content');
  $canEdit        = User::authorise('core.edit', 'com_content');
  $canChangeState = User::authorise('core.edit.state', 'com_content');
  $canDelete      = User::authorise('core.delete', 'com_content');

  $sort      = $filters['sort'] ?? 'title';
  $sortDir   = $filters['sort_Dir'] ?? 'ASC';
  $saveOrder = ($sort === 'ordering');
  $userId    = User::get('id');

  Toolbar::title(Lang::txt('COM_CONTENT_ARTICLES_TITLE'), 'content');
  if ($canCreate) {
      Toolbar::addNew();
  }
  if ($canEdit) {
      Toolbar::editList();
  }
  Toolbar::spacer();
  if ($canChangeState) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
      Toolbar::archiveList();
      Toolbar::checkin();
  }
  if ($canDelete) {
      Toolbar::deleteList('', 'trash');
  }
  if ($canAdmin) {
      Toolbar::spacer();
      Toolbar::preferences($option, '550');
  }
  Toolbar::spacer();
  Toolbar::help('articles');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <select name="filter_published" id="filter_published"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
        <option value="1" @selected(($filters['published'] ?? '') === '1')>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="0" @selected(($filters['published'] ?? '') === '0')>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="2" @selected(($filters['published'] ?? '') === '2')>{{ Lang::txt('JARCHIVED') }}</option>
        <option value="-2" @selected(($filters['published'] ?? '') === '-2')>{{ Lang::txt('JTRASHED') }}</option>
      </select>

      <select name="filter_category_id" id="filter_category_id"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JOPTION_SELECT_CATEGORY') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_CATEGORY') }}</option>
        {!! Html::select('options', Html::category('options', 'com_content'), 'value', 'text', $filters['category_id'] ?? '') !!}
      </select>

      <select name="filter_access" id="filter_access"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $filters['access'] ?? '') !!}
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'JGLOBAL_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'JSTATUS', 'state', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'JCATEGORY', 'catid', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $sortDir, $sort) !!}</th>
          <th class="priority-4">{!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access_level', $sortDir, $sort) !!}</th>
          <th class="priority-5">{!! Html::grid('sort', 'JGRID_HEADING_CREATED_BY', 'created_by', $sortDir, $sort) !!}</th>
          <th class="priority-5">{!! Html::grid('sort', 'JDATE', 'created', $sortDir, $sort) !!}</th>
          <th class="priority-6 text-right">{!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
            <div class="admin-pagination">
              {!! $pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($items as $i => $item)
          @php
            $canEditItem = User::authorise('core.edit', 'com_content.article.' . $item->id);
            $canEditOwn  = User::authorise('core.edit.own', 'com_content.article.' . $item->id)
                && $item->created_by == $userId;
            $canCheckin  = User::authorise('core.manage', 'com_checkin')
                || $item->checked_out == $userId
                || $item->checked_out == 0;
            $canChange   = User::authorise('core.edit.state', 'com_content.article.' . $item->id) && $canCheckin;

            $states = [
                1  => ['badge-success', Lang::txt('JPUBLISHED')],
                0  => ['badge-ghost',   Lang::txt('JUNPUBLISHED')],
                2  => ['badge-info',    Lang::txt('JARCHIVED')],
                -2 => ['badge-warning', Lang::txt('JTRASHED')],
            ];
            $stateVal = (int)$item->get('state');
            [$stateCls, $stateText] = $states[$stateVal] ?? ['badge-ghost', Lang::txt('JUNKNOWN')];

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $item->id,
                false, false
            );

            $authorName = $item->get('author_name', Lang::txt('JUNKNOWN'));
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="cid[]"
                     id="cb{{ $i }}"
                     value="{{ $item->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td>
              @if($item->checked_out && $item->checked_out != $userId)
                <span class="badge badge-xs badge-warning" title="{{ Lang::txt('JLIB_HTML_CHECKED_OUT') }}">
                  &#128274;
                </span>
              @endif
              @if($canEditItem || $canEditOwn)
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $item->title }}
                </a>
              @else
                {{ $item->title }}
              @endif
              <p class="text-xs text-muted-foreground">
                {!! Lang::txt('JGLOBAL_LIST_ALIAS', e($item->alias)) !!}
              </p>
            </td>
            <td>
              @if($item->featured)
                <span class="badge badge-xs badge-accent mr-1"
                      title="{{ Lang::txt('JFEATURED') }}">&#9733;</span>
              @endif
              <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
            </td>
            <td class="priority-2">
              {{ $item->get('category_title') ?: Lang::txt('JNONE') }}
            </td>
            <td class="priority-3">
              @if($canChange && $saveOrder)
                <input type="text"
                       name="order[{{ $item->get('catid', 0) }}][{{ $item->get('id', 0) }}]"
                       size="5"
                       value="{{ $item->ordering }}"
                       class="input input-bordered input-xs w-16 text-center" />
              @else
                {{ $item->ordering }}
              @endif
            </td>
            <td class="priority-4">
              {{ $item->get('access_level') ?? '' }}
            </td>
            <td class="priority-5">
              @if($item->created_by_alias)
                {{ $authorName }}
                <p class="text-xs text-muted-foreground">
                  {!! Lang::txt('JGLOBAL_LIST_ALIAS', e($item->created_by_alias)) !!}
                </p>
              @else
                {{ $item->created_by ? e($authorName) : Lang::txt('JUNKNOWN') }}
              @endif
            </td>
            <td class="priority-5">
              <time datetime="{{ $item->created }}">
                {{ Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')) }}
              </time>
            </td>
            <td class="priority-6 text-right">{{ $item->id }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($saveOrder && $canChangeState)
    <div class="mt-2 flex justify-end">
      <button type="submit" class="btn btn-sm btn-primary"
              data-task="saveorder">
        {{ Lang::txt('JGRID_HEADING_SAVE_ORDER') }}
      </button>
    </div>
  @endif

  {{-- Batch processing --}}
  @if($canCreate && $canEdit && $canChangeState)
    @include('com_content::admin.views.articles.tmpl._batch')
  @endif
</x-admin-form>
