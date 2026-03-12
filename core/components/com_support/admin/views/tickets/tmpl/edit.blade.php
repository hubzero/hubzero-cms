{{--
  Support — Ticket edit/view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;
  use Hubzero\Facades\Event;

  $currentUser = User::getInstance();
  $no_html     = Request::getInt('no_html', 0);

  // Resolve submitter display name + notify list
  $unknown  = true;
  $name     = '';
  $usertype = Lang::txt('COM_SUPPORT_UNKNOWN');
  $notify   = [];

  if ($row->get('login') && $row->get('name')) {
      $gids = [];
      foreach (User::getInstance($row->submitter->get('id'))->accessgroups() as $g) {
          $gids[] = $g->group_id;
      }
      $usertype = implode(', ', $gids);

      $submitterUrl  = Route::url('index.php?option=com_members&task=edit&id=' . $row->submitter->get('id'), false);
      $escapedName   = e($row->get('name'));
      $escapedLogin  = e($row->get('login'));
      $name          = '<a rel="profile" href="' . $submitterUrl . '">'
          . $escapedName . ' (' . $escapedLogin . ')</a>';
      $unknown       = false;
      $notify[]      = $escapedName . ' (' . $escapedLogin . ')';
  }

  if (!$name) {
      if ($row->get('name')) {
          $name = e($row->get('name')) . ' (' . e($row->get('email')) . ')';
      } else {
          $name = e($row->get('email'));
      }
      $notify[] = $name;
  }

  if ($row->isOwned() && $row->assignee->get('name')) {
      $notify[] = e($row->assignee->get('name'))
          . ' (' . e($row->assignee->get('username')) . ')';
  }

  // Last activity
  $lastactivity = Lang::txt('COM_SUPPORT_NOT_APPLICAPABLE');
  if ($row->comments->count() > 0) {
      $last         = $row->comments->last();
      $lastCreated  = $last->get('created');
      $lastactivity = '<time datetime="' . $lastCreated . '">'
          . Date::of($lastCreated)->toLocal(Lang::txt('TIME_FORMAT_HZ1'))
          . '</time>';
      $row->comments->rewind();
  }

  $cc = [];

  if (!$no_html) {
      $text         = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
      $toolbarTitle = Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKET') . ': ' . $text;
      Toolbar::title($toolbarTitle, 'support.png');
      Toolbar::save();
      Toolbar::apply();
      Toolbar::cancel();
      Toolbar::spacer();
      Toolbar::help('ticket');

      $__view->css()->css('support.blade')->js('ticket.blade.js')->js('support.blade.js');
  }

  $formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $tmp        = Request::getString('tmp_dir', ('-' . time()), 'post');
@endphp

<form action="{{ $formAction }}"
      method="post"
      @if (!$no_html) name="adminForm" id="item-form" @else name="ajaxForm" id="ajax-form" @endif
      enctype="multipart/form-data">

  @if (!$no_html)
  {{-- ── Ticket header + sidebar ──────────────────────────────────────────── --}}
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
  <div class="md:col-span-8">
    <fieldset>
      <legend><span>{{ Lang::txt('COM_SUPPORT_TICKET') }}{{ $row->get('id') ? ' #' . $row->get('id') : '' }}</span></legend>
  @else
    {{-- AJAX / no-html mode: inline status/severity dl --}}
    @php
      $statusClass = !$row->isOpen() ? 'closed' : 'open';
      $statusLabel = !$row->isOpen()
          ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED')
          : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN');
    @endphp
    <dl class="ticket-info {{ $row->get('severity') }}">
      <dt>#</dt>
      <dd>{{ $row->get('id') }}</dd>
      <dt>{{ Lang::txt('COM_SUPPORT_TICKET_STATUS') }}:</dt>
      <dd class="ticket-status {{ $statusClass }}">{{ $statusLabel }}</dd>
      <dt>{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_SEVERITY') }}:</dt>
      <dd class="ticket-severity {{ $row->get('severity') }}">
        {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($row->get('severity'))) }}
      </dd>
    </dl>
  @endif

      {{-- Ticket body --}}
      <div class="ticket{{ $no_html ? '-body' : '' }}" id="t{{ $row->get('id') }}">
        <p class="ticket-member-photo">
          <img src="{{ $row->submitter()->picture($unknown) }}" alt="" />
        </p>
        <div class="ticket-head">
          <strong>{!! $name !!}</strong>
          @php
            $permalinkUrl   = Route::url('index.php?option=com_support&controller=tickets&task=edit&id=' . $row->get('id'), false);
            $ticketCreated  = $row->get('created');
            $timeFormatted  = Date::of($ticketCreated)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
            $dateFormatted  = Date::of($ticketCreated)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
          @endphp
          <a class="permalink" href="{{ $permalinkUrl }}" title="{{ Lang::txt('COM_SUPPORT_PERMALINK') }}">
            <span class="time-at">{{ Lang::txt('COM_SUPPORT_AT') }}</span>
            <span class="time"><time datetime="{{ $ticketCreated }}">{{ $timeFormatted }}</time></span>
            <span class="date-on">{{ Lang::txt('COM_SUPPORT_ON') }}</span>
            <span class="date"><time datetime="{{ $ticketCreated }}">{{ $dateFormatted }}</time></span>
          </a>
        </div>

        <blockquote class="ticket-content" cite="{{ $row->get('login', $row->get('name')) }}">
          <p>{!! $row->content !!}</p>
          @if ($row->attachments->count())
            <div class="comment-attachments">
              @foreach ($row->attachments as $attachment)
                @php
                  if (!trim($attachment->get('description'))) {
                      $attachment->set('description', $attachment->get('filename'));
                  }
                  $attLink = Route::url($attachment->link(), false);
                  $attDesc = $attachment->get('description');
                @endphp
                @if ($attachment->isImage())
                  @if ($attachment->width() > 400)
                    <p><a href="{{ $attLink }}"><img src="{{ $attLink }}" alt="{{ $attDesc }}" width="400" /></a></p>
                  @else
                    <p><img src="{{ $attLink }}" alt="{{ $attDesc }}" /></p>
                  @endif
                @else
                  <p class="attachment"><a href="{{ $attLink }}" title="{{ $attDesc }}">{{ $attDesc }}</a></p>
                @endif
              @endforeach
            </div>
          @endif
        </blockquote>

        <div class="ticket-details">
          <table>
            <tbody>
              <tr>
                <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_EMAIL') }}:</th>
                <td><a href="mailto:{{ $row->get('email') }}">{{ $row->get('email') }}</a></td>
              </tr>
              <tr>
                <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE') }}:</th>
                <td>{{ $usertype }}</td>
              </tr>
              <tr>
                <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_OS') }}:</th>
                <td>
                  {{ $row->get('os') }} / {{ $row->get('browser') }}
                  ({{ $row->get('cookies') ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED') : Lang::txt('COM_SUPPORT_COOKIES_DISABLED') }})
                </td>
              </tr>
              <tr>
                <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_IP') }}:</th>
                <td>{{ $row->get('ip') }} ({{ $row->get('hostname') }})</td>
              </tr>
              <tr>
                <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_REFERRER') }}:</th>
                <td>{{ $row->get('referrer', ' ') }}</td>
              </tr>
              <tr>
                <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_INSTANCES') }}:</th>
                <td>{{ $row->get('instances') }}</td>
              </tr>
              @if ($uas = $row->get('uas'))
                <tr><td colspan="2">{{ $row->get('uas') }}</td></tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>{{-- /.ticket --}}

  @if (!$no_html)
    </fieldset>
  </div>{{-- /.md:col-span-8 --}}

  {{-- Right sidebar: status + meta + watch --}}
  <div class="md:col-span-4">
    @php
      $isOpenClass = !$row->isOpen() ? 'closed' : 'open';
      $isOpenLabel = !$row->isOpen()
          ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED')
          : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN');
    @endphp
    <dl class="ticket-status {{ $isOpenClass }}">
      <dt>{{ Lang::txt('COM_SUPPORT_TICKET_STATUS') }}</dt>
      <dd>{{ $isOpenLabel }}</dd>
    </dl>

    <table class="meta">
      <tbody>
        <tr>
          <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_SEVERITY') }}</th>
          <td>{{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($row->get('severity'))) }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_OWNER') }}</th>
          <td>
            @if ($row->isOwned())
              @if ($row->assignee->get('id'))
                @php
                  $assigneeUrl  = Route::url('index.php?option=com_members&task=edit&id=' . $row->assignee->get('id'), false);
                  $assigneeDisp = $row->assignee->get('name');
                @endphp
                <a rel="profile" href="{{ $assigneeUrl }}">{{ $assigneeDisp }}</a>
              @else
                {{ $row->get('owner') }}
              @endif
            @else
              {{ Lang::txt('COM_SUPPORT_NONE') }}
            @endif
          </td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_LAST_ACTIVITY') }}</th>
          <td>{!! $lastactivity !!}</td>
        </tr>
      </tbody>
    </table>

    <div class="ticket-watch">
      @if ($row->isWatching())
        @php $stopWatchUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id') . '&watch=stop', false); @endphp
        <div id="watching">
          <p>{{ Lang::txt('COM_SUPPORT_WATCH_TICKET_IN_LIST') }}</p>
          <p><a class="stop-watching btn" href="{{ $stopWatchUrl }}">{{ Lang::txt('COM_SUPPORT_WATCH_TICKET_STOP_WATCHING') }}</a></p>
        </div>
      @else
        @php $startWatchUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id') . '&watch=start', false); @endphp
        <p><a class="start-watching btn" href="{{ $startWatchUrl }}">{{ Lang::txt('COM_SUPPORT_WATCH_TICKET_START_WATCHING') }}</a></p>
      @endif
      <p>{{ Lang::txt('COM_SUPPORT_WATCH_TICKET_ABOUT') }}</p>
    </div>
  </div>
  </div>{{-- /.grid --}}
  @endif

  {{-- ── Comments list ────────────────────────────────────────────────────── --}}
  @if ($no_html)
  <div class="ticket-comments">
  @endif

  @if ($row->comments->count() > 0)
    @if (!$no_html)
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    <div class="md:col-span-8">
    @endif
      <fieldset>
        <legend><span>{{ Lang::txt('COM_SUPPORT_TICKET_COMMENTS') }}</span></legend>
        <ol class="comments">
          @foreach ($row->comments as $comment)
            @php
              $access = 'public';
              if ($comment->isPrivate()) {
                  $access = 'private';
              }
              if ($comment->get('created_by') == $row->get('login') && !$comment->isPrivate()) {
                  $access = 'submitter';
              }

              $commentName = Lang::txt('COM_SUPPORT_UNKNOWN');
              $cite        = $commentName;
              if ($comment->creator->get('id')) {
                  $cite          = e($comment->creator->get('name'));
                  $creatorUrl    = Route::url('index.php?option=com_members&task=edit&id[]=' . $comment->creator->get('id'), false);
                  $commentName   = '<a href="' . $creatorUrl . '">'
                      . $cite . ' (' . e($comment->creator->get('username')) . ')</a>';
              }

              if ($comment->changelog()->format() != 'html') {
                  $cc = $comment->changelog()->get('cc');
              }

              $commentCreated   = $comment->get('created');
              $commentTimeLocal = Date::of($commentCreated)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
              $commentDateLocal = Date::of($commentCreated)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
              $commentPermalink = 'index.php?option=com_support&amp;controller=tickets&amp;task=edit&amp;id=' . $row->get('id') . '#c' . $comment->get('id');
            @endphp
            <li class="{{ $access }} comment" id="c{{ $comment->get('id') }}">
              <p class="comment-member-photo">
                <span class="comment-anchor"></span>
                <img src="{{ $comment->creator->picture() }}" alt="{{ Lang::txt('COM_SUPPORT_PROFILE_IMAGE') }}" />
              </p>
              <p class="comment-head">
                <strong>{!! $commentName !!}</strong>
                <a class="permalink" href="{{ $commentPermalink }}" title="{{ Lang::txt('COM_SUPPORT_PERMALINK') }}">
                  <span class="time-at">{{ Lang::txt('COM_SUPPORT_AT') }}</span>
                  <span class="time"><time datetime="{{ $commentCreated }}">{{ $commentTimeLocal }}</time></span>
                  <span class="date-on">{{ Lang::txt('COM_SUPPORT_ON') }}</span>
                  <span class="date"><time datetime="{{ $commentCreated }}">{{ $commentDateLocal }}</time></span>
                </a>
              </p>
              <blockquote class="comment-content" cite="{{ $cite }}">
                @if ($content = $comment->comment)
                  <p>{!! $content !!}</p>
                @else
                  <p class="comment-none">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_NO_CONTENT') }}</p>
                @endif
                @if ($comment->attachments->count())
                  <div class="comment-attachments">
                    @foreach ($comment->attachments as $attachment)
                      @php
                        if (!trim($attachment->get('description'))) {
                            $attachment->set('description', $attachment->get('filename'));
                        }
                        $attLink = Route::url($attachment->link(), false);
                        $attDesc = $attachment->get('description');
                      @endphp
                      @if ($attachment->isImage())
                        @if ($attachment->width() > 400)
                          <p><a href="{{ $attLink }}"><img src="{{ $attLink }}" alt="{{ $attDesc }}" width="400" /></a></p>
                        @else
                          <p><img src="{{ $attLink }}" alt="{{ $attDesc }}" /></p>
                        @endif
                      @else
                        <p class="attachment"><a href="{{ $attLink }}" title="{{ $attDesc }}">{{ $attDesc }}</a></p>
                      @endif
                    @endforeach
                  </div>
                @endif
              </blockquote>
              <div class="comment-changelog">
                {!! $comment->changelog()->render() !!}
              </div>
            </li>
          @endforeach
        </ol>
      </fieldset>
    @if (!$no_html)
    </div>
    <div class="md:col-span-4">
      <p><a class="new button" href="#commentform">{{ Lang::txt('COM_SUPPORT_TICKET_ADD_COMMENT') }}</a></p>
    </div>
    </div>{{-- /.grid --}}
    @endif
  @endif

  {{-- ── New comment / ticket details form ──────────────────────────────── --}}
  @if (!$no_html)
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
  <div class="md:col-span-8">
  @endif
    <fieldset id="commentform">
      <legend><span>{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS') }}</span></legend>

      <div class="new ticket">
        <p class="ticket-member-photo">
          <span class="ticket-anchor"></span>
          <img src="{{ $currentUser->picture(0) }}" alt="{{ Lang::txt('COM_SUPPORT_PROFILE_IMAGE') }}" />
        </p>

        <fieldset class="ticket-head">
          <strong>
            @php $userProfileUrl = Route::url('index.php?option=com_members&task=edit&id=' . e($currentUser->get('id')), false); @endphp
            <a rel="profile" href="{{ $userProfileUrl }}">
              {{ $currentUser->get('name') }} ({{ $currentUser->get('username') }})
            </a>
          </strong>
          <span class="permalink">
            <span class="time-at">{{ Lang::txt('COM_SUPPORT_AT') }}</span>
            <span class="time"><time>{{ Date::toLocal(Lang::txt('TIME_FORMAT_HZ1')) }}</time></span>
            <span class="date-on">{{ Lang::txt('COM_SUPPORT_ON') }}</span>
            <span class="date"><time>{{ Date::toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</time></span>
          </span>
          <label for="comment-field-access"
                 class="private hasTip"
                 title="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION') }}">
            <input type="checkbox" name="access" id="comment-field-access" value="1" />
            <span>{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_FIELD_ACCESS') }}</span>
          </label>
        </fieldset>

        <div class="ticket-content">
          @php
            $results = Event::trigger('support.onTicketComment', [$row]);
            echo implode("\n", $results);
          @endphp

          <fieldset>
            <div class="input-wrap">
              <label for="comment-field-template">
                <span class="label text-base-content">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_TEMPLATE') }}</span>
              </label>
              <select name="messages" id="comment-field-template"
                      aria-label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_TEMPLATE') }}">
                <option value="custom">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_CUSTOM') }}</option>
                @php $hi = []; @endphp
                @foreach ($lists['messages'] as $message)
                  @php
                    $msgTitle = $message->title;
                    $msgVal   = e($message->transformMessage($row->get('id')));
                    $hi[]     = '<input type="hidden" name="m' . $message->id . '" id="m' . $message->id . '" value="' . $msgVal . '" />';
                  @endphp
                  <option value="m{{ $message->id }}">{{ $msgTitle }}</option>
                @endforeach
              </select>
              {!! implode("\n", $hi) !!}

              <label for="comment-field-comment">
                <span class="label text-base-content">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_COMMENTS') }}</span>
              </label>
              <textarea name="comment" id="comment-field-comment" cols="75" rows="15"
                        aria-label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_COMMENTS') }}">{{ $comment->get('comment') }}</textarea>

              @if ($config->get('email_terse'))
                <label for="email_terse">
                  <input class="option" type="checkbox" name="email_terse" id="email_terse" value="1" checked="checked" />
                  {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_TERSE') }}
                </label>
              @endif
            </div>

            <fieldset>
              <legend>{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_ATTACHMENTS') }}</legend>
              @php
                if (!$no_html) {
                    $__view->js('jquery.fileuploader.js', 'system');
                }
                $uploadAction = Route::url('index.php?option=com_support&controller=media&task=upload&no_html=1&ticket=' . $row->get('id') . '&comment=' . $tmp, false);
                $listAction   = Route::url('index.php?option=com_support&controller=media&task=list&no_html=1&ticket=' . $row->get('id') . '&comment=' . $tmp, false);
              @endphp
              <div id="ajax-uploader"
                   data-action="{{ $uploadAction }}"
                   data-list="{{ $listAction }}"
                   data-instructions="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_INSTRUCTIONS') }}">
                <noscript>
                  <div class="input-wrap">
                    <label for="upload">
                      {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE') }}:
                      <input type="file" name="upload" id="upload" />
                    </label>
                  </div>
                  <div class="input-wrap">
                    <label for="field-description">
                      {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_DESCRIPTION') }}:
                      <input type="text" name="description" id="field-description" value="" />
                    </label>
                  </div>
                </noscript>
              </div>
              <div class="field-wrap file-list" id="ajax-uploader-list">
                @php
                  $__view->view('list', 'media')
                      ->set('model', $comment)
                      ->set('comment', $tmp)
                      ->set('ticket', $row->get('id'))
                      ->display();
                @endphp
              </div>
              <input type="hidden" name="tmp_dir" id="comment-tmp_dir" value="{{ $tmp }}" />
            </fieldset>

            <div class="input-wrap">
              <label for="comment-field-message">
                {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC') }}
                @php
                  $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'cc', 'comment-field-message', '', implode(', ', $cc)]]);
                @endphp
                @if (count($mc) > 0)
                  <span class="hint">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') }}</span>
                  {!! $mc[0] !!}
                @else
                  <span class="hint">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS') }}</span>
                  <input type="text" name="cc" id="comment-field-message" value="{{ implode(', ', $cc) }}" />
                @endif
              </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <div class="input-wrap">
                  <label for="email_submitter">
                    <input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" />
                    {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_SUBMITTER') }}
                  </label>
                </div>
              </div>
              <div>
                <div class="input-wrap">
                  <label for="email_owner">
                    <input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" />
                    {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_OWNER') }}
                  </label>
                </div>
              </div>
            </div>
          </fieldset>
        </div>{{-- /.ticket-content --}}

        <fieldset class="ticket-details">
          <div class="input-wrap">
            <label for="tags">
              {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_TAGS') }}
              @php
                $tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', $row->tags('string')]]);
              @endphp
              @if (count($tf) > 0)
                {!! $tf[0] !!}
              @else
                <input type="text" name="tags" id="tags" value="{{ $row->tags('string', null) }}" />
              @endif
            </label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="input-wrap">
                <label for="ticket-field-group">
                  {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_GROUP') }}:<br />
                  @php
                    $groupCn = '';
                    if ($row->get('group_id')) {
                        if ($g = \Hubzero\User\Group::getInstance($row->get('group_id'))) {
                            $groupCn = $g->get('cn');
                        }
                    }
                    $gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', [['groups', 'ticket[group_id]', 'acgroup', '', $groupCn, '', 'owner']]);
                  @endphp
                  @if (count($gc) > 0)
                    {!! $gc[0] !!}
                  @else
                    <input type="text" name="ticket[group_id]" value="{{ $row->get('group_id') }}" id="acgroup" size="30" autocomplete="off" />
                  @endif
                </label>
              </div>
            </div>
            <div>
              <div class="input-wrap">
                <label for="ticketowner">
                  {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OWNER') }}
                  {!! $lists['owner'] !!}
                </label>
              </div>
            </div>
          </div>

          @if (!empty($lists['categories']))
            <div class="input-wrap">
              <label for="ticket-field-category">
                {{ Lang::txt('COM_SUPPORT_TICKET_FIELD_CATEGORY') }}
                <select name="ticket[category]" id="ticket-field-category">
                  <option value="">{{ Lang::txt('COM_SUPPORT_NONE') }}</option>
                  @foreach ($lists['categories'] as $category)
                    <option value="{{ $category->alias }}"
                            {{ $category->alias == $row->get('category') ? 'selected="selected"' : '' }}>
                      {{ $category->title }}
                    </option>
                  @endforeach
                </select>
              </label>
            </div>
          @endif

          <div class="input-wrap">
            <label for="field-target_date">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_TARGET_DATE') }}</label>
            @php
              $targetDate = $row->get('target_date');
              $hasTarget  = $targetDate && $targetDate != '0000-00-00 00:00:00';
              $targetVal  = $hasTarget ? e(Date::of($targetDate)->toLocal('Y-m-d H:i:s')) : '';
              echo Html::input('calendar', 'ticket[target_date]', $targetVal, ['id' => 'field-target_date']);
            @endphp
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="input-wrap">
                <label for="ticket-field-severity">
                  {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEVERITY') }}
                  <select name="ticket[severity]" id="ticket-field-severity">
                    @foreach (\Components\Support\Helpers\Utilities::getSeverities() as $severity)
                      <option value="{{ $severity }}"
                              {{ $severity == $row->get('severity') ? 'selected="selected"' : '' }}>
                        {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)) }}
                      </option>
                    @endforeach
                  </select>
                </label>
              </div>
            </div>
            <div>
              <div class="input-wrap">
                <label for="ticket-field-status">
                  {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_STATUS') }}:
                  <select name="ticket[status]" id="ticket-field-status">
                    <optgroup label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN') }}">
                      @foreach (\Components\Support\Models\Status::allOpen()->rows() as $status)
                        <option value="{{ $status->get('id') }}"
                                {{ ($row->isOpen() && $row->get('status') == $status->get('id')) ? 'selected="selected"' : '' }}>
                          {{ $status->get('title') }}
                        </option>
                      @endforeach
                    </optgroup>
                    <optgroup label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED') }}">
                      <option value="0"
                              {{ (!$row->isOpen() && $row->get('status') == 0) ? 'selected="selected"' : '' }}>
                        {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED') }}
                      </option>
                      @foreach (\Components\Support\Models\Status::allClosed()->rows() as $status)
                        <option value="{{ $status->get('id') }}"
                                {{ (!$row->isOpen() && $row->get('status') == $status->get('id')) ? 'selected="selected"' : '' }}>
                          {{ $status->get('title') }}
                        </option>
                      @endforeach
                    </optgroup>
                  </select>
                </label>
              </div>
            </div>
          </div>
        </fieldset>{{-- /.ticket-details --}}
      </div>
    </fieldset>
  @if (!$no_html)
  </div>
  <div class="md:col-span-4">
    <p>{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION') }}</p>
  </div>
  </div>{{-- /.grid --}}
  @endif

  {{-- Hidden fields --}}
  <input type="hidden" name="started" value="{{ Date::toSql() }}" />
  <input type="hidden" name="id" id="ticketid" value="{{ $row->get('id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="username" value="{{ User::get('username') }}" />

  @if ($no_html)
    <p class="submit"><input type="submit" value="{{ Lang::txt('Save') }}" /></p>
    <input type="hidden" name="no_html" value="1" />
    <input type="hidden" name="task" value="apply" />
  </div>{{-- /.ticket-comments --}}
  @else
    <input type="hidden" name="task" value="save" />
  @endif

  {!! Html::input('token') !!}
</form>
