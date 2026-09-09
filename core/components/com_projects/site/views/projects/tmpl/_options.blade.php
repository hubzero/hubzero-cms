{{--
 * Project member options dropdown partial
 *
 * Variables:
 *   $model  - Project model object
 *   $option - Component option string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $role  = Lang::txt('COM_PROJECTS_PROJECT') . ' <span>';
    if ($model->access('manager')) {
        $role .= Lang::txt('COM_PROJECTS_LABEL_OWNER');
    } elseif (!$model->access('content')) {
        $role .= Lang::txt('COM_PROJECTS_LABEL_REVIEWER');
    } else {
        $role .= Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
    }
    $role .= '</span>';

    $counts = $model->get('counts');
    $member = $model->member();

    $editUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&task=edit'
    );
    $inviteUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&task=edit&active=team'
    );
    $previewUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&preview=1'
    );
    $leaveUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&active=team&action=quit'
    );
@endphp

<div class="dropdown dropdown-end" id="member_options">
    <div tabindex="0" role="button" class="btn btn-sm btn-ghost gap-1">
        {!! ucfirst($role) !!}
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </div>
    <div tabindex="0" id="options-dock" class="dropdown-content z-10 card card-compact bg-base-100 shadow-lg w-64 p-2">
        <div class="card-body">
            <p class="text-sm text-base-content/60">{{ Lang::txt('COM_PROJECTS_JOINED') }} {{ $model->created('date') }}</p>
            <ul class="menu menu-sm p-0">
                @if ($model->access('manager'))
                    <li><a href="{{ $editUrl }}">{{ Lang::txt('COM_PROJECTS_EDIT_PROJECT') }}</a></li>
                    <li><a href="{{ $inviteUrl }}">{{ Lang::txt('COM_PROJECTS_INVITE_PEOPLE') }}</a></li>
                @endif
                @if ($model->isPublic())
                    <li>
                        <a href="{{ $previewUrl }}">
                            {{ Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') }}
                        </a>
                    </li>
                @endif
                @if (isset($counts['team']) && $counts['team'] > 1 && $member && $member->get('status') == 1)
                    <li><a href="{{ $leaveUrl }}" class="text-error">{{ Lang::txt('COM_PROJECTS_LEAVE_PROJECT') }}</a></li>
                @endif
            </ul>
        </div>
    </div>
</div>
