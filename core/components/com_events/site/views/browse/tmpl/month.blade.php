{{--
  Month view - shows all events for the current month in a list
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$monthUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year . '&month=' . $month
);
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
            ->set('formAction', $monthUrl)
            ->loadTemplate() !!}
    @endslot

    {{-- Tab navigation --}}
    {!! $__view->view('_nav')
        ->set('option', $option)->set('task', $task)
        ->set('year', $year)->set('month', $month)->set('day', $day)
        ->set('authorized', $authorized)
        ->loadTemplate() !!}

    @if (count($rows) > 0)
        <ul class="list-none">
            @foreach ($rows as $row)
                {!! $__view->view('item')
                    ->set('option', $option)->set('task', $task)
                    ->set('row', $row)->set('fields', $fields)
                    ->set('categories', $categories)->set('showdate', 1)
                    ->loadTemplate() !!}
            @endforeach
        </ul>
    @else
        <div class="alert">
            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') }}
            <strong>{{ \Components\Events\Helpers\Html::getDateFormat($year, $month, '', 3) }}</strong>
        </div>
    @endif
</x-page-container>
