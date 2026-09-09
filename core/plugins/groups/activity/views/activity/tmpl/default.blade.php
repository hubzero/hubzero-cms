{{--
  Group Activity — activity feed with search, post form, and pagination.

  Variables from plugin:
    $group   — group object
    $rows    — activity recipient rows collection
    $total   — total activity count
    $filters — filters array (filter, search, start, limit)

  @package    hubzero-cms
  @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  $no_html = Request::getInt('no_html', 0);

  $base = 'index.php?option=com_groups&cn=' . $group->get('cn');

  // Get online users
  $online = [];
  $sessions = Hubzero\Session\Helper::getAllSessions([
      'guest'    => 0,
      'distinct' => 1,
  ]);
  if ($sessions) {
      foreach ($sessions as $session) {
          $online[] = $session->userid;
      }
  }
@endphp

@if (!$no_html)
<div class="space-y-4">
  {{-- Search / filter toolbar --}}
  <form action="{{ Route::url($base . '&active=activity') }}" method="get">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
      <div class="flex gap-2 items-center">
        <input type="text"
               name="q"
               value="{{ e($filters['search']) }}"
               placeholder="{{ Lang::txt('PLG_GROUPS_ACTIVITY_SEARCH_PLACEHOLDER') }}"
               class="input input-bordered input-sm w-64" />
        <button type="submit" class="btn btn-primary btn-sm">
          {{ Lang::txt('PLG_GROUPS_ACTIVITY_SEARCH') }}
        </button>
      </div>
      <div class="flex gap-2">
        @if ($filters['filter'] == 'starred')
          <a class="btn btn-ghost btn-sm"
             href="{{ Route::url($base . '&active=activity') }}"
             title="{{ Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_ALL') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                 class="size-4 text-warning" aria-hidden="true">
              <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
            </svg>
            {{ Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_ALL') }}
          </a>
        @else
          <a class="btn btn-ghost btn-sm"
             href="{{ Route::url($base . '&active=activity&filter=starred') }}"
             title="{{ Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_STARRED') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
            </svg>
            {{ Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_STARRED') }}
          </a>
        @endif
      </div>
    </div>
  </form>

  {{-- Post form (managers only, published groups) --}}
  @if (in_array(User::get('id'), $group->get('managers')))
    @if ($group->published == 1)
      <form action="{{ Route::url($base . '&active=activity') }}"
            method="post"
            id="commentform"
            enctype="multipart/form-data"
            class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
          <div class="flex gap-3">
            <div class="shrink-0">
              <img src="{{ User::picture(!User::isGuest() ? 0 : 1) }}"
                   class="size-10 rounded-full object-cover"
                   alt="{{ Lang::txt('PLG_GROUPS_ACTIVITY_USER_PHOTO') }}" />
            </div>
            <div class="flex-1 space-y-3">
              <div class="form-control w-full">
                <label class="label" for="activity-description">
                  <span class="label-text">{{ Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_COMMENTS') }}</span>
                </label>
                {!! $__view->editor(
                    'activity[description]',
                    '',
                    5,
                    3,
                    'activity-description',
                    ['class' => 'form-control minimal no-footer']
                ) !!}
              </div>

              @if (in_array(User::get('id'), $group->get('managers')))
                <div class="form-control w-full">
                  <label class="label" for="activity-recipients">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS') }}</span>
                  </label>
                  <select name="activity_recipients" id="activity-recipients"
                          class="select select-bordered select-sm w-full max-w-xs">
                    <option value="all">{{ Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_ALL') }}</option>
                    <option value="managers">{{ Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_MANAGERS') }}</option>
                  </select>
                </div>
              @endif

              <div class="form-control w-full">
                <label class="label" for="activity-file">
                  <span class="label-text">{{ Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_FILE') }}</span>
                </label>
                <input type="file"
                       class="file-input file-input-bordered file-input-sm w-full max-w-xs"
                       name="activity_file"
                       id="activity-file"
                       data-multiple-caption="{{ Lang::txt('{count} files selected') }}"
                       multiple />
              </div>

              <div>
                {!! Html::input('token') !!}
                <input type="hidden" name="option" value="com_groups" />
                <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
                <input type="hidden" name="task" value="view" />
                <input type="hidden" name="active" value="activity" />
                <input type="hidden" name="action" value="post" />
                <input type="hidden" name="activity[id]" value="0" />
                <input type="hidden" name="activity[action]" value="created" />
                <input type="hidden" name="activity[scope]" value="activity.comment" />
                <input type="hidden" name="activity[scope_id]" value="{{ $group->get('gidNumber') }}" />
                <button type="submit" class="btn btn-primary btn-sm">
                  {{ Lang::txt('PLG_GROUPS_ACTIVITY_SUBMIT') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    @endif
  @endif
@endif

  @if ($rows->count())
    <ul class="activity-feed space-y-4"
        data-url="{{ Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=activity') }}">
      @foreach ($rows as $row)
        {!! $__view->view('default_item')
            ->set('group', $group)
            ->set('row', $row)
            ->set('online', $online)
            ->loadTemplate() !!}
      @endforeach
    </ul>

    @php
      $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
      $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
      $pageNav->setAdditionalUrlParam('active', 'activity');
      if ($filters['filter']) {
          $pageNav->setAdditionalUrlParam('filter', $filters['filter']);
      }
      if ($filters['search']) {
          $pageNav->setAdditionalUrlParam('search', $filters['search']);
      }
    @endphp
    {!! $pageNav !!}
  @else
    <div class="text-center py-8">
      <p class="text-base-content/70 mb-4">{{ Lang::txt('PLG_GROUPS_ACTIVITY_NO_RESULTS') }}</p>
      <p class="mb-2"><strong>{{ Lang::txt('PLG_GROUPS_ACTIVITY_ABOUT_TITLE') }}</strong></p>
      <p class="text-base-content/70">{{ Lang::txt('PLG_GROUPS_ACTIVITY_ABOUT') }}</p>
    </div>
  @endif

@if (!$no_html)
</div>
@endif
