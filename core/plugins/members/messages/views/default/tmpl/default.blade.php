{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

$no_html = Request::getInt('no_html', 0);

$sections = [
    ['name' => 'inbox',   'title' => Lang::txt('PLG_MEMBERS_MESSAGES_INBOX')],
    ['name' => 'sent',    'title' => Lang::txt('PLG_MEMBERS_MESSAGES_SENT')],
    ['name' => 'archive', 'title' => Lang::txt('PLG_MEMBERS_MESSAGES_ARCHIVE')],
    ['name' => 'trash',   'title' => Lang::txt('PLG_MEMBERS_MESSAGES_TRASH')],
    ['name' => 'new',     'title' => Lang::txt('PLG_MEMBERS_MESSAGES_COMPOSE')],
];
@endphp

@if (!$no_html)
<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_MESSAGES') }}</h3>

<div class="flex flex-wrap items-center justify-between gap-2 mb-4">
  <div role="tablist" class="tabs tabs-border">
    @foreach ($sections as $s)
      @php
        $tabUrl = Route::url(
            $member->link()
            . '&active=messages&task=' . $s['name']
            . '&limit=' . $filters['limit']
            . '&start=0'
        );
      @endphp
      <a role="tab"
         class="tab {{ $task == $s['name'] ? 'tab-active' : '' }}"
         href="{{ $tabUrl }}">
        {{ $s['title'] }}
      </a>
    @endforeach
  </div>
  <a class="btn btn-ghost btn-sm"
     href="{{ Route::url($member->link() . '&active=messages&task=settings') }}">
    {{ Lang::txt('PLG_MEMBERS_MESSAGES_SETTINGS') }}
  </a>
</div>

@foreach ($notifications as $n)
  <div class="alert {{ $n['type'] == 'error' ? 'alert-error' : 'alert-info' }} mb-2">
    <span>{{ $n['message'] }}</span>
  </div>
@endforeach
@endif

{!! $body !!}
