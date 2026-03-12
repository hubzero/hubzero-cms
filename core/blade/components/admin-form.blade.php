{{--
  Admin list/display view wrapper.

  Wraps the table, filter bar, and hidden fields for admin list views.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@props([
    'option'     => '',
    'controller' => '',
    'sort'       => '',
    'sortDir'    => 'asc',
])

@php
  $formUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller,
      false, false
  );
@endphp

<form action="{{ $formUrl }}"
      method="post"
      name="adminForm"
      id="adminForm">

  @if(isset($filters) && $filters->isNotEmpty())
    <div class="admin-filter-bar">
      {{ $filters }}
    </div>
  @endif

  {{ $slot }}

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />

  {!! \Hubzero\Facades\Html::input('token') !!}
</form>
