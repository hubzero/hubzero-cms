{{--
  com_installer migrations — Pending/completed migrations list

  Variables: $rows, $filters, $total, $breadcrumb, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

  Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_MIGRATIONS'));
  if ($canDo->get('core.edit.state')) {
      Toolbar::custom('runup',   'up',    '', 'COM_INSTALLER_TOOLBAR_MIGRATE_UP');
      Toolbar::custom('rundown', 'down',  '', 'COM_INSTALLER_TOOLBAR_MIGRATE_DOWN');
      Toolbar::spacer();
      Toolbar::custom('migrate', 'purge', '', 'COM_INSTALLER_TOOLBAR_MIGRATE_PENDING', false);
  }
@endphp

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=manage', false) !!}">
        {{ Lang::txt('COM_INSTALLER_SUBMENU_CORE_EXTENSIONS') }}
      </a>
    </li>
    <li>
      <a class="active" href="{!! Route::url('index.php?option=' . $option . '&controller=migrations', false) !!}">
        {{ Lang::txt('COM_INSTALLER_SUBMENU_MIGRATIONS') }}
      </a>
    </li>
  </ul>
</nav>

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post" name="adminForm" id="adminForm">

  @if (!empty($breadcrumb))
    <div class="flex items-center gap-2 mb-4 text-sm">
      <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&folder=', false) }}"
         class="link link-hover text-primary">
        {{ Lang::txt('JGLOBAL_FILTER_TYPE_LABEL') }}
      </a>
      <span class="text-muted-foreground" aria-hidden="true">/</span>
      <span>{{ $breadcrumb }}</span>
    </div>
  @endif

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox" name="toggle" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('COM_INSTALLER_HEADING_EXTENSION') }}</th>
          <th class="priority-3">{{ Lang::txt('JDATE') }}</th>
          <th>{{ Lang::txt('COM_INSTALLER_HEADING_FILENAME') }}</th>
          <th>{{ Lang::txt('COM_INSTALLER_HEADING_STATUS') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_INSTALLER_HEADING_DESCRIPTION') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach ($rows as $i => $row)
          @php
            $parts = explode('/', $row['entry']);
            $file  = array_pop($parts);
            $scope = implode('/', $parts);
            $isCore = ($parts[0] == 'core');

            $item      = ltrim($file, 'Migration');
            $date      = Date::of(strtotime(substr($item, 0, 14) . 'UTC'))->format('Y-m-d g:i:sa');
            $component = substr($item, 14, -4);
            $scopePath = str_replace('/migrations', '', $scope);

            $desc = '';
            if (is_file(PATH_ROOT . DS . $row['entry'])) {
                if (!class_exists(substr($file, 0, -4))) {
                    require_once PATH_ROOT . DS . $row['entry'];
                }
                $refClass = new ReflectionClass(substr($file, 0, -4));
                $desc = trim(rtrim(ltrim($refClass->getDocComment(), "/**\n *"), '**/'));
            } else {
                $desc = '<span class="text-warning">' . Lang::txt('COM_INSTALLER_MSG_MIGRATIONS_FILE_NOT_FOUND') . '</span>';
            }

            $scopeCls  = $isCore ? 'text-warning' : 'text-info';
            $scopeUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&folder=' . urlencode($scopePath), false);
            $token     = Session::getFormToken();
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="migration[]"
                     id="cb{{ $i }}"
                     value="{{ $file }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $component }}"
                     data-check-item />
            </td>
            <td>
              <span class="font-medium">{{ $component }}</span><br>
              <a href="{{ $scopeUrl }}" class="text-xs link link-hover {{ $scopeCls }}">
                {{ $scopePath }}
              </a>
            </td>
            <td class="priority-3 text-sm tabular-nums">{{ $date }}</td>
            <td class="text-sm font-mono">{{ basename($row['entry']) }}</td>
            <td>
              @if ($row['status'] === 'pending')
                @php
                  $migrateUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=migrate&file=' . $file, false) . '&' . $token . '=1';
                @endphp
                <a href="{{ $migrateUrl }}">
                  <span class="badge badge-sm badge-warning">{{ $row['status'] }}</span>
                </a>
              @elseif ($row['status'] === 'complete')
                <span class="badge badge-sm badge-success">{{ $row['status'] }}</span>
              @else
                <span class="badge badge-sm badge-ghost">{{ $row['status'] }}</span>
              @endif
            </td>
            <td class="priority-4 text-sm">{!! $desc !!}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="folder" value="{{ urlencode($filters['folder']) }}" />
  {!! Html::input('token') !!}
</form>
