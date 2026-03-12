{{--
  Solr Search — Admin overview/status

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('solr');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_SOLR_OVERVIEW') }}"
    icon="search"
    option="{{ $option }}"
>
  <x-slot:buttons>
    <button type="button"
            class="btn btn-sm"
            data-task="optimize">
      {{ Lang::txt('COM_SEARCH_SOLR_OPTIMIZE') }}
    </button>
  </x-slot:buttons>
</x-admin-toolbar>

@include('com_search::admin.views.shared.tmpl._submenu')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Solr Status --}}
  <x-admin-fieldset legend="Solr Status">
      @if ($status === true)
        <div role="alert" class="alert alert-success mb-4">
          <span>The search engine is responding.</span>
        </div>
      @else
        <div role="alert" class="alert alert-error mb-4">
          <span>{{ Lang::txt('COM_SEARCH_NOT_RESPONDING') }}</span>
          <span>{{ Lang::txt('COM_SEARCH_CHECK_CONFIG') }}</span>
        </div>
      @endif

      <table class="admin-meta">
        <tbody>
          <tr>
            <td>Last Document Insert</td>
            <td>{{ $lastInsert }}</td>
          </tr>
          <tr>
            <td>Mechanism</td>
            <td>{{ ucfirst($mechanism) }}</td>
          </tr>
        </tbody>
      </table>
  </x-admin-fieldset>

  {{-- Quick Links --}}
  <x-admin-fieldset legend="Management" body-class="prose prose-sm max-w-none">
      <ul>
        <li>
          <a href="{!! Route::url('index.php?option=' . $option . '&controller=searchable&task=display', false) !!}">
            Searchable Components
          </a>
          — manage which components are indexed
        </li>
        <li>
          <a href="{!! Route::url('index.php?option=' . $option . '&controller=solr&task=manageBlacklist', false) !!}">
            Index Blacklist
          </a>
          — documents excluded from results
        </li>
        <li>
          <a href="{!! Route::url('index.php?option=' . $option . '&controller=boosts&task=list', false) !!}">
            Boosts
          </a>
          — configure search result ranking
        </li>
      </ul>
  </x-admin-fieldset>

</div>

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">
  <input type="hidden" name="option" value="com_search" />
  <input type="hidden" name="controller" value="solr" />
  <input type="hidden" name="task" value="searchIndex" />
  {!! Html::input('token') !!}
</form>
