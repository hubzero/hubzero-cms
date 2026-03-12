{{--
  Article Picker Modal — used by other components to select articles

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $function  = Request::getCmd('function', 'jSelectArticle');
  $sort      = $filters['sort'] ?? 'title';
  $sortDir   = $filters['sort_Dir'] ?? 'ASC';
  $token     = Session::getFormToken();

  $formAction = Route::url(
      'index.php?option=com_content&view=articles&layout=modal'
      . '&tmpl=component&function=' . e($function)
      . '&' . $token . '=1',
      false, false
  );
@endphp

<h2 class="text-lg font-bold mb-4">{{ Lang::txt('COM_CONTENT_SELECT_AN_ARTICLE') }}</h2>

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <div class="flex flex-wrap gap-2 mb-4">
    <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
    <input type="text"
           name="filter_search"
           id="filter_search"
           class="input input-bordered input-sm w-48"
           value="{{ $filters['search'] ?? '' }}"
           placeholder="{{ Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC') }}" />
    <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
    <button type="button" class="btn btn-sm btn-ghost"
            data-clear-search="filter_search">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>

    <select name="filter_access" class="select select-bordered select-sm"
            aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}"
            data-submit-on-change>
      <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
      {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $filters['access'] ?? '') !!}
    </select>

    <select name="filter_published" class="select select-bordered select-sm"
            aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}"
            data-submit-on-change>
      <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
      {!! Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $filters['published'] ?? '', true) !!}
    </select>

    <select name="filter_category_id" class="select select-bordered select-sm"
            aria-label="{{ Lang::txt('JOPTION_SELECT_CATEGORY') }}"
            data-submit-on-change>
      <option value="">{{ Lang::txt('JOPTION_SELECT_CATEGORY') }}</option>
      {!! Html::select('options', Html::category('options', 'com_content'), 'value', 'text', $filters['category_id'] ?? '') !!}
    </select>
  </div>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{!! Html::grid('sort', 'JGLOBAL_TITLE', 'a.title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access_level', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'JCATEGORY', 'a.catid', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'JDATE', 'a.created', $sortDir, $sort) !!}</th>
          <th class="text-right">{!! Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($items as $i => $item)
          @php
            $fnEsc = $function;
          @endphp
          <tr>
            <td>
              <a href="#"
                 class="link link-hover text-primary"
                 data-article-select
                 data-function="{{ $fnEsc }}"
                 data-id="{{ $item->id }}"
                 data-title="{{ $item->title }}"
                 data-catid="{{ $item->catid }}">
                {{ $item->title }}
              </a>
            </td>
            <td>{{ $item->accessLevel->title ?? '' }}</td>
            <td>{{ $item->category->title ?? '' }}</td>
            <td>
              <time datetime="{{ $item->created }}">
                {{ Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')) }}
              </time>
            </td>
            <td class="text-right">{{ (int) $item->id }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
  {!! Html::input('token') !!}
</form>
