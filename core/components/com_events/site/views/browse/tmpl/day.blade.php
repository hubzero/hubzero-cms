{{--
  Day view - shows all events for a single day
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$dayFormUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year
    . '&month=' . $month . '&day=' . $day
);
$dateStr = $year . '-' . $month . '-' . $day . ' 00:00:00';
$dateFmt = \Hubzero\Facades\Lang::txt('DATE_FORMAT_HZ1');
@endphp

<x-page-container :title="$title">
    @slot('actions')
        @if ($authorized)
            <a class="btn btn-primary btn-sm"
                href="{{ \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=add') }}">
                {{ \Hubzero\Facades\Lang::txt('EVENTS_ADD_EVENT') }}
            </a>
        @endif
    @endslot

    @slot('sidebar')
        {!! $__view->view('_sidebar')
            ->set('option', $option)->set('task', $task)
            ->set('year', $year)->set('month', $month)->set('day', $day)
            ->set('offset', $offset)->set('category', $category)
            ->set('categories', $categories)
            ->set('formAction', $dayFormUrl)
            ->loadTemplate() !!}
    @endslot

    {{-- Tab navigation --}}
    {!! $__view->view('_nav')
        ->set('option', $option)->set('task', $task)
        ->set('year', $year)->set('month', $month)->set('day', $day)
        ->set('authorized', $authorized)
        ->loadTemplate() !!}

    <h3 class="text-lg font-semibold mb-4">
        {{ \Hubzero\Facades\Date::of($dateStr)->format($dateFmt) }}
    </h3>

    @if (count($rows) > 0)
        <ul class="list-none">
            @foreach ($rows as $row)
                {!! $__view->view('item')
                    ->set('option', $option)->set('task', $task)
                    ->set('row', $row)->set('fields', $fields)
                    ->set('categories', $categories)->set('showdate', 0)
                    ->loadTemplate() !!}
            @endforeach
        </ul>
    @else
        <div class="alert">
            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') }}
            <strong>{{ \Components\Events\Helpers\Html::getDateFormat($year, $month, $day, 0) }}</strong>
        </div>
    @endif
</x-page-container>
