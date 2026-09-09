{{--
 * Member result card partial for the browse/directory page
 *
 * Variables:
 *   $row         - Member record object
 *   $option      - Component option (e.g. 'com_members')
 *   $fields      - Collection of profile fields
 *   $messageuser - Whether current user can message this member
 *   $extras      - HTML from plugin events (members.onMemberProfile)
 *   $name        - Formatted display name
 *   $id          - Member ID (or 'n' prefixed for negative IDs)
 *   $cls         - CSS class string (private, me)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
    use Hubzero\Utility\Str;

    $profileUrl = Route::url('index.php?option=' . $option . '&id=' . $id);
    $avatarAlt = Lang::txt('COM_MEMBERS_BROWSE_AVATAR', e($name));
@endphp

<div class="card bg-base-100 shadow-sm {{ $cls }}">
    <div class="card-body flex-row gap-4 p-4">
        {{-- Avatar --}}
        <div class="avatar">
            <div class="w-16 rounded-full">
                <a href="{{ $profileUrl }}">
                    <img src="{{ $row->picture() }}" alt="{{ $avatarAlt }}" />
                </a>
            </div>
        </div>

        {{-- Details --}}
        <div class="flex-1 min-w-0">
            <h3 class="card-title text-base">
                <a href="{{ $profileUrl }}">{{ $name }}</a>
            </h3>

            {{-- Organization --}}
            @foreach ($fields as $c)
                @if (!in_array($c->get('name'), ['org', 'organization']))
                    @continue
                @endif
                @if ($val = $row->get($c->get('name')))
                    <p class="text-sm text-base-content/70 {{ e($c->get('name')) }}">
                        {{ e(Str::truncate(stripslashes($val), 60)) }}
                    </p>
                @endif
            @endforeach

            {{-- Other profile fields --}}
            @foreach ($fields as $c)
                @if (in_array($c->get('name'), ['name', 'org', 'organization']))
                    @continue
                @endif
                @if ($val = $row->get($c->get('name')))
                    @php
                        $val = is_array($val) ? implode(', ', $val) : $val;
                        $snippet = Str::truncate(strip_tags(stripslashes($val)), 150);
                    @endphp
                    <div class="text-sm text-base-content/60 result-snippet-{{ e($c->get('name')) }}">
                        {{ e($snippet) }}
                    </div>
                @endif
            @endforeach

            {{-- Actions: message button and plugin extras --}}
            @if ($extras || $messageuser)
                <div class="card-actions mt-2">
                    @if ($messageuser)
                        @php
                            $msgUrl = Route::url(
                                'index.php?option=' . $option
                                . '&id=' . User::get('id')
                                . '&active=messages&task=new&to[]=' . $row->get('id')
                            );
                            $msgTitle = Lang::txt(
                                'COM_MEMBERS_BROWSE_SEND_MESSAGE_TO_TITLE',
                                e($name)
                            );
                        @endphp
                        <a class="btn btn-sm btn-ghost"
                           href="{{ $msgUrl }}"
                           title="{{ $msgTitle }}">
                            {{ Lang::txt('COM_MEMBERS_BROWSE_SEND_MESSAGE') }}
                        </a>
                    @endif
                    @if ($extras)
                        {!! $extras !!}
                    @endif
                </div>
            @endif

            {{-- "Your profile" badge --}}
            @if (!User::isGuest() && User::get('id') == $row->get('id'))
                <span class="badge badge-primary mt-2">
                    {{ Lang::txt('COM_MEMBERS_BROWSE_YOUR_PROFILE') }}
                </span>
            @endif
        </div>
    </div>
</div>
