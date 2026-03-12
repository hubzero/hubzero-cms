{{--
  Citation Types — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Citations\Helpers\Permissions::getActions('type');

  Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_TYPES'), 'citations');
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::spacer();
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::help('types');
@endphp

<form action="{{ Route::url('index.php?option=' . $option, false) }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <table class="admin-table">
    <thead>
      <tr>
        <th class="admin-table-col-check">
          <input type="checkbox" name="checkall-toggle" id="checkall-toggle"
                 value="" class="checkbox-toggle toggle-all" />
          <label for="checkall-toggle" class="sr-only visually-hidden">
            {{ Lang::txt('JGLOBAL_CHECK_ALL') }}
          </label>
        </th>
        <th scope="col">{{ Lang::txt('CITATION_TYPES_ID') }}</th>
        <th scope="col">{{ Lang::txt('CITATION_TYPES_ALIAS') }}</th>
        <th scope="col">{{ Lang::txt('CITATION_TYPES_TITLE') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($types as $i => $t)
        @php
          $editUrl = Route::url(
              'index.php?option=' . $option . '&controller=' . $controller
              . '&task=edit&id=' . $t['id'],
              false, false
          );
        @endphp
        <tr>
          <td>
            <input type="checkbox" name="id[]" id="cb{{ $i }}"
                   value="{{ $t['id'] }}" class="checkbox-toggle" />
            <label for="cb{{ $i }}" class="sr-only visually-hidden">{{ $t['id'] }}</label>
          </td>
          <td>{{ $t['id'] }}</td>
          <td><a href="{{ $editUrl }}">{{ $t['type'] }}</a></td>
          <td><a href="{{ $editUrl }}">{{ $t['type_title'] }}</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />

  {!! Html::input('token') !!}
</form>
