{{--
  Groups Categories — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Groups\Helpers\Permissions::getActions('group');

  Toolbar::title(
      $group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_CATEGORIES'),
      'groups'
  );

  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_GROUPS_PAGES_CATEGORIES_CONFIRM_DELETE', 'delete');
  }
  Toolbar::spacer();
  Toolbar::custom('manage', 'config', 'config', 'COM_GROUPS_MANAGE', false);
@endphp

@include('com_groups::admin.views.pages.tmpl.menu')

@php
  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&gid=' . $group->cn, false
  );
@endphp

<form action="{{ $formAction }}" name="adminForm" id="adminForm" method="post">
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR') }}</th>
        </tr>
      </thead>
      <tbody>
        @if($rows->count() > 0)
          @foreach($rows as $k => $category)
            <tr>
              <td class="column-check">
                @if($canDo->get('core.edit'))
                  <input type="checkbox"
                         name="id[]"
                         id="cb{{ $k }}"
                         value="{{ $category->get('id') }}"
                         class="checkbox checkbox-sm"
                         aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $category->get('title')) }}"
                         data-check-item />
                @endif
              </td>
              <td>
                @if($canDo->get('core.edit'))
                  @php
                    $editUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=' . $controller
                        . '&gid=' . $group->cn
                        . '&task=edit&id=' . $category->get('id'), false
                    );
                  @endphp
                  <a href="{!! $editUrl !!}" class="link link-hover font-medium">
                    {{ $category->get('title') }}
                  </a>
                @else
                  {{ $category->get('title') }}
                @endif
              </td>
              <td class="priority-3">#{{ $category->get('color') }}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="3" class="text-center text-muted-foreground py-8">
              {{ Lang::txt('COM_GROUPS_PAGES_NO_CATEGORIES') }}
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="gid" value="{{ $group->cn }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
