{{--
  Category display — thread listing with sort tabs.

  Variables from controller (displayTask):
    $category — Category model instance
    $threads  — Paginated collection of thread (Post) models
    $filters  — Array with section, category, search, sortby, sort_Dir, access, state
    $config   — Component params (Registry)
    $section  — Section model instance
    $forum    — Manager model instance

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();
  $__view->js();

  $category->set('section_alias', $filters['section']);

  $forumUrl   = Route::url('index.php?option=' . $option, false);
  $searchUrl  = Route::url('index.php?option=' . $option . '&controller=categories&task=search', false);

  $canManage = $config->get('access-manage-thread');
  $canEdit   = $config->get('access-edit-thread');
  $canDelete = $config->get('access-delete-thread');
  $canCreate = $config->get('access-create-thread');

  // Sort direction toggler
  $sortDir = function ($current, $dir = 'DESC') use ($filters) {
      if ($filters['sortby'] == $current && $filters['sort_Dir'] == $dir) {
          $dir = ($dir == 'ASC' ? 'DESC' : 'ASC');
      }
      return strtolower($dir);
  };

  // Sort tab helper
  $sortTabs = [
      'created'  => ['label' => Lang::txt('COM_FORUM_SORT_CREATED'),  'default' => 'DESC'],
      'activity' => ['label' => Lang::txt('COM_FORUM_SORT_ACTIVITY'), 'default' => 'DESC'],
      'replies'  => ['label' => Lang::txt('COM_FORUM_SORT_NUM_POSTS'),'default' => 'DESC'],
      'title'    => ['label' => Lang::txt('COM_FORUM_SORT_TITLE'),    'default' => 'ASC'],
  ];
@endphp

<x-page-container :title="e(stripslashes($category->get('title')))">
  @slot('actions')
    <a class="btn" href="{{ $forumUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
      </svg>
      {{ Lang::txt('COM_FORUM_ALL_CATEGORIES') }}
    </a>
  @endslot

  @slot('sidebar')
    {{-- Last post --}}
    <x-sidebar-card :title="Lang::txt('COM_FORUM_LAST_POST')">
      @php
        $last = $category->lastActivity();
      @endphp
      @if($last->get('id'))
        @php
          $lname = Lang::txt('JANONYMOUS');
          $lnameUrl = '';
          if (!$last->get('anonymous')) {
              $lname = e(stripslashes($last->creator->get('name', $lname)));
              if (in_array($last->creator->get('access'), User::getAuthorisedViewLevels())) {
                  $lnameUrl = Route::url($last->creator->link(), false);
              }
          }
          $last->set('category', $filters['category']);
          $last->set('section', $filters['section']);
        @endphp
        <a class="link link-hover text-sm" href="{{ Route::url($last->link(), false) }}">
          {{ \Hubzero\Utility\Str::truncate(strip_tags($last->get('comment')), 170) }}
        </a>
        <p class="text-xs opacity-70 mt-1">
          @if($lnameUrl)
            <a href="{{ $lnameUrl }}">{{ $lname }}</a>
          @else
            {{ $lname }}
          @endif
          &middot;
          <time datetime="{{ $last->get('created') }}">{{ $last->created('date') }}</time>
        </p>
      @else
        <p class="text-sm opacity-50">{{ Lang::txt('COM_FORUM_NONE') }}</p>
      @endif
    </x-sidebar-card>

    {{-- New discussion --}}
    @if($canCreate)
      <x-sidebar-card :title="Lang::txt('COM_FORUM_CREATE_YOUR_OWN')">
        @if(!$category->isClosed())
          <p class="text-sm mb-3">{{ Lang::txt('COM_FORUM_CREATE_YOUR_OWN_DISCUSSION') }}</p>
          <a class="btn btn-primary btn-sm w-full"
             href="{{ Route::url($category->link('newthread'), false) }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ Lang::txt('COM_FORUM_NEW_DISCUSSION') }}
          </a>
        @else
          <p class="text-sm text-warning">{{ Lang::txt('COM_FORUM_CATEGORY_CLOSED') }}</p>
        @endif
      </x-sidebar-card>
    @endif
  @endslot

  {{-- Sort tabs --}}
  <div role="tablist" class="tabs tabs-border mb-4"
       aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
    @foreach($sortTabs as $key => $tab)
      @php
        $isActive = $filters['sortby'] == $key;
        $dir = $sortDir($key, $tab['default']);
        $url = Route::url($category->link('here', '&sortby=' . $key . '&sortdir=' . $dir), false);
        $arrowUp = $isActive && strtolower($filters['sort_Dir']) == 'asc';
      @endphp
      <a role="tab" class="tab{{ $isActive ? ' tab-active' : '' }}" href="{{ $url }}">
        {{ $tab['label'] }}
        @if($isActive)
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="2" stroke="currentColor" class="size-3 ml-1" aria-hidden="true">
            @if($arrowUp)
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            @else
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            @endif
          </svg>
        @endif
      </a>
    @endforeach
  </div>

  {{-- Thread listing --}}
  @if($threads->count() > 0)
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>{{ Lang::txt('COM_FORUM_FIELD_TITLE') }}</th>
            <th class="text-center">{{ Lang::txt('COM_FORUM_COMMENTS') }}</th>
            <th>{{ Lang::txt('COM_FORUM_LAST_POST') }}</th>
            @if($canManage || $canEdit || $canDelete)
              <th></th>
            @endif
          </tr>
        </thead>
        <tbody>
          @foreach($threads as $row)
            @php
              $name = Lang::txt('JANONYMOUS');
              $nameUrl = '';
              if (!$row->get('anonymous')) {
                  $name = e(stripslashes($row->creator->get('name', $name)));
                  if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                      $nameUrl = Route::url($row->creator->link(), false);
                  }
              }
              $row->set('category', $filters['category']);
              $row->set('section', $filters['section']);

              $replyCount = $row->thread()
                  ->whereEquals('state', $row->get('state'))
                  ->whereIn('access', $filters['access'])
                  ->total();
            @endphp
            <tr @if($row->isClosed() || $row->isSticky()) class="{{ $row->isClosed() ? 'opacity-60' : '' }}" @endif>
              <td class="w-8">
                @if($row->isSticky())
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-4 text-warning" aria-hidden="true"
                       title="{{ Lang::txt('COM_FORUM_FIELD_STICKY') }}">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                  </svg>
                @elseif($row->isClosed())
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-4 opacity-50" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                  </svg>
                @else
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-4 opacity-50" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                  </svg>
                @endif
              </td>
              <td>
                <a class="link link-hover font-medium" href="{{ Route::url($row->link(), false) }}">
                  {{ e(stripslashes($row->get('title'))) }}
                </a>
                <p class="text-xs opacity-60 mt-0.5">
                  <time datetime="{{ $row->created() }}">{{ $row->created('date') }}</time>
                  {{ Lang::txt('COM_FORUM_BY_USER', '') }}
                  @if($nameUrl)
                    <a href="{{ $nameUrl }}">{{ $name }}</a>
                  @else
                    {{ $name }}
                  @endif
                </p>
              </td>
              <td class="text-center">{{ $replyCount }}</td>
              <td>
                @php
                  $lastpost = $row->lastActivity();
                @endphp
                @if($lastpost->get('id'))
                  @php
                    $lpName = Lang::txt('JANONYMOUS');
                    $lpUrl = '';
                    if (!$lastpost->get('anonymous')) {
                        $lpName = e(stripslashes($lastpost->creator->get('name')));
                        if (in_array($lastpost->creator->get('access'), User::getAuthorisedViewLevels())) {
                            $lpUrl = Route::url($lastpost->creator->link(), false);
                        }
                    }
                  @endphp
                  <span class="text-xs">
                    <time datetime="{{ $lastpost->created() }}">{{ $lastpost->created('date') }}</time>
                    &middot;
                    @if($lpUrl)
                      <a href="{{ $lpUrl }}">{{ $lpName }}</a>
                    @else
                      {{ $lpName }}
                    @endif
                  </span>
                @else
                  <span class="text-xs opacity-50">{{ Lang::txt('COM_FORUM_NONE') }}</span>
                @endif
              </td>
              @if($canManage || $canEdit || $canDelete)
                <td class="text-right">
                  @php $isCreator = $row->get('created_by') == User::get('id'); @endphp
                  @if($canManage || ($canEdit && $isCreator))
                    <a class="btn btn-ghost btn-xs"
                       href="{{ Route::url($row->link('edit'), false) }}">
                      {{ Lang::txt('JACTION_EDIT') }}
                    </a>
                  @endif
                  @if($canManage || ($canDelete && $isCreator))
                    <a class="btn btn-ghost btn-xs text-error delete"
                       data-txt-confirm="{{ Lang::txt('COM_FORUM_CONFIRM_DELETE') }}"
                       href="{{ Route::url($row->link('delete'), false) }}">
                      {{ Lang::txt('JACTION_DELETE') }}
                    </a>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @php
      $pageNav = $threads->pagination;
      $pageNav->setAdditionalUrlParam('section', $filters['section']);
      $pageNav->setAdditionalUrlParam('category', $filters['category']);
      $pageNav->setAdditionalUrlParam('q', $filters['search']);
    @endphp
    {!! $pageNav !!}
  @else
    <x-empty-state
        :title="Lang::txt('COM_FORUM_CATEGORY_EMPTY')"
        :message="Lang::txt('COM_FORUM_CREATE_YOUR_OWN_DISCUSSION')">
      @if($canCreate && !$category->isClosed())
        <a class="btn btn-primary btn-sm"
           href="{{ Route::url($category->link('newthread'), false) }}">
          {{ Lang::txt('COM_FORUM_NEW_DISCUSSION') }}
        </a>
      @endif
    </x-empty-state>
  @endif
</x-page-container>
