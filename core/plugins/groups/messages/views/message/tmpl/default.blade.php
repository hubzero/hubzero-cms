{{--
  Group Messages — single message view.

  Variables from plugin:
    $option     — component option string
    $group      — group object
    $no_html    — ajax mode flag
    $xmessage   — message object (id, created, created_by, subject, message, type, component)
    $authorized — user authorization level

  @package    hubzero-cms
  @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (substr($xmessage->type, -8) == '_message') {
      $u = User::getInstance($xmessage->created_by);
      $fromUrl = Route::url('index.php?option=' . $option . '&id=' . $u->get('id'));
      $from = '<a href="' . $fromUrl . '">' . e($u->get('name')) . '</a>';
  } else {
      $from = Lang::txt('System') . ' (' . e($xmessage->component) . ')';
  }

  $cn = $group->get('cn');
  $messagesUrl = Route::url(
      'index.php?option=' . $option . '&cn=' . $cn . '&active=messages'
  );
  $newMessageUrl = Route::url(
      'index.php?option=' . $option . '&cn=' . $cn . '&active=messages&action=new'
  );
@endphp

<div class="subject">
  @if (!$no_html)
    <div role="tablist" class="tabs tabs-border mb-4">
      <a role="tab" class="tab tab-active" href="{{ $messagesUrl }}">
        {{ Lang::txt('PLG_GROUPS_MESSAGES_SENT') }}
      </a>
      @if ($authorized == 'admin' || $authorized == 'manager')
        <a role="tab" class="tab" href="{{ $newMessageUrl }}">
          {{ Lang::txt('PLG_GROUPS_MESSAGES_SEND') }}
        </a>
      @endif
    </div>
  @endif

  @if (!$no_html)
    <div class="mb-4">
      <a class="btn btn-ghost btn-sm" href="{{ $messagesUrl }}">
        &larr; {{ Lang::txt('&lsaquo; Back to Sent Messages') }}
      </a>
    </div>
  @endif

  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">
      <h2 class="card-title text-xl mb-4">
        {{ stripslashes($xmessage->subject) }}
      </h2>

      <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm opacity-70 mb-6">
        <div>
          <span class="font-medium">{{ Lang::txt('PLG_GROUPS_MESSAGES_FROM') }}:</span>
          {!! $from !!}
        </div>
        <div>
          <span class="font-medium">{{ Lang::txt('PLG_GROUPS_MESSAGES_RECEIVED') }}:</span>
          <time datetime="{{ $xmessage->created }}">
            {{ Date::of($xmessage->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
          </time>
        </div>
      </div>

      <div class="divider"></div>

      <div class="prose max-w-none">
        {!! $xmessage->message !!}
      </div>
    </div>
  </div>
</div>
