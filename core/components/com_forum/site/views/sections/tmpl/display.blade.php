{{--
  Forum home — sections listing with category tables.

  Variables from controller (displayTask):
    $sections — Collection of Section models
    $filters  — Array with scope, state, access, search
    $config   — Component params (Registry) with access-* permissions
    $forum    — Manager model instance
    $edit     — Section alias currently being edited (or null)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Breadcrumbs & title
  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_FORUM'), 'index.php?option=' . $option);
  }
  Document::setTitle(Lang::txt('COM_FORUM'));

  $__view->css();
  $__view->js();

  $searchUrl  = Route::url('index.php?option=' . $option . '&controller=categories&task=search', false);
  $forumUrl   = Route::url('index.php?option=' . $option, false);

  $canEditSection   = $config->get('access-edit-section');
  $canDeleteSection = $config->get('access-delete-section');
  $canCreateCat     = $config->get('access-create-category');
  $canCreateSection = $config->get('access-create-section');
@endphp

<x-page-container :title="Lang::txt('COM_FORUM')">
  @slot('actions')
    <a class="btn" href="{{ $searchUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
      </svg>
      {{ Lang::txt('COM_FORUM_SEARCH') }}
    </a>
  @endslot

  @slot('sidebar')
    {{-- Statistics --}}
    <x-sidebar-card :title="Lang::txt('COM_FORUM_STATS')">
      <dl class="stats-list">
        <div class="stats-row">
          <dt>{{ Lang::txt('COM_FORUM_CATEGORIES') }}</dt>
          <dd>{{ $forum->count('categories', $filters) }}</dd>
        </div>
        <div class="stats-row">
          <dt>{{ Lang::txt('COM_FORUM_DISCUSSIONS') }}</dt>
          <dd>{{ $forum->count('threads', $filters) }}</dd>
        </div>
        <div class="stats-row">
          <dt>{{ Lang::txt('COM_FORUM_POSTS') }}</dt>
          <dd>{{ $forum->count('posts', $filters) }}</dd>
        </div>
      </dl>
    </x-sidebar-card>

    {{-- Last post --}}
    <x-sidebar-card :title="Lang::txt('COM_FORUM_LAST_POST')">
      @php
        $lastPost = $forum->lastActivity();
      @endphp
      @if($lastPost->get('id'))
        @php
          $lname = Lang::txt('COM_FORUM_ANONYMOUS');
          $lnameUrl = '';
          if (!$lastPost->get('anonymous')) {
              $lname = e(stripslashes($lastPost->creator->get('name', $lname)));
              if (in_array($lastPost->creator->get('access'), User::getAuthorisedViewLevels())) {
                  $lnameUrl = Route::url($lastPost->creator->link(), false);
              }
          }
          // Find category/section for link building
          foreach ($sections as $sec) {
              if ($sec->categories()->total() > 0) {
                  foreach ($sec->categories()->rows() as $cat) {
                      if ($cat->get('id') == $lastPost->get('category_id')) {
                          $lastPost->set('category', $cat->get('alias'));
                          $lastPost->set('section', $sec->get('alias'));
                          break 2;
                      }
                  }
              }
          }
        @endphp
        <a class="link link-hover text-sm" href="{{ Route::url($lastPost->link(), false) }}">
          {{ \Hubzero\Utility\Str::truncate(strip_tags($lastPost->get('comment')), 170) }}
        </a>
        <p class="text-xs opacity-70 mt-1">
          @if($lnameUrl)
            <a href="{{ $lnameUrl }}">{{ $lname }}</a>
          @else
            {{ $lname }}
          @endif
          &middot;
          <time datetime="{{ $lastPost->get('created') }}">
            {{ $lastPost->created('date') }}
          </time>
        </p>
      @else
        <p class="text-sm opacity-50">{{ Lang::txt('COM_FORUM_NONE') }}</p>
      @endif
    </x-sidebar-card>

    {{-- New section form --}}
    @if($canCreateSection)
      <x-sidebar-card :title="Lang::txt('COM_FORUM_SECTION')">
        <p class="text-sm mb-3">{{ Lang::txt('COM_FORUM_SECTION_EXPLANATION') }}</p>
        <form action="{{ $forumUrl }}" method="post">
          <div class="form-field">
            <label class="form-field-label" for="field-title">
              {{ Lang::txt('COM_FORUM_FIELD_TITLE') }}
            </label>
            <input type="text" class="input input-sm w-full" name="fields[title]"
                   id="field-title" value="" required />
          </div>
          <div class="mt-3">
            <button type="submit" class="btn btn-primary btn-sm">
              {{ Lang::txt('JACTION_CREATE') }}
            </button>
          </div>
          <input type="hidden" name="task" value="save" />
          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="sections" />
          <input type="hidden" name="fields[id]" value="" />
          <input type="hidden" name="fields[scope]" value="site" />
          <input type="hidden" name="fields[scope_id]" value="0" />
          <input type="hidden" name="fields[access]" value="1" />
          <input type="hidden" name="fields[state]" value="1" />
          {!! Html::input('token') !!}
        </form>
      </x-sidebar-card>
    @endif
  @endslot

  @if($sections->count())
    @foreach($sections as $section)
      @php
        $categories = $section->categories()
            ->whereEquals('state', $filters['state'])
            ->whereIn('access', $filters['access'])
            ->order('title', 'asc')
            ->rows();
        $sectionAlias = $section->get('alias');
        $sectionId    = $section->get('id');
        $sectionTitle = e(stripslashes($section->get('title')));
      @endphp

      <div class="card bg-base-100 shadow-sm mb-4" id="section-{{ $sectionId }}">
        {{-- Section header --}}
        <div class="card-body p-4">
          <div class="flex items-center justify-between">
            @if($canEditSection && $edit == $sectionAlias && $sectionId)
              {{-- Inline edit form --}}
              <form action="{{ $forumUrl }}" method="post" class="flex items-center gap-2 flex-1"
                    id="s{{ $sectionId }}">
                <input type="text" name="fields[title]" value="{{ $sectionTitle }}"
                       class="input input-sm flex-1" />
                <button type="submit" class="btn btn-primary btn-sm">
                  {{ Lang::txt('JSUBMIT') }}
                </button>
                <input type="hidden" name="fields[id]" value="{{ $sectionId }}" />
                <input type="hidden" name="fields[scope]" value="site" />
                <input type="hidden" name="fields[scope_id]" value="0" />
                <input type="hidden" name="fields[access]" value="{{ $section->get('access') }}" />
                <input type="hidden" name="fields[state]" value="{{ $section->get('state') }}" />
                <input type="hidden" name="controller" value="sections" />
                <input type="hidden" name="task" value="save" />
                {!! Html::input('token') !!}
              </form>
            @else
              <h2 class="card-title text-lg">{{ $sectionTitle }}</h2>
            @endif

            @if(($canEditSection || $canDeleteSection) && $sectionId)
              <div class="flex gap-1">
                @if($canEditSection && $edit != $sectionAlias)
                  <a class="btn btn-ghost btn-xs"
                     href="{{ Route::url('index.php?option=' . $option . '&section=' . $sectionAlias . '&task=edit#s' . $sectionId, false) }}"
                     title="{{ Lang::txt('JACTION_EDIT') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z" />
                    </svg>
                    {{ Lang::txt('JACTION_EDIT') }}
                  </a>
                @endif
                @if($canDeleteSection)
                  <a class="btn btn-ghost btn-xs text-error"
                     data-txt-confirm="{{ Lang::txt('COM_FORUM_CONFIRM_DELETE') }}"
                     href="{{ Route::url('index.php?option=' . $option . '&section=' . $sectionAlias . '&task=delete', false) }}"
                     title="{{ Lang::txt('JACTION_DELETE') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    {{ Lang::txt('JACTION_DELETE') }}
                  </a>
                @endif
              </div>
            @endif
          </div>

          {{-- Categories table --}}
          @if($categories->count() > 0)
            <div class="overflow-x-auto mt-3">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th></th>
                    <th>{{ Lang::txt('COM_FORUM_FIELD_TITLE') }}</th>
                    <th class="text-center">{{ Lang::txt('COM_FORUM_DISCUSSIONS') }}</th>
                    <th class="text-center">{{ Lang::txt('COM_FORUM_POSTS') }}</th>
                    @if($config->get('access-edit-category') || $config->get('access-delete-category'))
                      <th></th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @foreach($categories as $row)
                    @php
                      $row->set('section_alias', $sectionAlias);
                      $threadCount = $row->threads()
                          ->whereEquals('state', $filters['state'])
                          ->whereIn('access', $filters['access'])
                          ->total();
                      $postCount = $threadCount ? $row->posts()
                          ->whereEquals('state', $filters['state'])
                          ->whereIn('access', $filters['access'])
                          ->total() : 0;
                    @endphp
                    <tr @if($row->get('closed')) class="opacity-60" @endif>
                      <td class="w-8">
                        @if($row->get('closed'))
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                               stroke-width="1.5" stroke="currentColor" class="size-4 opacity-50" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                          </svg>
                        @else
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                               stroke-width="1.5" stroke="currentColor" class="size-4 opacity-50" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                          </svg>
                        @endif
                      </td>
                      <td>
                        <a class="link link-hover font-medium" href="{{ Route::url($row->link(), false) }}">
                          {{ e(stripslashes($row->get('title'))) }}
                        </a>
                        @if($row->get('description'))
                          <p class="text-xs opacity-60 mt-0.5">
                            {{ e(stripslashes($row->get('description'))) }}
                          </p>
                        @endif
                      </td>
                      <td class="text-center">{{ $threadCount }}</td>
                      <td class="text-center">{{ $postCount }}</td>
                      @if($config->get('access-edit-category') || $config->get('access-delete-category'))
                        <td class="text-right">
                          @php
                            $isCreator = $row->get('created_by') == User::get('id');
                            $canEditCat = $config->get('access-edit-category');
                            $canDeleteCat = $config->get('access-delete-category');
                          @endphp
                          @if(($isCreator || $canEditCat) && $sectionId)
                            <a class="btn btn-ghost btn-xs"
                               href="{{ Route::url($row->link('edit'), false) }}"
                               title="{{ Lang::txt('JACTION_EDIT') }}">
                              {{ Lang::txt('JACTION_EDIT') }}
                            </a>
                          @endif
                          @if($canDeleteCat && $sectionId)
                            <a class="btn btn-ghost btn-xs text-error delete"
                               data-txt-confirm="{{ Lang::txt('COM_FORUM_CONFIRM_DELETE') }}"
                               href="{{ Route::url($row->link('delete'), false) }}"
                               title="{{ Lang::txt('JACTION_DELETE') }}">
                              {{ Lang::txt('JACTION_DELETE') }}
                            </a>
                          @endif
                        </td>
                      @endif
                    </tr>
                  @endforeach
                </tbody>
                @if($canCreateCat)
                  <tfoot>
                    <tr>
                      <td colspan="5">
                        <a class="btn btn-ghost btn-sm"
                           href="{{ Route::url('index.php?option=' . $option . '&section=' . $sectionAlias . '&task=new', false) }}">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                               stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                          </svg>
                          {{ Lang::txt('COM_FORUM_NEW_CATEGORY') }}
                        </a>
                      </td>
                    </tr>
                  </tfoot>
                @endif
              </table>
            </div>
          @else
            <p class="text-sm opacity-50 mt-3">{{ Lang::txt('COM_FORUM_SECTION_EMPTY') }}</p>
          @endif
        </div>
      </div>
    @endforeach
  @else
    {{-- Empty state --}}
    @if($canCreateSection)
      <x-empty-state
          :title="Lang::txt('COM_FORUM')"
          :message="Lang::txt('COM_FORUM_EMPTY_MODERATOR', Route::url('index.php?option=' . $option . '&action=populate', false))">
        <form action="{{ $forumUrl }}" method="post" class="flex items-center gap-2">
          <input type="text" class="input input-sm" name="fields[title]"
                 placeholder="{{ Lang::txt('COM_FORUM_ENTER_TITLE') }}" required />
          <button type="submit" class="btn btn-primary btn-sm">
            {{ Lang::txt('JACTION_CREATE') }}
          </button>
          <input type="hidden" name="task" value="save" />
          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="sections" />
          <input type="hidden" name="fields[id]" value="" />
          <input type="hidden" name="fields[scope]" value="site" />
          <input type="hidden" name="fields[scope_id]" value="0" />
          <input type="hidden" name="fields[access]" value="1" />
          <input type="hidden" name="fields[state]" value="1" />
          {!! Html::input('token') !!}
        </form>
      </x-empty-state>
    @else
      <x-empty-state
          :title="Lang::txt('COM_FORUM')"
          :message="Lang::txt('COM_FORUM_EMPTY_NOT_MODERATOR')" />
    @endif
  @endif
</x-page-container>
