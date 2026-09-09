{{--
 * Single project card in browse/list view
 *
 * Variables:
 *   $row     - Project model object
 *   $option  - Component option string
 *   $filters - Active filters array
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
    use Hubzero\Facades\Event;

    $isReviewer = isset($filters['reviewer']) && in_array(
        $filters['reviewer'],
        ['sponsored', 'sensitive']
    );

    $src = Route::url($row->picture('master'));
    $truncTitle = e(\Hubzero\Utility\Str::truncate($row->get('title'), 60));

    $collabLabel = Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
    $role = $row->access('member')
        ? ($row->access('manager')
            ? Lang::txt('COM_PROJECTS_LABEL_OWNER')
            : $collabLabel)
        : '';

    $role = $row->access('readonly') && !$row->isArchived()
        ? Lang::txt('COM_PROJECTS_LABEL_REVIEWER')
        : $role;

    // Privacy label and icon
    $privacyTxt = Lang::txt('COM_PROJECTS_PRIVATE');
    $privacyIcon = '<svg class="h-4 w-4 inline-block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>';

    if ($row->access('member')) {
        $privacyIcon = '<svg class="h-4 w-4 inline-block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M14.5 1A4.5 4.5 0 0 0 10 5.5V9H3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-1.5V5.5a3 3 0 1 1 6 0v2.75a.75.75 0 0 0 1.5 0V5.5A4.5 4.5 0 0 0 14.5 1Z" clip-rule="evenodd"/></svg>';
    }

    if ($row->isArchived()) {
        $privacyTxt = Lang::txt('COM_PROJECTS_ARCHIVED');
        $privacyIcon = '<svg class="h-4 w-4 inline-block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Z"/><path fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM7 11a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z" clip-rule="evenodd"/></svg>';
    }

    $canAccess = (!$row->inSetup() && $row->access('view'))
        || ($row->inSetup() && $row->access('owner'));
@endphp

<div class="card bg-base-100 shadow-sm" id="project-{{ $row->get('id') }}">
    <div class="card-body p-4">
        <div class="flex gap-4">
            {{-- Project image --}}
            @if ($canAccess)
                <a href="{{ Route::url($row->link()) }}" class="shrink-0">
                    <img
                        src="{{ $src }}"
                        alt="{{ e($row->get('title')) }}"
                        class="w-24 h-24 rounded-lg object-cover"
                    />
                </a>
            @else
                <div class="shrink-0">
                    <img
                        src="{{ $src }}"
                        alt="{{ e($row->get('title')) }}"
                        class="w-24 h-24 rounded-lg object-cover"
                    />
                </div>
            @endif

            {{-- Project info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="text-xs text-muted-foreground">
                            {{ e($row->get('alias')) }}
                        </span>
                        @if ($canAccess)
                            <h3 class="text-base font-semibold">
                                <a
                                    class="link link-hover text-primary"
                                    href="{{ Route::url($row->link()) }}"
                                >{{ $truncTitle }}</a>
                            </h3>
                        @else
                            <h3 class="text-base font-semibold">
                                {{ e(\Hubzero\Utility\Str::truncate($row->get('title'), 60)) }}
                            </h3>
                        @endif
                    </div>

                    {{-- Badges --}}
                    <div class="flex items-center gap-1 shrink-0">
                        @if ($row->get('featured'))
                            <span
                                class="badge badge-warning badge-sm gap-1"
                                title="{{ Lang::txt('COM_PROJECTS_FEATURED') }}"
                            >
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd"/>
                                </svg>
                                {{ Lang::txt('COM_PROJECTS_FEATURED') }}
                            </span>
                        @endif

                        @php
                            $setup = $row->inSetup() ? Lang::txt('COM_PROJECTS_COMPLETE_SETUP') : '';
                        @endphp

                        @if ($setup && $row->access('member'))
                            <span class="badge badge-info badge-sm">
                                {!! Lang::txt('COM_PROJECTS_COMPLETE_SETUP') !!}
                            </span>
                        @elseif ($row->get('state') == 0)
                            <span class="badge badge-error badge-sm">
                                {{ Lang::txt('COM_PROJECTS_STATUS_INACTIVE') }}
                            </span>
                        @elseif ($row->get('state') == 5)
                            <span class="badge badge-warning badge-sm">
                                {{ Lang::txt('COM_PROJECTS_STATUS_PENDING') }}
                            </span>
                        @endif

                        @if ($role)
                            <span class="badge badge-outline badge-sm">
                                {{ $role }}
                            </span>
                        @endif

                        <span
                            class="badge badge-ghost badge-sm gap-1"
                            title="{{ $privacyTxt }}"
                        >
                            {!! $privacyIcon !!}
                            {{ $privacyTxt }}
                        </span>
                    </div>
                </div>

                {{-- Owner info --}}
                <div class="text-sm text-muted-foreground mt-1">
                    @if ($row->groupOwner())
                        @php
                            $groupUrl = Route::url(
                                'index.php?option=com_groups&cn=' . $row->groupOwner('cn')
                            );
                            $groupDesc = e(\Hubzero\Utility\Str::truncate(
                                $row->groupOwner('description'),
                                25
                            ));
                            $spanTitle = e(Lang::txt(
                                'This project is owned by the %s group',
                                $row->groupOwner('description')
                            ));
                        @endphp
                        <span title="{{ $spanTitle }}">
                            <svg class="h-3.5 w-3.5 inline-block mr-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M7 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM14.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.615 16.428a1.224 1.224 0 0 1-.569-1.175 6.002 6.002 0 0 1 11.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 0 1 7 18a9.953 9.953 0 0 1-5.385-1.572ZM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 0 0-1.588-3.755 4.502 4.502 0 0 1 5.874 2.636.818.818 0 0 1-.36.98A7.465 7.465 0 0 1 14.5 16Z"/>
                            </svg>
                            <a class="link link-hover" href="{{ $groupUrl }}">
                                {{ $groupDesc }}
                            </a>
                        </span>
                    @else
                        @php
                            $owner = $row->owner();
                            $name = Lang::txt('COM_PROJECTS_UNKNOWN');
                            if ($owner->get('id')) {
                                $name = $owner->get('name');
                            }
                            $ownerTitle = e(Lang::txt(
                                'This project is owned by %s',
                                $name
                            ));
                        @endphp
                        <span title="{{ $ownerTitle }}">
                            <svg class="h-3.5 w-3.5 inline-block mr-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"/>
                            </svg>
                            @if ($owner->get('id') && in_array($owner->get('access'), User::getAuthorisedViewLevels()))
                                <a
                                    class="link link-hover"
                                    href="{{ Route::url('index.php?option=com_members&id=' . $owner->get('id')) }}"
                                >{{ e($name) }}</a>
                            @else
                                {{ $name }}
                            @endif
                        </span>
                    @endif

                    @if ($isReviewer && $row->owner())
                        <span class="block">{{ $row->owner('email') }}</span>
                        @if ($row->owner('phone'))
                            <span class="block">Tel. {{ $row->owner('phone') }}</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Reviewer data sections --}}
        @if ($isReviewer)
            @php
                $params = new \Hubzero\Config\Registry($row->get('params'));
            @endphp

            @if ($filters['reviewer'] == 'sensitive')
                <div class="mt-4 pt-4 border-t border-base-200">
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if ($params->get('hipaa_data') == 'yes')
                            <span class="badge badge-outline badge-sm">
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_HIPAA') }}
                            </span>
                        @endif
                        @if ($params->get('ferpa_data') == 'yes')
                            <span class="badge badge-outline badge-sm">
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_FERPA') }}
                            </span>
                        @endif
                        @if ($params->get('export_data') == 'yes')
                            <span class="badge badge-outline badge-sm">
                                {{ Lang::txt('COM_PROJECTS_SETUP_EXPORT_CONTROLLED') }}
                            </span>
                        @endif
                        @if ($params->get('irb_data') == 'yes')
                            <span class="badge badge-outline badge-sm">
                                {{ Lang::txt('COM_PROJECTS_SETUP_IRB') }}
                            </span>
                        @endif
                        @if ($params->get('restricted_data') == 'maybe' && $params->get('followup') == 'yes')
                            <span class="badge badge-outline badge-sm">
                                {{ Lang::txt('COM_PROJECTS_SETUP_FOLLOW_UP_NECESSARY') }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            @if ($row->isActive())
                                <span class="badge badge-success badge-sm">
                                    {{ Lang::txt('COM_PROJECTS_ACTIVE') }}
                                </span>
                            @elseif ($row->inSetup())
                                <span class="badge badge-info badge-sm">
                                    {{ Lang::txt('COM_PROJECTS_STATUS_SETUP') }}
                                </span>
                            @elseif ($row->isInactive())
                                <span class="badge badge-error badge-sm">
                                    {{ Lang::txt('COM_PROJECTS_STATUS_INACTIVE') }}
                                </span>
                            @elseif ($row->isArchived())
                                <span class="badge badge-ghost badge-sm">
                                    {{ Lang::txt('COM_PROJECTS_STATUS_ARCHIVED') }}
                                </span>
                            @elseif ($row->isPending())
                                <span class="badge badge-warning badge-sm">
                                    {{ Lang::txt('COM_PROJECTS_STATUS_PENDING') }}
                                </span>
                            @endif
                        </div>
                        <div>
                            @php
                                $commentCount = 0;
                                if ($row->get('admin_notes')) {
                                    $notes = $row->get('admin_notes');
                                    $commentCount = \Components\Projects\Helpers\Html::getAdminNoteCount(
                                        $notes,
                                        'sensitive'
                                    );
                                    $lastNote = \Components\Projects\Helpers\Html::getLastAdminNote(
                                        $notes,
                                        'sensitive'
                                    );
                                }
                                $processUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&task=process&id=' . $row->get('id')
                                    . '&reviewer=' . $filters['reviewer']
                                );
                            @endphp

                            @if (isset($lastNote))
                                <div class="text-sm mb-1">{!! $lastNote !!}</div>
                            @endif

                            <a href="{{ $processUrl }}" class="link link-hover text-sm">
                                {{ $commentCount }} {{ Lang::txt('COM_PROJECTS_COMMENTS') }}
                            </a>

                            @if ($row->isPending())
                                <a
                                    href="{{ $processUrl }}"
                                    class="btn btn-xs btn-primary ml-2"
                                >{{ Lang::txt('COM_PROJECTS_APPROVE') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($filters['reviewer'] == 'sponsored')
                <div class="mt-4 pt-4 border-t border-base-200">
                    <table class="table table-sm">
                        <caption class="text-left font-semibold text-sm mb-2">
                            {{ Lang::txt('COM_PROJECTS_SPS_INFO') }}
                        </caption>
                        <tbody>
                            @foreach (['title', 'PI', 'agency', 'budget'] as $key)
                                <tr>
                                    <th class="text-muted-foreground font-medium" scope="row">
                                        {{ Lang::txt('COM_PROJECTS_GRANT_' . strtoupper($key)) }}
                                    </th>
                                    <td>{{ e($params->get('grant_' . $key)) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th class="text-muted-foreground font-medium" scope="row">
                                    {{ Lang::txt('Status') }}
                                </th>
                                <td>
                                    @if (!$params->get('grant_approval') && $params->get('grant_status', 0) == 0)
                                        <span class="badge badge-warning badge-sm">
                                            {{ Lang::txt('COM_PROJECTS_STATUS_PENDING_SPS') }}
                                        </span>
                                    @elseif ($params->get('grant_approval') || $params->get('grant_status') == 1)
                                        <span class="badge badge-success badge-sm">
                                            {{ Lang::txt('COM_PROJECTS_APPROVAL_CODE') }}:
                                            {{ $params->get('grant_approval', '(N/A)') }}
                                        </span>
                                    @elseif ($params->get('grant_status') == '2')
                                        <span class="badge badge-error badge-sm">
                                            {{ Lang::txt('COM_PROJECTS_STATUS_SPS_REJECTED') }}
                                        </span>
                                    @endif

                                    @php
                                        $manageUrl = Route::url(
                                            'index.php?option=' . $option
                                            . '&task=process&id=' . $row->get('id')
                                        )
                                            . '?reviewer=' . $filters['reviewer']
                                            . '&filterby=' . $filters['filterby'];
                                    @endphp
                                    <a
                                        href="{{ $manageUrl }}"
                                        class="btn btn-xs btn-ghost ml-2"
                                    >{{ Lang::txt('COM_PROJECTS_MANAGE') }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        @endif

        {{-- Plugin extras --}}
        @php
            $results = Event::trigger('projects.onProjectsBrowse', [$row]);
        @endphp

        @if (!empty($results))
            <div class="mt-4 pt-4 border-t border-base-200">
                {!! implode("\n", $results) !!}
            </div>
        @endif
    </div>
</div>
