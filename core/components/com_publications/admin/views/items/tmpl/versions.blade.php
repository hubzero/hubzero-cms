{{--
  Publications — Version history list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $mgrUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $editUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=edit&id[]=' . $pub->id, false
  );

  Toolbar::title(
      Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER')
      . ' - ' . Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ': #' . $pub->id
      . ' - ' . Lang::txt('COM_PUBLICATIONS_VERSIONS'),
      'publications'
  );
  Toolbar::spacer();
  Toolbar::cancel();

  $__view->css();

  // Status badge helper
  $statusMap = [
      0  => ['badge-ghost',     'COM_PUBLICATIONS_VERSION_UNPUBLISHED'],
      1  => ['badge-success',   'COM_PUBLICATIONS_VERSION_PUBLISHED'],
      2  => ['badge-error',     'COM_PUBLICATIONS_VERSION_DELETED'],
      3  => ['badge-info',      'COM_PUBLICATIONS_VERSION_DRAFT'],
      4  => ['badge-accent',    'COM_PUBLICATIONS_VERSION_READY'],
      5  => ['badge-warning',   'COM_PUBLICATIONS_VERSION_PENDING'],
      7  => ['badge-neutral',   'COM_PUBLICATIONS_VERSION_WIP'],
      10 => ['badge-secondary', 'COM_PUBLICATIONS_VERSION_PRESERVING'],
  ];
@endphp

{{-- Breadcrumb --}}
<nav class="text-sm breadcrumbs mb-4">
  <ul>
    <li><a href="{!! $mgrUrl !!}">{{ Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER') }}</a></li>
    <li><a href="{!! $editUrl !!}">{{ Lang::txt('COM_PUBLICATIONS_PUBLICATION') }} #{{ $pub->id }}</a></li>
    <li>{{ Lang::txt('COM_PUBLICATIONS_VERSIONS') }}</li>
  </ul>
</nav>

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{{ Lang::txt('COM_PUBLICATIONS_VERSION') }}</th>
          <th>{{ Lang::txt('COM_PUBLICATIONS_ID') }}</th>
          <th>{{ Lang::txt('COM_PUBLICATIONS_FIELD_VERSION') }}</th>
          <th>{{ Lang::txt('COM_PUBLICATIONS_TITLE') }}</th>
          <th>{{ Lang::txt('COM_PUBLICATIONS_FIELD_STATUS') }}</th>
          <th>{{ Lang::txt('COM_PUBLICATIONS_DOI') }}</th>
          <th>{{ Lang::txt('COM_PUBLICATIONS_OPTIONS') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($versions as $v)
          @php
            $statusBadge = $statusMap[$v->state ?? 0] ?? ['badge-ghost', 'COM_PUBLICATIONS_VERSION_UNPUBLISHED'];
            $statusLabel = Lang::txt($statusBadge[1]);
            $statusClass = $statusBadge[0];
            $date        = $pub->getStatusDate($v);
            $doiDisplay  = $v->doi ? 'doi:' . $v->doi : Lang::txt('COM_PUBLICATIONS_NA');
            $vEditUrl    = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id[]=' . $pub->id
                . '&version=' . $v->version_number, false
            );
          @endphp
          <tr class="{{ $v->main == 1 ? 'font-semibold' : '' }}">
            <td class="text-center">{{ $v->version_number ?: '' }}</td>
            <td>{{ $v->id }}</td>
            <td>{{ $v->version_label }}</td>
            <td>{{ $v->title }}</td>
            <td>
              <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
              @if($date)
                <span class="block text-xs text-muted-foreground mt-0.5">{{ $date }}</span>
              @endif
            </td>
            <td class="text-sm">{{ $doiDisplay }}</td>
            <td>
              <a href="{!! $vEditUrl !!}" class="btn btn-xs btn-ghost">
                {{ Lang::txt('COM_PUBLICATIONS_MANAGE_VERSION') }}
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
