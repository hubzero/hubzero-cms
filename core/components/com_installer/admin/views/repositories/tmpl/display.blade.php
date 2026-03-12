{{--
  com_installer repositories — Composer repository list

  Variables: $repositories, $filters, $total, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Utility\Arr;

  $canDo   = \Components\Installer\Admin\Helpers\Installer::getActions();
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_REPOSITORIES'));
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_installer');
      Toolbar::divider();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
      Toolbar::spacer();
  }
  Toolbar::help('repositories');
@endphp

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=packages', false) !!}">
        {{ Lang::txt('COM_INSTALLER_PACKAGES_PACKAGES') }}
      </a>
    </li>
    <li>
      <a class="active" href="{!! Route::url('index.php?option=' . $option . '&controller=repositories', false) !!}">
        {{ Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORIES') }}
      </a>
    </li>
  </ul>
</nav>

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post" name="adminForm" id="updateRepositoryForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox" name="toggle" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_INSTALLER_COL_REPO', 'repo', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_INSTALLER_COL_TYPE', 'type', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_INSTALLER_COL_DESCRIPTION', 'description', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_INSTALLER_COL_URL', 'url', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php $id = 1; @endphp
        @forelse ($repositories as $alias => $config)
          @if ($alias === 'hz-installer' || $alias === 'packagist.org')
            @continue
          @endif
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="repositories[]"
                     id="cb{{ $id }}"
                     value="{{ $alias }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Arr::getValue($config, 'name', '') }}"
                     data-check-item />
            </td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&alias=' . $alias, false) }}"
                 class="link link-hover text-primary font-medium">
                {{ Arr::getValue($config, 'name', '') }}
              </a>
              <br>
              <span class="text-xs font-mono text-muted-foreground">{{ $alias }}</span>
            </td>
            <td class="text-sm">
              <span class="badge badge-sm badge-ghost">{{ Arr::getValue($config, 'type', '') }}</span>
            </td>
            <td class="text-sm text-muted-foreground">{{ Arr::getValue($config, 'description', '') }}</td>
            <td class="text-sm font-mono text-xs text-muted-foreground max-w-xs truncate">
              {{ Arr::getValue($config, 'url', '') }}
            </td>
          </tr>
          @php $id++; @endphp
        @empty
          <tr>
            <td colspan="5" class="text-center py-8 text-muted-foreground">
              No custom repositories configured.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $filters['sort'] }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $filters['sort_Dir'] }}" />
  {!! Html::input('token') !!}
</form>
