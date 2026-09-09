{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

$loggedin = !User::isGuest();
$isUser   = false;

$profiles = $profile->profiles()->ordered()->rows();

// Convert to XML so we can use the Form processor
$xml = \Components\Members\Models\Profile\Field::toXml($fields, 'edit');

// Gather data to pass to the form processor
$data = new Hubzero\Config\Registry(
    \Components\Members\Models\Profile::collect($profiles)
);

// Create a new form
Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');

$form = new Hubzero\Form\Form('profile', ['control' => 'profile']);
$form->load($xml);
$form->bind($data);

$fieldValues = [];
foreach ($profiles as $p) {
    if (isset($fieldValues[$p->get('profile_key')])) {
        $values = $fieldValues[$p->get('profile_key')]->get('profile_value');
        if (!is_array($values)) {
            $values = [$values];
        }
        $values[] = $p->get('profile_value');
        $fieldValues[$p->get('profile_key')]->set('profile_value', $values);
    } else {
        $fieldValues[$p->get('profile_key')] = $p;
    }
}
@endphp

@if ($membership_control == 1)
    @if ($authorized == 'manager' || $authorized == 'admin')
        <div class="flex flex-wrap gap-2 mb-4">
            @php
                $cn = $group->get('cn');
                $inviteUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&task=invite');
                $addRoleUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&active=members&action=addrole');
            @endphp
            <a class="btn btn-primary btn-sm"
                href="{{ $inviteUrl }}">
                {{ Lang::txt('PLG_GROUPS_MEMBERS_INVITE_MEMBERS') }}
            </a>
            @if ($membership_control == 1 && $authorized == 'manager')
                <a class="btn btn-primary btn-sm"
                    href="{{ $addRoleUrl }}">
                    {{ Lang::txt('PLG_GROUPS_MEMBERS_ADD_ROLE') }}
                </a>
            @endif
        </div>
    @endif
@endif

<div class="card bg-base-100 shadow">
    <div class="card-body">
        <h4 class="card-title text-lg">
            {{ e($profile->get('name')) }}
        </h4>

        {!! implode("\n", Event::trigger('groups.onGroupMemberBefore', [$group, $profile])) !!}

        <ul class="divide-y divide-base-300">
            {{-- Full profile link --}}
            <li class="py-3 flex flex-wrap gap-2 items-baseline">
                <div class="font-semibold min-w-[10rem]">
                    {{ Lang::txt('PLG_GROUPS_PROFILE_FULL') }}
                </div>
                <div>
                    <a href="{{ $profile->link() }}" class="link link-primary">
                        {{ Lang::txt('PLG_GROUPS_PROFILE_FULL_GO') }}
                    </a>
                </div>
            </li>

            {{-- Email --}}
            @if ($profile->get('email'))
                @if (
                    $params->get('access_email', 2) == 0
                    || ($params->get('access_email', 2) == 1 && $loggedin)
                    || ($params->get('access_email', 2) == 2 && $isUser)
                )
                    <li class="py-3 flex flex-wrap gap-2 items-baseline">
                        <div class="font-semibold min-w-[10rem]">
                            {{ Lang::txt('PLG_GROUPS_PROFILE_EMAIL') }}
                        </div>
                        <div>
                            @php
                                $obfEmail = \Hubzero\Utility\Str::obfuscate($profile->get('email'));
                            @endphp
                            <a class="link link-primary"
                                href="mailto:{!! $obfEmail !!}"
                                rel="nofollow">
                                {!! $obfEmail !!}
                            </a>
                        </div>
                    </li>
                @endif
            @endif

            {{-- Dynamic profile fields --}}
            @foreach ($fields as $field)
                @php
                    if (!isset($fieldValues[$field->get('name')])) {
                        $fieldValues[$field->get('name')] = \Components\Members\Models\Profile::blank();
                        $fieldValues[$field->get('name')]->set('access', 1);
                    }

                    $profileField = $fieldValues[$field->get('name')];
                    if (!$profileField->get('access')) {
                        $profileField->set('access', 5);
                    }
                @endphp

                @if (in_array($profileField->get('access', $field->get('access', 5)), User::getAuthorisedViewLevels()))
                    @php
                        $cls = ['profile-' . $field->get('name')];

                        if ($profileField->get('access', $field->get('access')) == 2) {
                            $cls[] = 'registered';
                        }
                        if ($profileField->get('access', $field->get('access')) == 5) {
                            $cls[] = 'private';
                        }

                        if ($field->get('type') == 'tags') {
                            $value = $profile->tags();
                        } else {
                            $value = $profileField->get('profile_value');
                            $value = $value ?: $profile->get($field->get('name'));
                        }

                        if (is_array($value)) {
                            foreach ($value as $k => $v) {
                                if (strstr($v, '{')) {
                                    $v = json_decode((string)$v, true);
                                    if (!$v || json_last_error() !== JSON_ERROR_NONE) {
                                        continue;
                                    }
                                    foreach ($v as $nm => $vl) {
                                        $v[$nm] = '<strong>' . $nm . ':</strong> ' . $vl;
                                    }
                                    $value[$k] = implode('<br />', $v);
                                }
                            }
                        } else {
                            if (strstr($value == null ? '' : $value, '{')) {
                                $v = json_decode((string)$value, true);
                                if (!$v || json_last_error() !== JSON_ERROR_NONE) {
                                    $v = [$value];
                                }
                                foreach ($v as $nm => $vl) {
                                    $v[$nm] = '<strong>' . $nm . ':</strong> ' . $vl;
                                }
                                $value = implode('<br />', $v);
                            }
                        }

                        if (empty($value)) {
                            $cls[] = 'hidden';
                        }

                        $displayValue = !empty($value)
                            ? (is_array($value) ? implode(', ', $value) : $value)
                            : '(not set)';
                    @endphp

                    @if (!empty($value))
                        <li class="py-3 flex flex-wrap gap-2 items-baseline {{ implode(' ', $cls) }}">
                            <div class="font-semibold min-w-[10rem]">
                                {{ $field->get('label') }}
                            </div>
                            <div>{!! $displayValue !!}</div>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>

        {!! implode("\n", Event::trigger('groups.onGroupMemberAfter', [$group, $profile])) !!}
    </div>
</div>
