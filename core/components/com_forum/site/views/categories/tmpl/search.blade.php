{{--
  Forum search results.

  Variables from controller (searchTask):
    $filters    — Array with scope, scope_id, search, orderBy, orderDir, state, access
    $forum      — Manager model instance
    $sections   — Array of Section models keyed by ID
    $categories — Array of Category models keyed by ID
    $config     — Component params (Registry)

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

  $forumUrl  = Route::url('index.php?option=' . $option, false);
  $searchUrl = Route::url('index.php?option=' . $option . '&controller=categories&task=search', false);

  $rows = $forum->posts($filters)->paginated()->rows();
@endphp

<x-page-container :title="Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_SEARCH')">
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

  {{-- Search form --}}
  <x-search-bar :action="$searchUrl"
                :query="$filters['search']"
                name="q"
                :placeholder="Lang::txt('COM_FORUM_SEARCH_PLACEHOLDER')"
                :label="Lang::txt('COM_FORUM_SEARCH_LABEL')"
                :buttonLabel="Lang::txt('COM_FORUM_SEARCH')" />

  {{-- Results --}}
  @if($filters['search'] && $rows->count() > 0)
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>{{ Lang::txt('COM_FORUM_FIELD_TITLE') }}</th>
            <th>{{ Lang::txt('COM_FORUM_SECTION') }}</th>
            <th>{{ Lang::txt('COM_FORUM_CATEGORY') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
            @php
              // Highlight search term in title
              $title = e(stripslashes($row->get('title')));
              $title = preg_replace(
                  '#' . preg_quote($filters['search'], '#') . '#i',
                  '<mark>$0</mark>',
                  $title
              );

              // Author
              $name = Lang::txt('JANONYMOUS');
              $nameUrl = '';
              if (!$row->get('anonymous')) {
                  $name = e(stripslashes($row->creator->get('name')));
                  if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                      $nameUrl = Route::url($row->creator->link(), false);
                  }
              }

              // Resolve category / section
              $catId    = $row->get('category_id');
              $catalias = $catId;
              $catTitle = $catId;
              $secalias = '';
              $secTitle = Lang::txt('COM_FORUM_UNKNOWN');
              if (isset($categories[$catId])) {
                  $cat = $categories[$catId];
                  $secId = $cat->get('section_id');
                  $catalias = $cat->get('alias');
                  $catTitle = $cat->get('title');
                  if (isset($sections[$secId])) {
                      $secalias = $sections[$secId]->get('alias');
                      $secTitle = $sections[$secId]->get('title');
                  }
              }

              $threadUrl = Route::url(
                  'index.php?option=' . $option
                  . '&section=' . $secalias
                  . '&category=' . $catalias
                  . '&thread=' . $row->get('thread')
                  . '&q=' . urlencode($filters['search']),
                  false
              );
            @endphp
            <tr>
              <td class="w-8">
                @if($row->get('sticky'))
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-4 text-warning" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                  </svg>
                @elseif($row->get('closed'))
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
                <a class="link link-hover font-medium" href="{{ $threadUrl }}">
                  {!! $title !!}
                </a>
                <p class="text-xs opacity-60 mt-0.5">
                  {{ $row->created('date') }}
                  {{ Lang::txt('COM_FORUM_BY_USER', '') }}
                  @if($nameUrl)
                    <a href="{{ $nameUrl }}">{{ $name }}</a>
                  @else
                    {{ $name }}
                  @endif
                </p>
              </td>
              <td class="text-sm">
                {{ e(\Hubzero\Utility\Str::truncate($secTitle, 100, ['exact' => true])) }}
              </td>
              <td class="text-sm">
                {{ e(\Hubzero\Utility\Str::truncate($catTitle, 100, ['exact' => true])) }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @php
      $pageNav = $rows->pagination;
      $pageNav->setAdditionalUrlParam('q', $filters['search']);
    @endphp
    {!! $pageNav !!}
  @else
    <x-empty-state
        :title="Lang::txt('COM_FORUM_SEARCH')"
        :message="Lang::txt('COM_FORUM_CATEGORY_EMPTY')" />
  @endif
</x-page-container>
