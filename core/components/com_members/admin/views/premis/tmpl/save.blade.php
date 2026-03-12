{{--
  com_members — PREMIS import results

  Variables: $ok (int), $fail (int), $report (array of line results), $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Members\Helpers\Permissions::getActions('component');

  Toolbar::title(
      Lang::txt('COM_MEMBERS_REGISTRATION') . ': ' . Lang::txt('COM_MEMBERS_PREMIS'),
      'user'
  );
  if ($canDo->get('core.edit')) {
      Toolbar::addNew();
      Toolbar::editList();
      Toolbar::deleteList();
  }
@endphp

@if ($__view->getError())
  <div class="alert alert-error">
    <p>{!! implode('<br />', $__view->getErrors()) !!}</p>
  </div>
@else
  <div class="alert alert-success">
    <p>{{ Lang::txt('Import complete') }}</p>
  </div>

  <div class="bg-base-100 rounded-box border border-base-300 p-4">
    <p>
      Total records processed: <strong>{{ $ok + $fail }}</strong><br />
      Successfully processed: <strong>{{ $ok }}</strong><br />
      Errors processing: <strong>{{ $fail }}</strong>
    </p>

    @if ($fail)
      <h4>Error log:</h4>
      <div id="report">
        @foreach ($report as $line)
          @if ($line['status'] != 'ok')
            <p>Line {{ $line['line'] }}: {{ $line['msg'] }}</p>
          @endif
        @endforeach
      </div>
    @endif
  </div>
@endif
