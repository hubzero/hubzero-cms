{{--
  Tool Versions — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'desc';

  Toolbar::title(Lang::txt('COM_TOOLS'), 'tools');
  Toolbar::preferences($option, '550');
  Toolbar::spacer();
  Toolbar::help('versions');

  $__view->css('tools.css');

  $stateMap = [
    0 => ['label' => Lang::txt('JUNPUBLISHED'),        'badge' => 'badge-ghost'],
    1 => ['label' => Lang::txt('COM_TOOLS_REGISTERED'), 'badge' => 'badge-info'],
    2 => ['label' => Lang::txt('COM_TOOLS_CREATED'),    'badge' => 'badge-info'],
    3 => ['label' => Lang::txt('COM_TOOLS_UPLOADED'),   'badge' => 'badge-info'],
    4 => ['label' => Lang::txt('COM_TOOLS_INSTALLED'),  'badge' => 'badge-info'],
    5 => ['label' => Lang::txt('COM_TOOLS_UPDATED'),    'badge' => 'badge-warning'],
    6 => ['label' => Lang::txt('COM_TOOLS_APPROVED'),   'badge' => 'badge-accent'],
    7 => ['label' => Lang::txt('JPUBLISHED'),           'badge' => 'badge-success'],
    8 => ['label' => Lang::txt('COM_TOOLS_RETIRED'),    'badge' => 'badge-neutral'],
    9 => ['label' => Lang::txt('COM_TOOLS_ABANDONED'),  'badge' => 'badge-error'],
  ];

  $pipelineUrl = Route::url('index.php?option=' . $option . '&controller=pipeline', false);
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <caption class="text-left px-4 py-2 text-sm text-muted-foreground">
        <a href="{{ $pipelineUrl }}" class="link link-hover">{{ Lang::txt('COM_TOOLS_PIPELINE') }}</a>
        &rsaquo; {{ $tool->title }} ({{ $tool->toolname }})
      </caption>
      <thead>
        <tr>
          <th class="column-check"></th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_TOOLS_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_COL_INSTANCE', 'toolname', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_TOOLS_COL_VERSION', 'version', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_TOOLS_COL_REVISION', 'revision', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_COL_STATE', 'state', $sortDir, $sort) !!}
          </th>
          @if($config->get('new_doi'))
            <th>{{ Lang::txt('COM_TOOLS_COL_DOI') }}</th>
          @endif
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="{{ $config->get('new_doi') ? 7 : 6 }}">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $state   = $stateMap[$row['state']] ?? ['label' => $row['state'], 'badge' => 'badge-ghost'];
            $editUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&version=' . $row['id'] . '&id=' . $tool->id, false);
          @endphp
          <tr>
            <td>
              <input type="radio"
                     name="id"
                     id="cb{{ $i }}"
                     value="{{ $row['id'] }}"
                     class="radio radio-sm"
                     aria-label="{{ $row['id'] }}" />
            </td>
            <td class="priority-5">{{ $row['id'] }}</td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">
                {{ $row['instance'] }}
              </a>
            </td>
            <td class="priority-4">{{ $row['version'] }}</td>
            <td class="priority-3">{{ $row['revision'] }}</td>
            <td>
              <span class="badge badge-sm {{ $state['badge'] }}">{{ $state['label'] }}</span>
            </td>
            @if($config->get('new_doi'))
              <td class="font-mono text-sm">{{ $row['doi'] }}</td>
            @endif
          </tr>
        @empty
          <tr>
            <td colspan="{{ $config->get('new_doi') ? 7 : 6 }}" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
