{{--
  com_installer packages — Composer package list

  Variables: $packages, $filters, $total, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Installer\Admin\Helpers\Installer::getActions();
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_PACKAGES'));
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_installer');
      Toolbar::divider();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
      Toolbar::divider();
  }
  Toolbar::addNew();
  Toolbar::help('packages');
@endphp

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a class="active" href="{!! Route::url('index.php?option=' . $option . '&controller=packages', false) !!}">
        {{ Lang::txt('COM_INSTALLER_PACKAGES_PACKAGES') }}
      </a>
    </li>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=repositories', false) !!}">
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
          <th>Extension</th>
          <th class="priority-3">Installed Version</th>
          <th class="priority-4">Description</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($packages as $i => $package)
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="packages[]"
                     id="cb{{ $i }}"
                     value="{{ $package->getPrettyName() }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $package->getPrettyName() }}"
                     data-check-item />
            </td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&packageName=' . $package->getName(), false) }}"
                 class="link link-hover text-primary font-medium">
                {{ $package->getPrettyName() }}
              </a>
            </td>
            <td class="priority-3 text-sm tabular-nums">
              {{ $package->getFullPrettyVersion() }}
            </td>
            <td class="priority-4 text-sm text-muted-foreground">
              {{ $package->getDescription() }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-8 text-muted-foreground">
              No packages installed.
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
