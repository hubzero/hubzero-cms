{{--
  com_menus — Admin menu list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Menus\Helpers\Menus::getActions($filters['parent_id'] ?? 0);
  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $return  = base64_encode(Request::current());

  Toolbar::title(Lang::txt('COM_MENUS_VIEW_MENUS_TITLE'), 'menumgr');
  if ($canDo->get('core.create')) Toolbar::addNew('add');
  if ($canDo->get('core.edit'))   Toolbar::editList('edit');
  if ($canDo->get('core.delete')) {
      Toolbar::divider();
      Toolbar::deleteList(Lang::txt('COM_MENUS_MENU_CONFIRM_DELETE'), 'remove');
  }
  Toolbar::divider();
  Toolbar::custom('rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
  if ($canDo->get('core.admin')) {
      Toolbar::divider();
      Toolbar::preferences($option);
  }
  Toolbar::divider();
  Toolbar::help('menus');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="w-8">
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('JGLOBAL_TITLE') }}</th>
          <th class="text-center priority-4" colspan="3">{{ Lang::txt('COM_MENUS_HEADING_NUMBER_MENU_ITEMS') }}</th>
          <th>{{ Lang::txt('COM_MENUS_HEADING_LINKED_MODULES') }}</th>
          <th class="text-center priority-5 w-16">{{ Lang::txt('JGRID_HEADING_ID') }}</th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th class="text-center priority-4 w-20">{{ Lang::txt('COM_MENUS_HEADING_PUBLISHED_ITEMS') }}</th>
          <th class="text-center priority-4 w-20">{{ Lang::txt('COM_MENUS_HEADING_UNPUBLISHED_ITEMS') }}</th>
          <th class="text-center priority-4 w-20">{{ Lang::txt('COM_MENUS_HEADING_TRASHED_ITEMS') }}</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7" class="p-3 text-sm text-muted-foreground">
            {!! $items->pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($items as $i => $item)
          @php
            $canEdit   = \Hubzero\Facades\User::authorise('core.edit', $option);
            $canChange = \Hubzero\Facades\User::authorise('core.edit.state', $option);
            $menutype  = $item->get('menutype');
          @endphp
          <tr>
            <td class="text-center">
              <input type="checkbox" name="cid[]" value="{{ (int) $item->get('id') }}"
                     class="checkbox checkbox-sm" aria-label="{{ $item->get('title') }}" data-check-item />
            </td>
            <td>
              @php
                $itemsUrl = Route::url('index.php?option=' . $option . '&controller=items&menutype=' . $menutype, false);
              @endphp
              <a href="{{ $itemsUrl }}" class="font-medium link link-hover">
                {{ $item->get('title') }}
              </a>
              <div class="text-xs text-muted-foreground mt-0.5">
                {{ Lang::txt('COM_MENUS_MENU_MENUTYPE_LABEL') }}
                @if($canEdit)
                  @php
                    $editUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $item->get('id'), false);
                  @endphp
                  <a href="{{ $editUrl }}" class="link link-hover"
                     title="{{ $item->get('description') }}">{{ $menutype }}</a>
                @else
                  {{ $menutype }}
                @endif
              </div>
            </td>
            <td class="text-center priority-4">
              @php $pubUrl = Route::url('index.php?option=' . $option . '&controller=items&menutype=' . $menutype . '&filter_published=1', false); @endphp
              <a href="{{ $pubUrl }}" class="badge badge-success badge-sm">{{ $item->countPublishedItems() }}</a>
            </td>
            <td class="text-center priority-4">
              @php $unpubUrl = Route::url('index.php?option=' . $option . '&controller=items&menutype=' . $menutype . '&filter_published=0', false); @endphp
              <a href="{{ $unpubUrl }}" class="badge badge-ghost badge-sm">{{ $item->countUnpublishedItems() }}</a>
            </td>
            <td class="text-center priority-4">
              @php $trashUrl = Route::url('index.php?option=' . $option . '&controller=items&menutype=' . $menutype . '&filter_published=-2', false); @endphp
              <a href="{{ $trashUrl }}" class="badge badge-error badge-sm">{{ $item->countTrashedItems() }}</a>
            </td>
            <td>
              @if(isset($modules[$menutype]))
                <ul class="space-y-1 text-sm">
                  @foreach($modules[$menutype] as $module)
                    <li>
                      @if($canEdit)
                        @php
                          $modUrl = Route::url('index.php?option=com_modules&task=edit&id=' . $module->id . '&return=' . $return, false);
                        @endphp
                        <a href="{{ $modUrl }}" class="link link-hover text-xs">
                          {!! Lang::txt('COM_MENUS_MODULE_ACCESS_POSITION', e($module->title), e($module->access_title), e($module->position)) !!}
                        </a>
                      @else
                        <span class="text-xs">
                          {!! Lang::txt('COM_MENUS_MODULE_ACCESS_POSITION', e($module->title), e($module->access_title), e($module->position)) !!}
                        </span>
                      @endif
                    </li>
                  @endforeach
                </ul>
              @elseif($modMenuId)
                @php
                  $addModUrl = Route::url('index.php?option=com_modules&task=add&eid=' . $modMenuId . '&params[menutype]=' . $menutype, false);
                @endphp
                <a href="{{ $addModUrl }}" class="link link-hover text-sm">
                  {{ Lang::txt('COM_MENUS_ADD_MENU_MODULE') }}
                </a>
              @endif
            </td>
            <td class="text-center priority-5 text-sm">{{ (int) $item->get('id') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="p-6 text-center text-muted-foreground text-sm">
              {{ Lang::txt('COM_MENUS_NO_MENUS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! \Hubzero\Facades\Html::input('token') !!}

</x-admin-form>
