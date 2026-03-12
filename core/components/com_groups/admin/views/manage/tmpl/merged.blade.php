{{--
  Groups — Merge code results

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $__view->css();

  Toolbar::title(Lang::txt('COM_GROUPS'), 'groups');
  Toolbar::custom('display', 'back', 'back', 'COM_GROUPS_BACK', false);
@endphp

@php
  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller, false
  );
@endphp

<form action="{{ $formAction }}" method="post" name="adminForm" id="adminForm">

  @if(!empty($success))
    <table class="admin-table success">
      <thead>
        <tr>
          <th scope="col">{{ Lang::txt('COM_GROUPS_PULL_SUCCESS') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($success as $item)
          @php
            $group = \Hubzero\User\Group::getInstance($item['group']);
          @endphp
          <tr>
            <td>
              <strong>{{ $group->get('description') }} ({{ $group->get('cn') }})</strong>
              <br /><br />
              <pre>{{ $item['message'] }}</pre>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <br /><br />

  @if(!empty($failed))
    <table class="admin-table failed">
      <thead>
        <tr>
          <th scope="col">{{ Lang::txt('COM_GROUPS_PULL_FAIL') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($failed as $item)
          @php
            $group = \Hubzero\User\Group::getInstance($item['group']);
          @endphp
          <tr>
            <td>
              <strong>{{ $group->get('description') }} ({{ $group->get('cn') }})</strong>
              <br /><br />
              <pre>{{ $item['message'] }}</pre>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
