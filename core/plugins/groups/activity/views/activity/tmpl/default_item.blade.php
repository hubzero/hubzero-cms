{{--
  Group Activity — single activity item.

  Variables (set by parent view):
    $group  — group object
    $row    — activity recipient row
    $online — array of online user IDs

  @package    hubzero-cms
  @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\User;

  $status = '';
  if (!$row->wasViewed()) {
      $status = 'new';
      $row->markAsViewed();
  }
  if ($row->get('starred')) {
      $status .= ' starred';
  }

  $creator = User::getInstance($row->log->get('created_by'));
  $name = Lang::txt('JANONYMOUS');
  $isOnline = false;

  if (!$row->log->get('anonymous')) {
      $name = e(stripslashes($creator->get('name', Lang::txt('PLG_GROUPS_ACTIVITY_UNKNOWN'))));
      if (in_array($creator->get('access'), User::getAuthorisedViewLevels())) {
          $name = '<a href="' . Route::url($creator->link()) . '">' . $name . '</a>';
      }
      if (isset($online) && in_array($row->log->get('created_by'), $online)) {
          $isOnline = true;
      }
  }

  $base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=activity';

  // Time display
  $dt = Date::of($row->get('created'));
  $ct = Date::of('now');
  $lapsed = $ct->toUnix() - $dt->toUnix();

  if ($lapsed < 30) {
      $timeDisplay = Lang::txt('PLG_GROUPS_ACTIVITY_JUST_NOW');
  } elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y')) {
      $timeDisplay = $dt->toLocal('M j, Y');
  } elseif ($lapsed > 86400) {
      $timeDisplay = $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
  } else {
      $timeDisplay = $dt->relative();
  }

  // Scope context
  $scope = explode('.', $row->log->get('scope'));
  $context = e($scope[0]);

  // Channel label
  $channelTxt = ($row->get('scope') == 'group_managers')
      ? Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_MANAGERS')
      : Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_ALL');

  // Content truncation
  $content = $row->log->get('description');
  $short = (strlen(strip_tags($content)) > 150)
      ? Hubzero\Utility\Str::truncate($content, 150, ['html' => true])
      : null;

  // Attachments
  $attachments = $row->log->details->get('attachments');
  $attachments = $attachments ?: [];
  $attached = count($attachments);

  $isComment = $row->log->get('scope') == 'activity.comment';
  $isTopLevel = !$row->log->get('parent');
@endphp

<li data-time="{{ $row->get('created') }}"
    data-id="{{ $row->get('id') }}"
    data-log_id="{{ $row->get('log_id') }}"
    data-context="{{ $row->log->get('scope') }}"
    data-action="{{ $row->log->get('action') }}"
    id="activity{{ $row->get('id') }}"
    class="activity card bg-base-100 shadow-sm {{ trim($status) }} {{ $row->get('starred') ? 'border-l-4 border-warning' : '' }}">

  <div class="card-body p-4">
    <div class="flex gap-3">
      {{-- Avatar --}}
      <div class="shrink-0 relative">
        @if ($creator->get('public'))
          <a href="{{ Route::url($creator->link()) }}" title="{{ strip_tags($name) }}">
            <img src="{{ $creator->picture() }}"
                 class="size-10 rounded-full object-cover"
                 alt="{{ Lang::txt('PLG_GROUPS_ACTIVITY_PROFILE_PICTURE', strip_tags($name)) }}" />
          </a>
        @else
          <img src="{{ $creator->picture() }}"
               class="size-10 rounded-full object-cover"
               alt="{{ Lang::txt('PLG_GROUPS_ACTIVITY_PROFILE_PICTURE', strip_tags($name)) }}" />
        @endif
        @if ($isOnline)
          <span class="absolute bottom-0 right-0 size-3 rounded-full bg-success border-2 border-base-100"
                title="{{ Lang::txt('PLG_GROUPS_ACTIVITY_ONLINE') }}"></span>
        @endif
      </div>

      {{-- Content --}}
      <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
          <div class="text-sm">
            <span class="font-medium">{!! $name !!}</span>
            <span class="text-base-content/60">{{ e($row->log->get('action')) }}</span>
            <span class="badge badge-ghost badge-xs">{{ $channelTxt }}</span>
            <span class="text-base-content/40">{{ $context }}</span>
            <span class="text-base-content/40">&middot;</span>
            <time class="text-base-content/40"
                  datetime="{{ Date::of($row->get('created'))->format('Y-m-d\TH:i:s\Z') }}">
              {{ $timeDisplay }}
            </time>
          </div>

          {{-- Actions --}}
          @if ($group->published == 1)
            <div class="flex gap-1 shrink-0">
              @if ($isComment && $isTopLevel)
                @if (Request::getInt('reply', 0) == $row->get('id'))
                  <a class="btn btn-ghost btn-xs"
                     href="{{ Route::url($base) }}"
                     rel="comment-form{{ $row->get('id') }}"
                     title="{{ Lang::txt('JCANCEL') }}"
                     data-txt-active="{{ Lang::txt('JCANCEL') }}"
                     data-txt-inactive="{{ Lang::txt('PLG_GROUPS_ACTIVITY_REPLY') }}">
                    {{ Lang::txt('JCANCEL') }}
                  </a>
                @else
                  <a class="btn btn-ghost btn-xs"
                     href="{{ Route::url($base . '&action=reply&activity=' . $row->get('id')) }}"
                     rel="comment-form{{ $row->get('id') }}"
                     title="{{ Lang::txt('PLG_GROUPS_ACTIVITY_REPLY') }}"
                     data-txt-active="{{ Lang::txt('JCANCEL') }}"
                     data-txt-inactive="{{ Lang::txt('PLG_GROUPS_ACTIVITY_REPLY') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                  </a>
                @endif
              @endif

              @if ($isTopLevel)
                @php
                  $rowId = $row->get('id');
                  $starred = $row->get('starred');
                  $starAction = ($starred ? 'un' : '') . 'star';
                  $starHref = Route::url($base . '&action=' . $starAction . '&activity=' . $rowId);
                  $starTitle = $starred ? Lang::txt('Unstar this') : Lang::txt('Star this');
                @endphp
                <a class="btn btn-ghost btn-xs"
                   href="{{ $starHref }}"
                   data-id="activity{{ $rowId }}"
                   data-hrf-active="{{ Route::url($base . '&action=unstar&activity=' . $rowId) }}"
                   data-hrf-inactive="{{ Route::url($base . '&action=star&activity=' . $rowId) }}"
                   data-txt-active="{{ Lang::txt('Unstar this') }}"
                   data-txt-inactive="{{ Lang::txt('Star this') }}"
                   title="{{ $starTitle }}">
                  @if ($starred)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                         class="size-4 text-warning" aria-hidden="true">
                      <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                  @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                  @endif
                </a>
              @endif

              <a class="btn btn-ghost btn-xs text-error"
                 data-id="activity{{ $row->get('id') }}"
                 href="{{ Route::url($base . '&action=remove&activity=' . $row->get('id') . '&' . Session::getFormToken() . '=1') }}"
                 title="{{ Lang::txt('PLG_GROUPS_ACTIVITY_DELETE') }}"
                 data-txt-confirm="{{ Lang::txt('PLG_GROUPS_ACTIVITY_CONFIRM_DELETE') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
              </a>
            </div>
          @endif
        </div>

        {{-- Project source context --}}
        @if (str_starts_with($row->log->get('scope'), 'project'))
          @php
            $project = new \Components\Projects\Models\Project(
                $row->log->details->get('projectid', $row->log->get('scope_id'))
            );
          @endphp
          @if ($project)
            @php
              $projectDetailUrl = $row->log->details->get('url');
              if (empty($projectDetailUrl)) {
                  $projectDetailUrl = Route::url(
                      'index.php?option=com_projects&alias=' . $project->get('alias')
                  );
              }
            @endphp
            <div class="text-xs text-base-content/50 mt-1">
              <a class="link link-hover" href="{{ $projectDetailUrl }}">
                {{ $project->get('title') }}
              </a>
            </div>
          @endif
        @endif

        {{-- Event content --}}
        @if ($short)
          <div class="activity-event-preview mt-2 text-sm text-base-content/80">
            {!! $short !!}
            <a class="link link-hover text-xs more-content"
               href="#activity-event-content{{ $row->get('id') }}">
              {{ Lang::txt('PLG_GROUPS_ACTIVITY_MORE') }}
            </a>
          </div>
        @endif
        <div class="activity-event-content mt-2 text-sm text-base-content/80 {{ $short ? 'hide' : '' }}"
             id="activity-event-content{{ $row->get('id') }}">
          {!! $content !!}
        </div>

        {{-- Attachments --}}
        @if ($attached)
          <div class="activity-attachments mt-3 flex flex-wrap gap-3">
            @foreach ($attachments as $attachment)
              @php
                $attachment = new \Plugins\Groups\Activity\Models\Attachment($attachment);
                $attachment->setUploadDir('/site/groups/' . $group->get('gidNumber') . '/uploads');

                if (!$attachment->exists()) {
                    continue;
                }

                if (!trim($attachment->get('description'))) {
                    $attachment->set('description', $attachment->get('filename'));
                }

                $link = 'index.php?option=com_groups&cn='
                    . $group->get('cn')
                    . '&active=File:/uploads/'
                    . ($attachment->get('subdir') ? $attachment->get('subdir') . '/' : '')
                    . $attachment->get('filename');
              @endphp

              @if ($attachment->isImage())
                <a class="attachment img block"
                   rel="lightbox"
                   href="{{ Route::url($link) }}">
                  <img src="{{ Route::url($link) }}"
                       alt="{{ e($attachment->get('description')) }}"
                       class="rounded max-w-[400px]"
                       width="{{ ($attachment->width() > 400) ? 400 : $attachment->width() }}" />
                  <p class="text-xs text-base-content/50 mt-1">
                    <span>{{ Hubzero\Utility\Number::formatBytes($attachment->size()) }}</span>
                    <span>{{ Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD') }}</span>
                  </p>
                </a>
              @else
                @php
                  $fileExt = Filesystem::extension($attachment->get('filename'));
                @endphp
                <a class="attachment {{ $fileExt }} link link-hover"
                   href="{{ Route::url($link) }}"
                   title="{{ e($attachment->get('description')) }}">
                  <p class="font-medium text-sm">{{ $attachment->get('description') }}</p>
                  <p class="text-xs text-base-content/50">
                    <span>{{ Hubzero\Utility\Number::formatBytes($attachment->size()) }}</span>
                    <span>{{ Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD') }}</span>
                  </p>
                </a>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Reply form --}}
    @if ($group->published == 1 && $isComment)
      <div class="comment-add mt-3 {{ Request::getInt('reply', 0) != $row->get('id') ? 'hide' : '' }}"
           id="comment-form{{ $row->get('id') }}">
        <form id="cform{{ $row->get('id') }}"
              action="{{ Route::url($base) }}"
              method="post"
              enctype="multipart/form-data"
              class="pl-13 space-y-3">
          <fieldset>
            @php
              $replyTo = !$row->log->get('anonymous')
                  ? strip_tags($name)
                  : Lang::txt('JANONYMOUS');
            @endphp
            <legend class="text-sm font-medium mb-2">
              {{ Lang::txt('PLG_GROUPS_ACTIVITY_REPLYING_TO', $replyTo) }}
            </legend>

            <input type="hidden" name="activity[id]" value="0" />
            <input type="hidden" name="activity[action]" value="created" />
            <input type="hidden" name="activity[scope]" value="{{ $row->log->get('scope') }}" />
            <input type="hidden" name="activity[scope_id]" value="{{ $row->log->get('scope_id') }}" />
            <input type="hidden" name="activity[parent]" value="{{ $row->log->get('id') }}" />
            <input type="hidden" name="activity[created]" value="" />
            <input type="hidden" name="activity[created_by]" value="{{ User::get('id') }}" />
            <input type="hidden" name="option" value="com_groups" />
            <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
            <input type="hidden" name="active" value="activity" />
            <input type="hidden" name="action" value="post" />
            {!! Html::input('token') !!}

            <div class="form-control w-full">
              <label class="label" for="comment-{{ $row->get('id') }}-content">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_COMMENTS') }}</span>
              </label>
              {!! $__view->editor(
                  'activity[description]',
                  '',
                  35,
                  4,
                  'field_' . $row->get('id') . '_comment',
                  ['class' => 'form-control minimal no-footer']
              ) !!}
            </div>

            <div class="form-control w-full">
              <label class="label" for="activity-{{ $row->get('id') }}-file">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_FILE') }}</span>
              </label>
              <input type="file"
                     class="file-input file-input-bordered file-input-sm w-full max-w-xs"
                     name="activity_file"
                     id="activity-{{ $row->get('id') }}-file"
                     data-multiple-caption="{{ Lang::txt('{count} files selected') }}"
                     multiple />
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
              {{ Lang::txt('PLG_GROUPS_ACTIVITY_SUBMIT') }}
            </button>
          </fieldset>
        </form>
      </div>
    @endif

    <div class="activity-processor hidden">
      <div class="loading loading-spinner loading-sm"></div>
      <div class="msg"></div>
    </div>
  </div>

  {{-- Child comments --}}
  @if ($row->log->get('scope') == 'activity.comment' || $row->log->get('scope') == 'project.comment')
    @php
      $recipient = Hubzero\Activity\Recipient::all();
      $r = $recipient->getTableName();
      $l = Hubzero\Activity\Log::blank()->getTableName();

      $childRows = $recipient
          ->select($r . '.*')
          ->including('log')
          ->join($l, $l . '.id', $r . '.log_id')
          ->whereEquals($r . '.scope', 'group')
          ->whereEquals($r . '.scope_id', $group->get('gidNumber'))
          ->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
          ->whereEquals($l . '.parent', $row->log->get('id'))
          ->order('id', 'asc')
          ->rows();
    @endphp
    @if ($childRows->count())
      <ul class="activity-comments ml-6 space-y-2 border-l-2 border-base-200 pl-4 mt-2">
        @foreach ($childRows as $childRow)
          {!! $__view->view('default_item')
              ->set('group', $group)
              ->set('online', $online)
              ->set('row', $childRow)
              ->loadTemplate() !!}
        @endforeach
      </ul>
    @endif
  @endif
</li>
