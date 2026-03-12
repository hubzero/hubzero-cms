{{--
  Activity — Admin chart view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  Toolbar::title(Lang::txt('COM_ACTIVITY_TITLE'), 'activity');
  if (User::authorise('core.admin', $option)) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  Toolbar::spacer();
  Toolbar::help('entries');

  Html::behavior('chart');

  $__view->css();
  $__view->js();

  // Build chart data points
  $chartPoints = '';
  $top = 0;
  if ($data) {
      $points = [];
      foreach ($data as $k => $v) {
          $top = $v > $top ? $v : $top;
          $points[] = '[' . Date::of($k)->toUnix() . ',' . $v . ']';
      }
      $chartPoints = implode(',', $points);
  }
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 p-6">
    <h3 class="text-lg font-semibold mb-1">{{ Lang::txt('COM_ACTIVITY_RECENT') }}</h3>
    <p class="text-sm text-muted-foreground mb-4">
      {{ Lang::txt('COM_ACTIVITY_TITLE') }} — {{ Lang::txt('COM_ACTIVITY_LAST_DAYS', 'Last 28 days') }}
    </p>

    <div id="container1"
         class="{{ $option }}-chart chart min-h-[300px]"
         data-datasets="{{ $option }}-data"></div>

    @if($total > 0)
      <div class="mt-4 pt-4 border-t border-base-300">
        <span class="text-2xl font-bold text-primary">{{ number_format($total) }}</span>
        <span class="text-sm text-muted-foreground ml-1">{{ Lang::txt('COM_ACTIVITY_TOTAL_EVENTS', 'total events') }}</span>
      </div>
    @endif
  </div>

  <template id="{{ $option }}-data">
    {
      "datasets": [
        {
          "color": "orange",
          "label": "{{ Lang::txt('COM_ACTIVITY_RECENT') }}",
          "data": [{!! $chartPoints !!}]
        }
      ]
    }
  </template>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />

  {!! Html::input('token') !!}
</form>
