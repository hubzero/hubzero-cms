{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$__view->css();
$__view->js();

$subject = stripslashes($xmessage->get('subject'));
$component = $xmessage->get('component');
if ($component == 'support' || $component == 'com_support') {
    $parts = explode(' ', $subject);
    array_pop($parts);
    $subject = implode(' ', $parts);
}

$componentDisplay = (substr($component, 0, 4) == 'com_')
    ? substr($component, 4)
    : $component;
@endphp

<div class="mb-4">
  <a class="btn btn-ghost btn-sm"
     href="{{ Route::url($member->link() . '&active=messages&task=inbox') }}">
    &larr; {{ Lang::txt('PLG_MEMBERS_MESSAGES_INBOX') }}
  </a>
</div>

<div class="card bg-base-100 shadow-sm">
  <div class="card-body">
    <h2 class="card-title text-xl mb-4">{{ $subject }}</h2>

    <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm opacity-70 mb-6">
      <div>
        <span class="font-medium">{{ Lang::txt('PLG_MEMBERS_MESSAGES_FROM') }}:</span>
        @if (substr($xmessage->get('type'), -8) == '_message')
          @if ($xmessage->anonymous)
            {{ Lang::txt('JANONYMOUS') }}
          @else
            @php
              $creator = $xmessage->creator;
              $creatorUrl = Route::url(
                  'index.php?option=' . $option . '&id=' . $creator->get('id')
              );
            @endphp
            <a href="{{ $creatorUrl }}">{{ $creator->get('name') }}</a>
          @endif
        @else
          {{ Lang::txt('PLG_MEMBERS_MESSAGES_SYSTEM', $componentDisplay) }}
        @endif
      </div>
      <div>
        <span class="font-medium">{{ Lang::txt('PLG_MEMBERS_MESSAGES_DATE_RECEIVED') }}:</span>
        <time datetime="{{ $xmessage->get('created') }}">
          {{ Date::of($xmessage->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
        </time>
      </div>
      @if ($componentDisplay)
        <div>
          <span class="font-medium">{{ Lang::txt('PLG_MEMBERS_MESSAGES_COMPONENT') ?? 'Component' }}:</span>
          {{ $componentDisplay }}
        </div>
      @endif
    </div>

    <div class="divider"></div>

    <div class="prose max-w-none">
      {!! $xmessage->message !!}
    </div>
  </div>
</div>
