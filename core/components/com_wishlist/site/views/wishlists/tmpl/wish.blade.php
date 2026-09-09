{{--
 * Single wish detail — voting, comments, plan, admin actions
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Session;
    use Hubzero\Facades\User;

    $error = $__view->getError();

    // Proposer name & image
    $name = Lang::txt('JANONYMOUS');
    $memberImage = '';
    if (!$wish->get('anonymous')) {
        $name = e(stripslashes($wish->proposer->get('name', $name)));
        if (in_array($wish->proposer->get('access'), User::getAuthorisedViewLevels())) {
            $proposerLink = Route::url($wish->proposer->link(), false);
            $name = '<a class="link link-hover" href="' . $proposerLink . '">' . $name . '</a>';
        }
        $memberImage = $wish->proposer->picture();
    }

    // Assignment info
    $assigned = '';
    $editplanUrl = Route::url(
        'index.php?option=' . $option
        . '&task=wish&category=' . $wishlist->get('category')
        . '&rid=' . $wishlist->get('referenceid')
        . '&wishid=' . $wish->get('id'),
        false
    ) . '?filterby=' . $filters['filterby']
        . '&sortby=' . $filters['sortby']
        . '&tags=' . $filters['tag']
        . '&action=editplan#plan';

    if ($wish->get('assigned')) {
        $assigned = Lang::txt(
            'COM_WISHLIST_WISH_ASSIGNED_TO',
            '<a href="' . $editplanUrl . '">' . $wish->assignee->get('name') . '</a>'
        );
    }

    $adminIsOwnerOrAdmin = ($wish->get('admin') == 2 || $wish->get('admin') == 1);
    if (!$assigned && $adminIsOwnerOrAdmin && $wish->get('status') == 0) {
        $assigned = '<a href="' . $editplanUrl . '">' . Lang::txt('COM_WISHLIST_UNASSIGNED') . '</a>';
    }

    $acceptedAndOpen = ($wish->get('accepted') == 1 && $wish->get('status') == 0);
    $wish->set('status', ($acceptedAndOpen ? 6 : $wish->get('status')));
    $due = ($wish->get('due') && $wish->get('due') != '0000-00-00 00:00:00')
        ? Date::of($wish->get('due'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : '';

    $wish->set('positive', $wish->votes()->whereEquals('helpful', 'yes')->total());
    $wish->set('negative', $wish->votes()->whereEquals('helpful', 'no')->total());

    $filterln = '&' . Session::getFormToken() . '=1';
    foreach ($filters as $key => $val) {
        if ($val && $key !== 'comments') {
            $filterln .= '&' . $key . '=' . $val;
        }
    }

    $voteDisabled = User::isGuest()
        || User::get('id') == $wish->get('proposed_by')
        || $wish->get('status') == 1
        || $wish->get('status') == 3
        || $wish->get('status') == 4;

    $likeUrl = $voteDisabled ? '' : Route::url(
        'index.php?option=com_wishlist&task=rateitem&refid=' . $wish->get('id')
        . '&vote=yes&page=wish' . $filterln,
        false
    );
    $dislikeUrl = $voteDisabled ? '' : Route::url(
        'index.php?option=com_wishlist&task=rateitem&refid=' . $wish->get('id')
        . '&vote=no&page=wish' . $filterln,
        false
    );
@endphp

<x-page-container :title="$__view->title . ': ' . Lang::txt('COM_WISHLIST_WISH') . ' #' . $wish->get('id')">
    @slot('sidebar')
        <x-sidebar-card :title="Lang::txt('COM_WISHLIST_STATUS')">
            @php
                $statusAlias = $wish->status('alias');
                $statusBadge = match($statusAlias) {
                    'granted'   => 'badge-success',
                    'rejected'  => 'badge-error',
                    'withdrawn' => 'badge-warning',
                    'accepted'  => 'badge-info',
                    default     => 'badge-ghost',
                };
            @endphp
            <p>
                @if($wishlist->access('manage'))
                    <a class="badge {{ $statusBadge }}"
                       href="{{ Route::url($wish->link('changestatus'), false) }}">
                        {{ $wish->status('text') }}
                    </a>
                @else
                    <span class="badge {{ $statusBadge }}">
                        {{ $wish->status('text') }}
                    </span>
                @endif
            </p>
            @if($wishlist->access('manage') && $wish->status('note'))
                <p class="text-sm text-base-content/60 mt-1">
                    {{ $wish->status('note') }}
                </p>
            @endif
        </x-sidebar-card>
    @endslot

    {{-- Success messages --}}
    @if(!$error)
        @if($wish->get('saved') == 3)
            <div role="alert" class="alert alert-success mb-4">
                <span>{{ Lang::txt('COM_WISHLIST_NOTICE_WISH_CREATED') }}</span>
            </div>
        @endif
        @if($wish->get('saved') == 2 && $wishlist->access('manage'))
            <div role="alert" class="alert alert-success mb-4">
                <span>{{ Lang::txt('COM_WISHLIST_NOTICE_WISH_CHANGES_SAVED') }}</span>
            </div>
        @endif
    @endif

    @if($error)
        <div role="alert" class="alert alert-error mb-4">
            <span>{{ $error }}</span>
        </div>
    @endif

    {{-- Reported / withdrawn states --}}
    @if($wish->isReported())
        <div role="alert" class="alert alert-warning">
            <span>{{ Lang::txt('COM_WISHLIST_NOTICE_POSTING_REPORTED') }}</span>
        </div>
    @elseif($wish->isDeleted() || (!$wish->get('admin') && $wish->isWithdrawn()))
        <div role="alert" class="alert alert-warning">
            <span>{{ Lang::txt('COM_WISHLIST_NOTICE_WISH_WITHDRAWN') }}</span>
        </div>
    @else
        {{-- Main wish content --}}
        <article class="card bg-base-100 shadow-sm mb-6" id="w{{ $wish->get('id') }}">
            <div class="card-body">
                <div class="flex gap-4">
                    {{-- Author photo --}}
                    @if($memberImage)
                        <div class="shrink-0">
                            <img src="{{ $memberImage }}"
                                 alt="{{ Lang::txt('COM_WISHLIST_MEMBER_PICTURE') }}"
                                 class="w-12 h-12 rounded-full" />
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        {{-- Author and date --}}
                        <div class="flex items-baseline gap-2 text-sm text-base-content/60 mb-1">
                            <span class="font-medium text-base-content">{!! $name !!}</span>
                            <a class="link link-hover"
                               href="{{ Route::url($wish->link(), false) }}"
                               title="{{ Lang::txt('COM_WISHLIST_PERMALINK') }}">
                                <time datetime="{{ $wish->proposed() }}">
                                    {{ $wish->proposed('date') }}
                                    {{ Lang::txt('COM_WISHLIST_AT') }}
                                    {{ $wish->proposed('time') }}
                                </time>
                            </a>
                        </div>

                        {{-- Subject --}}
                        <h2 class="text-lg font-semibold mb-2">
                            {{ e(stripslashes($wish->get('subject'))) }}
                        </h2>

                        {{-- Description --}}
                        @if($content = $wish->content)
                            <div class="prose prose-sm max-w-none mb-3">
                                {!! $content !!}
                            </div>
                        @endif

                        {{-- Tags --}}
                        @if($tags = $wish->tags('string'))
                            <div class="mb-3">
                                {!! $tags !!}
                            </div>
                        @endif

                        {{-- Vote widget --}}
                        <div class="flex items-center gap-3 mt-2">
                            <x-vote-widget
                                :likes="$wish->get('positive', 0)"
                                :dislikes="$wish->get('negative', 0)"
                                vote=""
                                :likeUrl="$likeUrl"
                                :dislikeUrl="$dislikeUrl"
                                :disabled="$voteDisabled"
                            />

                            @if($wishlist->get('banking') && $wish->get('bonus', 0) > 0
                                && ($wish->isOpen() || $wish->isAccepted()))
                                <span class="badge badge-warning badge-sm">
                                    {{ $wish->get('bonus') }} {{ Lang::txt('COM_WISHLIST_POINTS') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Due date indicator (admin only) --}}
                @if($wishlist->access('manage'))
                    @php
                        $hasDue = $wish->due() && $wish->due() != '0000-00-00 00:00:00';
                    @endphp
                    @if($hasDue && !$wish->isGranted())
                        @php
                            $editplanLinkUrl = Route::url($wish->link('editplan'), false);
                            $isOverdue = ($wish->get('due') <= Date::of('now')->toSql());
                        @endphp
                        <div class="mt-3">
                            <a href="{{ $editplanLinkUrl }}"
                               class="badge {{ $isOverdue ? 'badge-error' : 'badge-info' }} badge-sm">
                                @if($isOverdue)
                                    {{ Lang::txt('COM_WISHLIST_OVERDUE') }}
                                @else
                                    {{ Lang::txt('COM_WISHLIST_WISH_DUE_IN') }}
                                    {{ \Components\Wishlist\Helpers\Html::nicetime($wish->get('due')) }}
                                @endif
                            </a>
                        </div>
                    @endif
                @endif

                {{-- Action links --}}
                <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t border-base-200">
                    @if($wishlist->access('admin') && $wishlist->get('admin') != 3)
                        @if(!$wish->isGranted())
                            <a class="btn btn-xs btn-ghost"
                               href="{{ Route::url($wish->link('changestatus'), false) }}">
                                {{ Lang::txt('COM_WISHLIST_ACTION_CHANGE_STATUS') }}
                            </a>
                        @endif
                        <a class="btn btn-xs btn-ghost"
                           href="{{ Route::url($wish->link('move'), false) }}">
                            {{ Lang::txt('COM_WISHLIST_MOVE') }}
                        </a>
                        @if($wish->isPrivate())
                            <a class="btn btn-xs btn-ghost"
                               href="{{ Route::url($wish->link('privacy', ['private' => '0']), false) }}">
                                {{ Lang::txt('COM_WISHLIST_MAKE_PUBLIC') }}
                            </a>
                        @else
                            <a class="btn btn-xs btn-ghost"
                               href="{{ Route::url($wish->link('privacy', ['private' => '1']), false) }}">
                                {{ Lang::txt('COM_WISHLIST_MAKE_PRIVATE') }}
                            </a>
                        @endif
                    @endif

                    @php
                        $canEdit = ($wishlist->access('manage') && $wishlist->get('admin') != 3)
                            || User::get('id') == $wish->get('proposed_by');
                    @endphp
                    @if($canEdit)
                        <a class="btn btn-xs btn-ghost"
                           href="{{ Route::url($wish->link('edit'), false) }}">
                            {{ ucfirst(Lang::txt('COM_WISHLIST_ACTION_EDIT')) }}
                        </a>
                    @endif

                    <a class="btn btn-xs btn-ghost"
                       href="{{ Route::url($wish->link('report'), false) }}">
                        {{ Lang::txt('COM_WISHLIST_REPORT_ABUSE') }}
                    </a>

                    @if(User::get('id') == $wish->get('proposed_by') && $wish->isOpen())
                        <a class="btn btn-xs btn-ghost text-error"
                           href="{{ Route::url($wish->link('withdraw'), false) }}">
                            {{ Lang::txt('COM_WISHLIST_ACTION_WITHDRAW_WISH') }}
                        </a>
                    @endif
                </div>
            </div>
        </article>

        {{-- Priority ranking table (admin only) --}}
        @if($wishlist->access('manage') && !$wish->isDeleted() && !$wish->isWithdrawn())
            @php
                $owners = $wishlist->getOwners();
                $eligible = array_unique(array_merge($owners['individuals'], $owners['advisory']));
                $voters = ($wish->votes()->total() <= count($eligible))
                    ? count($eligible) : $wish->votes()->total();
            @endphp
            @if($voters > 0)
                <div class="card bg-base-100 shadow-sm mb-6">
                    <div class="card-body">
                        <form method="post"
                              action="{{ Route::url('index.php?option=' . $option, false) }}"
                              id="rankForm">
                            <h3 class="font-semibold mb-2">
                                {{ Lang::txt('COM_WISHLIST_PRIORITY') }}:
                                <strong>{{ $wish->get('ranking') }}</strong>
                                <span class="text-sm text-base-content/60">
                                    ({{ $wish->rankings()->total('count') }}
                                    {{ Lang::txt('COM_WISHLIST_NOTICE_OUT_OF') }}
                                    {{ $voters }}
                                    {{ Lang::txt('COM_WISHLIST_VOTES') }})
                                </span>
                            </h3>

                            <div class="overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            @if($wishlist->access('manage'))
                                                <th>{{ Lang::txt('COM_WISHLIST_MY_OPINION') }}</th>
                                            @endif
                                            <th>{{ Lang::txt('COM_WISHLIST_CONSENSUS') }}</th>
                                            <th>{{ Lang::txt('COM_WISHLIST_COMMUNITY_VOTE') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>{{ Lang::txt('COM_WISHLIST_IMPORTANCE') }}</th>
                                            @if($wishlist->access('manage'))
                                                <td>
                                                    @php
                                                        $importance = [
                                                            ''    => Lang::txt('COM_WISHLIST_SELECT_IMP'),
                                                            '0.0' => '0 - ' . Lang::txt('COM_WISHLIST_RUBBISH'),
                                                            '1'   => '1 - ' . Lang::txt('COM_WISHLIST_MAYBE'),
                                                            '2'   => '2 - ' . Lang::txt('COM_WISHLIST_INTERESTING'),
                                                            '3'   => '3 - ' . Lang::txt('COM_WISHLIST_GOODIDEA'),
                                                            '4'   => '4 - ' . Lang::txt('COM_WISHLIST_IMPORTANT'),
                                                            '5'   => '5 - ' . Lang::txt('COM_WISHLIST_CRITICAL'),
                                                        ];
                                                    @endphp
                                                    {!! \Components\Wishlist\Helpers\Html::formSelect(
                                                        'importance', $importance,
                                                        $wish->ranking('importance'), 'select select-bordered select-sm'
                                                    ) !!}
                                                </td>
                                            @endif
                                            @if($wish->rankings()->total() == 0)
                                                <td>{{ Lang::txt('COM_WISHLIST_NA') }}</td>
                                            @else
                                                <td>
                                                    {!! \Components\Wishlist\Helpers\Html::convertVote(
                                                        $wish->get('average_imp', $wish->ranking('importance')),
                                                        'importance'
                                                    ) !!}
                                                </td>
                                            @endif
                                            <td>
                                                <x-vote-widget
                                                    :likes="$wish->get('positive', 0)"
                                                    :dislikes="$wish->get('negative', 0)"
                                                    vote=""
                                                    :likeUrl="$likeUrl"
                                                    :dislikeUrl="$dislikeUrl"
                                                    :disabled="$voteDisabled"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ Lang::txt('COM_WISHLIST_EFFORT') }}</th>
                                            @if($wishlist->access('manage'))
                                                <td>
                                                    @php
                                                        $effort = [
                                                            ''    => Lang::txt('COM_WISHLIST_SELECT_EFFORT'),
                                                            '5'   => Lang::txt('COM_WISHLIST_FOURHOURS'),
                                                            '4'   => Lang::txt('COM_WISHLIST_ONEDAY'),
                                                            '3'   => Lang::txt('COM_WISHLIST_TWODAYS'),
                                                            '2'   => Lang::txt('COM_WISHLIST_ONEWEEK'),
                                                            '1'   => Lang::txt('COM_WISHLIST_TWOWEEKS'),
                                                            '0.0' => Lang::txt('COM_WISHLIST_TWOMONTHS'),
                                                            '6'   => Lang::txt('COM_WISHLIST_DONT_KNOW'),
                                                        ];
                                                    @endphp
                                                    {!! \Components\Wishlist\Helpers\Html::formSelect(
                                                        'effort', $effort,
                                                        $wish->ranking('effort'), 'select select-bordered select-sm'
                                                    ) !!}
                                                </td>
                                            @endif
                                            @if($wish->rankings()->total() == 0)
                                                <td>{{ Lang::txt('COM_WISHLIST_NA') }}</td>
                                            @else
                                                <td>
                                                    {!! \Components\Wishlist\Helpers\Html::convertVote(
                                                        $wish->get('average_effort', $wish->ranking('effort')),
                                                        'effort'
                                                    ) !!}
                                                </td>
                                            @endif
                                            <td>
                                                @if($wishlist->get('banking'))
                                                    @php
                                                        $isOpenOrAccepted = ($wish->isOpen() || $wish->isAccepted());
                                                    @endphp
                                                    @if($wish->get('bonus', 0) > 0 && $isOpenOrAccepted)
                                                        <a class="badge badge-warning badge-sm"
                                                           href="{{ Route::url($wish->link('addbonus'), false) }}"
                                                           title="{{ Lang::txt('COM_WISHLIST_WISH_ADD_BONUS') }}">
                                                            + {{ $wish->get('bonus', 0) }}
                                                        </a>
                                                    @elseif($isOpenOrAccepted)
                                                        <a class="badge badge-ghost badge-sm"
                                                           href="{{ Route::url($wish->link('addbonus'), false) }}"
                                                           title="{{ Lang::txt('COM_WISHLIST_WISH_ADD_BONUS') }}">0</a>
                                                    @else
                                                        <span class="badge badge-ghost badge-sm opacity-50"
                                                              title="{{ Lang::txt('COM_WISHLIST_WISH_BONUS_NOT_ACCEPTED') }}">
                                                            &mdash;
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                    @if($wishlist->access('manage'))
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <input type="hidden" name="task" value="savevote" />
                                                    <input type="hidden" name="category"
                                                           value="{{ e($wishlist->get('category')) }}" />
                                                    <input type="hidden" name="rid"
                                                           value="{{ e($wishlist->get('referenceid')) }}" />
                                                    <input type="hidden" name="wishid"
                                                           value="{{ e($wish->get('id')) }}" />
                                                    {!! Html::input('token') !!}
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        {{ Lang::txt('COM_WISHLIST_SAVE') }}
                                                    </button>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endif

        {{-- Delete confirmation --}}
        @if($wish->get('action') == 'delete')
            <div role="alert" class="alert alert-warning mb-6" id="action">
                <div>
                    <h4 class="font-semibold">{{ Lang::txt('COM_WISHLIST_ARE_YOU_SURE_DELETE_WISH') }}</h4>
                    <div class="flex gap-2 mt-2">
                        <a class="btn btn-sm btn-error"
                           href="{{ Route::url($wish->link('delete'), false) }}">
                            {{ Lang::txt('COM_WISHLIST_YES') }}
                        </a>
                        <a class="btn btn-sm"
                           href="{{ Route::url($wish->link(), false) }}">
                            {{ Lang::txt('COM_WISHLIST_NO') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Change status form --}}
        @if($wish->get('action') == 'changestatus')
            <div class="card bg-base-100 shadow-sm mb-6" id="action">
                <div class="card-body">
                    <form method="post"
                          action="{{ Route::url('index.php?option=' . $option, false) }}"
                          id="changeStatus">
                        <h4 class="font-semibold mb-3">
                            {{ Lang::txt('COM_WISHLIST_ACTION_CHANGE_STATUS_TO') }}
                        </h4>
                        <p class="text-sm text-base-content/60 mb-3">
                            {{ Lang::txt('COM_WISHLIST_WISH_STATUS_INFO') }}
                        </p>

                        {!! Html::input('token') !!}
                        <input type="hidden" name="option" value="{{ $option }}" />
                        <input type="hidden" name="task" value="editwish" />
                        <input type="hidden" name="wishlist" value="{{ e($wishlist->get('id')) }}" />
                        <input type="hidden" name="category" value="{{ e($wishlist->get('category')) }}" />
                        <input type="hidden" name="rid" value="{{ e($wishlist->get('referenceid')) }}" />
                        <input type="hidden" name="wishid" value="{{ e($wish->get('id')) }}" />

                        <div class="space-y-2 mb-4">
                            <x-form-field name="status" inputId="field-status-pending"
                                          :label="Lang::txt('COM_WISHLIST_WISH_STATUS_PENDING')" type="checkbox">
                                <input type="radio" name="status" id="field-status-pending"
                                       class="radio radio-sm" value="pending"
                                       {{ $wish->isOpen() ? 'checked' : '' }} />
                            </x-form-field>

                            <x-form-field name="status" inputId="field-status-accepted"
                                          :label="Lang::txt('COM_WISHLIST_WISH_STATUS_ACCEPTED')" type="checkbox">
                                <input type="radio" name="status" id="field-status-accepted"
                                       class="radio radio-sm" value="accepted"
                                       {{ $wish->isAccepted() ? 'checked' : '' }} />
                            </x-form-field>

                            <x-form-field name="status" inputId="field-status-rejected"
                                          :label="Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED')" type="checkbox">
                                <input type="radio" name="status" id="field-status-rejected"
                                       class="radio radio-sm" value="rejected"
                                       {{ $wish->isRejected() ? 'checked' : '' }} />
                            </x-form-field>

                            @php
                                $isAssigned = $wish->get('assigned')
                                    && $wish->get('assigned') != User::get('id');
                            @endphp
                            <x-form-field name="status" inputId="field-status-granted"
                                          :label="Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED')"
                                          type="checkbox">
                                <input type="radio" name="status" id="field-status-granted"
                                       class="radio radio-sm" value="granted"
                                       {{ $wish->get('status') == 1 ? 'checked' : '' }}
                                       {{ $isAssigned ? 'disabled' : '' }} />
                            </x-form-field>
                            @if($isAssigned)
                                <p class="text-sm text-warning ml-6">
                                    {{ Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED_WARNING') }}
                                </p>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                {{ Lang::txt('COM_WISHLIST_ACTION_CHANGE_STATUS') }}
                            </button>
                            <a class="btn btn-sm" href="{{ Route::url($wish->link(), false) }}">
                                {{ Lang::txt('JCANCEL') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Add bonus form --}}
        @if(!$wish->isDeleted() && !$wish->isWithdrawn())
            @if($wish->get('action') == 'addbonus' && $wish->get('status') != 1 && $wishlist->get('banking'))
                <div class="card bg-base-100 shadow-sm mb-6" id="action">
                    <div class="card-body">
                        <form method="post"
                              action="{{ Route::url('index.php?option=' . $option, false) }}"
                              id="addBonus">
                            <h4 class="font-semibold mb-2">
                                {{ Lang::txt('COM_WISHLIST_WISH_ADD_BONUS') }}
                            </h4>
                            <p class="text-sm text-base-content/60 mb-3">
                                {{ Lang::txt('COM_WISHLIST_WHY_ADDBONUS') }}
                            </p>

                            <p class="font-medium mb-3">
                                {{ $wish->get('bonusgivenby') }} {{ Lang::txt('COM_WISHLIST_MULTIPLE_USERS') }}
                                {{ Lang::txt('COM_WISHLIST_WISH_BONUS_CONTRIBUTED_TOTAL') }}
                                {{ $wish->get('bonus', 0) }} {{ Lang::txt('COM_WISHLIST_POINTS') }}
                                {{ Lang::txt('COM_WISHLIST_WISH_BONUS_AS_BONUS') }}
                            </p>

                            <input type="hidden" name="task" value="addbonus" />
                            <input type="hidden" name="wishlist" value="{{ e($wishlist->get('id')) }}" />
                            <input type="hidden" name="wish" value="{{ e($wish->get('id')) }}" />

                            <x-form-field name="amount" :label="Lang::txt('COM_WISHLIST_ACTION_ADD')">
                                <input type="text" name="amount" id="field-amount"
                                       class="input input-bordered input-sm w-24"
                                       maxlength="4" value=""
                                       {{ $wish->get('funds') <= 0 ? 'disabled' : '' }} />
                                <span class="text-sm text-base-content/60 ml-2">
                                    ({{ Lang::txt('COM_WISHLIST_NOTICE_OUT_OF') }}
                                    {{ $wish->get('funds') }}
                                    {{ Lang::txt('COM_WISHLIST_NOTICE_POINTS_AVAILABLE') }}
                                    @php
                                        $pointsUrl = Route::url(
                                            'index.php?option=com_members&id='
                                            . User::get('id') . '&active=points',
                                            false
                                        );
                                    @endphp
                                    <a class="link" href="{{ $pointsUrl }}">
                                        {{ Lang::txt('COM_WISHLIST_ACCOUNT') }}
                                    </a>)
                                </span>
                            </x-form-field>

                            <div class="flex gap-2 mt-3">
                                @if($wish->get('funds') > 0)
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        {{ Lang::txt('COM_WISHLIST_ACTION_ADD_POINTS') }}
                                    </button>
                                @endif
                                <a class="btn btn-sm" href="{{ Route::url($wish->link(), false) }}">
                                    {{ Lang::txt('JCANCEL') }}
                                </a>
                            </div>

                            @if($wish->get('funds') <= 0)
                                <p class="text-sm text-warning mt-2">
                                    {{ Lang::txt('COM_WISHLIST_SORRY_NO_FUNDS') }}
                                </p>
                            @endif
                        </form>
                    </div>
                </div>
            @endif

            {{-- Move wish form --}}
            @if($wish->get('action') == 'move')
                <div class="card bg-base-100 shadow-sm mb-6" id="action">
                    <div class="card-body">
                        <form method="post"
                              action="{{ Route::url('index.php?option=' . $option, false) }}"
                              id="moveWish">
                            @if($__view->getError())
                                <div role="alert" class="alert alert-error mb-3">
                                    <span>{{ $__view->getError() }}</span>
                                </div>
                            @endif
                            <h4 class="font-semibold mb-3">
                                {{ Lang::txt('COM_WISHLIST_WISH_BELONGS_TO') }}:
                            </h4>

                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="task" value="movewish" />
                            <input type="hidden" name="wishlist" value="{{ $wishlist->get('id') }}" />
                            <input type="hidden" name="wish" value="{{ $wish->get('id') }}" />

                            <div class="space-y-2 mb-4">
                                <x-form-field name="type" inputId="field-type-general"
                                              :label="Lang::txt('COM_WISHLIST_MAIN_NAME')" type="checkbox">
                                    <input type="radio" name="type" id="field-type-general"
                                           class="radio radio-sm" value="general"
                                           {{ $wishlist->get('category') == 'general' ? 'checked' : '' }} />
                                </x-form-field>

                                <x-form-field name="type" inputId="field-type-resource"
                                              :label="Lang::txt('COM_WISHLIST_RESOURCE_NAME')" type="checkbox">
                                    <input type="radio" name="type" id="field-type-resource"
                                           class="radio radio-sm" value="resource"
                                           {{ $wishlist->get('category') == 'resource' ? 'checked' : '' }} />
                                </x-form-field>

                                <x-form-field name="resource" inputId="acresource" label="">
                                    @php
                                        $resourceVal = ($wishlist->get('category') == 'resource')
                                            ? $wishlist->get('referenceid') : '';
                                    @endphp
                                    <input type="text" name="resource" id="acresource"
                                           class="input input-bordered input-sm w-full"
                                           value="{{ $resourceVal }}" autocomplete="off" />
                                </x-form-field>

                                @php
                                    $catsHasGroup = $wish->get('cats')
                                        && preg_replace("/group/", '', $wish->get('cats')) != $wish->get('cats');
                                @endphp
                                @if($catsHasGroup)
                                    <x-form-field name="type" inputId="field-type-group"
                                                  :label="Lang::txt('COM_WISHLIST_GROUP_NAME')" type="checkbox">
                                        <input type="radio" name="type" id="field-type-group"
                                               class="radio radio-sm" value="group"
                                               {{ $wishlist->get('category') == 'group' ? 'checked' : '' }} />
                                    </x-form-field>

                                    <x-form-field name="group" inputId="acgroup" label="">
                                        @php
                                            $groupVal = ($wishlist->get('category') == 'group')
                                                ? $wishlist->item('alias') : '';
                                        @endphp
                                        <input type="text" name="group" id="acgroup"
                                               class="input input-bordered input-sm w-full"
                                               value="{{ $groupVal }}" autocomplete="off" />
                                    </x-form-field>
                                @endif
                            </div>

                            <x-form-section :heading="Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS')">
                                <x-form-field name="keepcomments" inputId="field-keepcomments"
                                              :label="Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_COMMENTS')"
                                              type="checkbox">
                                    <input type="checkbox" name="keepcomments" id="field-keepcomments"
                                           class="checkbox checkbox-sm" value="1" checked />
                                </x-form-field>

                                <x-form-field name="keepplan" inputId="field-keepplan"
                                              :label="Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_PLAN')"
                                              type="checkbox">
                                    <input type="checkbox" name="keepplan" id="field-keepplan"
                                           class="checkbox checkbox-sm" value="1" checked />
                                </x-form-field>

                                <x-form-field name="keepstatus" inputId="field-keepstatus"
                                              :label="Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_STATUS')"
                                              type="checkbox">
                                    <input type="checkbox" name="keepstatus" id="field-keepstatus"
                                           class="checkbox checkbox-sm" value="1" checked />
                                </x-form-field>

                                <x-form-field name="keepfeedback" inputId="field-keepfeedback"
                                              :label="Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_VOTES')"
                                              type="checkbox">
                                    <input type="checkbox" name="keepfeedback" id="field-keepfeedback"
                                           class="checkbox checkbox-sm" value="1" checked />
                                </x-form-field>
                            </x-form-section>

                            <div class="flex gap-2 mt-3">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    {{ Lang::txt('COM_WISHLIST_ACTION_MOVE_THIS_WISH') }}
                                </button>
                                <a class="btn btn-sm" href="{{ Route::url($wish->link(), false) }}">
                                    {{ Lang::txt('JCANCEL') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endif {{-- if not withdrawn --}}

        {{-- Comments section --}}
        @if(!$wish->isDeleted() && !$wish->isWithdrawn())
            @php
                $comments = $wish->comments()
                    ->whereIn('state', [
                        \Components\Wishlist\Models\Comment::STATE_PUBLISHED,
                        \Components\Wishlist\Models\Comment::STATE_FLAGGED,
                    ])
                    ->rows();
            @endphp

            <div class="mb-6" id="section-comments">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ Lang::txt('COM_WISHLIST_COMMENTS') }} ({{ $comments->count() }})
                    </h3>
                    @php
                        $addCommentLink = Route::url($wish->link('comment'), false);
                        if (User::isGuest()) {
                            $addCommentLink = Route::url(
                                'index.php?option=com_users&view=login&return='
                                . base64_encode($addCommentLink),
                                false
                            );
                        }
                    @endphp
                    <a class="btn btn-sm btn-primary" href="{{ $addCommentLink }}">
                        {{ Lang::txt('COM_WISHLIST_ADD_A_COMMENT') }}
                    </a>
                </div>

                @if($comments->count() > 0)
                    {!! $__view->view('_list')
                        ->set('parent', 0)
                        ->set('cls', 'odd')
                        ->set('depth', 0)
                        ->set('option', $option)
                        ->set('comments', $comments)
                        ->set('wishlist', $wishlist)
                        ->set('wish', $wish)
                        ->loadTemplate() !!}
                @else
                    <p class="text-base-content/60">
                        {{ Lang::txt('COM_WISHLIST_NO_COMMENTS') }}
                        <a class="link" href="{{ Route::url($wish->link('comment'), false) }}">
                            {{ Lang::txt('COM_WISHLIST_MAKE_A_COMMENT') }}
                        </a>.
                    </p>
                @endif
            </div>

            {{-- Add comment form --}}
            @if(!User::isGuest())
                <div class="card bg-base-100 shadow-sm mb-6">
                    <div class="card-body">
                        <form action="{{ Route::url('index.php?option=' . $option, false) }}"
                              method="post" id="commentform"
                              enctype="multipart/form-data">
                            <h3 class="font-semibold mb-3">
                                {{ Lang::txt('COM_WISHLIST_ACTION_ADD_COMMENT') }}
                            </h3>

                            <input type="hidden" name="option" value="{{ e($option) }}" />
                            <input type="hidden" name="listid" value="{{ e($wishlist->get('id')) }}" />
                            <input type="hidden" name="wishid" value="{{ e($wish->get('id')) }}" />
                            <input type="hidden" name="task" value="savereply" />
                            <input type="hidden" name="referenceid" value="{{ e($wish->get('id')) }}" />
                            <input type="hidden" name="cat" value="wish" />
                            <input type="hidden" name="comment[item_id]" value="{{ $wish->get('id') }}" />
                            <input type="hidden" name="comment[item_type]" value="wish" />
                            <input type="hidden" name="comment[parent]" value="0" />
                            {!! Html::input('token') !!}

                            <x-form-field name="comment[content]"
                                          inputId="comment{{ $wish->get('id') }}"
                                          :label="Lang::txt('COM_WISHLIST_ENTER_COMMENTS')">
                                {!! $__view->editor(
                                    'comment[content]', '', 35, 4,
                                    'comment' . $wish->get('id'),
                                    ['class' => 'textarea textarea-bordered w-full']
                                ) !!}
                            </x-form-field>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                                <x-form-field name="upload" inputId="comment-upload"
                                              :label="Lang::txt('COM_WISHLIST_ACTION_ATTACH_FILE')">
                                    <input type="file" name="upload" id="comment-upload"
                                           class="file-input file-input-bordered file-input-sm w-full" />
                                </x-form-field>

                                <x-form-field name="description" inputId="comment-description"
                                              :label="Lang::txt('COM_WISHLIST_ACTION_ATTACH_FILE_DESC')">
                                    <input type="text" name="description" id="comment-description"
                                           class="input input-bordered input-sm w-full" value="" />
                                </x-form-field>
                            </div>

                            <x-form-field name="comment[anonymous]" inputId="comment-anonymous"
                                          :label="Lang::txt('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY')"
                                          type="checkbox">
                                <input type="checkbox" name="comment[anonymous]" id="comment-anonymous"
                                       class="checkbox checkbox-sm" value="1" />
                            </x-form-field>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    {{ Lang::txt('COM_WISHLIST_POST_COMMENT') }}
                                </button>
                            </div>

                            <p class="text-sm text-base-content/60 mt-3">
                                <strong>{{ Lang::txt('COM_WISHLIST_COMMENT_KEEP_POLITE') }}</strong>
                            </p>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Implementation plan (admin/advisory only) --}}
            @if($wishlist->access('manage'))
                <div class="card bg-base-100 shadow-sm mb-6" id="plan">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold">
                                {{ Lang::txt('COM_WISHLIST_IMPLEMENTATION_PLAN') }}
                                @if($wish->plan->get('id'))
                                    (<a class="link" href="{{ Route::url($wish->link('editplan'), false) }}">
                                        {{ Lang::txt('COM_WISHLIST_ACTION_EDIT') }}
                                    </a>)
                                @else
                                    ({{ Lang::txt('COM_WISHLIST_PLAN_NOT_STARTED') }})
                                @endif
                            </h3>
                            @if($wish->get('action') != 'editplan')
                                <a class="btn btn-sm btn-primary"
                                   href="{{ Route::url($wish->link('editplan'), false) }}">
                                    {{ Lang::txt('COM_WISHLIST_ADD_TO_THE_PLAN') }}
                                </a>
                            @endif
                        </div>

                        <form action="{{ Route::url('index.php?option=' . $option, false) }}"
                              method="post" id="planform"
                              enctype="multipart/form-data">
                            @if($wish->get('action') == 'editplan')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <x-form-field name="assigned" inputId="assigned"
                                                  :label="Lang::txt('COM_WISHLIST_WISH_ASSIGN_TO')">
                                        {!! $wish->get('assignlist') !!}
                                    </x-form-field>

                                    <x-form-field name="publish_up" inputId="publish_up"
                                                  :label="Lang::txt('COM_WISHLIST_DUE') . ' (' . Lang::txt('COM_WISHLIST_OPTIONAL') . ')'">
                                        <input type="text" name="publish_up" id="publish_up"
                                               class="input input-bordered input-sm w-full"
                                               placeholder="YYYY-MM-DD"
                                               value="{{ $due ? $wish->due() : '' }}" />
                                    </x-form-field>
                                </div>

                                @if($wish->get('plan'))
                                    <x-form-field name="create_revision" inputId="create_revision"
                                                  :label="Lang::txt('COM_WISHLIST_PLAN_NEW_REVISION')"
                                                  type="checkbox">
                                        <input type="checkbox" name="create_revision" id="create_revision"
                                               class="checkbox checkbox-sm" value="1" />
                                    </x-form-field>
                                @else
                                    <input type="hidden" name="create_revision" value="0" />
                                @endif

                                <x-form-field name="pagetext" inputId="pagetext"
                                              :label="Lang::txt('COM_WISHLIST_ACTION_INSERT_TEXT')">
                                    {!! $__view->editor(
                                        'pagetext',
                                        e($wish->plan->get('pagetext')),
                                        35, 40, 'pagetext',
                                        ['class' => 'textarea textarea-bordered w-full']
                                    ) !!}
                                </x-form-field>

                                <input type="hidden" name="pageid"
                                       value="{{ $wish->plan()->get('id', 0) }}" />
                                <input type="hidden" name="version"
                                       value="{{ $wish->plan()->get('version', 1) }}" />
                                <input type="hidden" name="wishid"
                                       value="{{ $wish->get('id') }}" />
                                <input type="hidden" name="option" value="{{ $option }}" />
                                <input type="hidden" name="created_by"
                                       value="{{ User::get('id') }}" />
                                <input type="hidden" name="task" value="saveplan" />
                                {!! Html::input('token') !!}

                                <div class="flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        {{ Lang::txt('COM_WISHLIST_SAVE') }}
                                    </button>
                                    <a class="btn btn-sm"
                                       href="{{ Route::url($wish->link(), false) }}">
                                        {{ Lang::txt('JCANCEL') }}
                                    </a>
                                </div>

                                <p class="text-sm text-base-content/60 mt-3">
                                    {{ Lang::txt('COM_WISHLIST_PLAN_FORMATTING_HELP') }}
                                </p>
                            @elseif(!$wish->plan->get('id'))
                                <p>
                                    {{ Lang::txt('COM_WISHLIST_THERE_IS_NO_PLAN') }}
                                    <a class="link" href="{{ Route::url($wish->link('editplan'), false) }}">
                                        {{ Lang::txt('COM_WISHLIST_START_PLAN') }}
                                    </a>.
                                </p>
                                @if($wish->isOpen() || $wish->isAccepted())
                                    <p class="text-sm text-base-content/60 mt-2">
                                        {{ Lang::txt('COM_WISHLIST_PLAN_IS_ASSIGNED') }}
                                        {!! $assigned !!}
                                        {{ Lang::txt('COM_WISHLIST_PLAN_IS_DUE') }}
                                        <a class="link" href="{{ Route::url($wish->link('editplan'), false) }}">
                                            {{ ($wish->due() && $wish->due() != '0000-00-00 00:00:00')
                                                ? $wish->due()
                                                : Lang::txt('COM_WISHLIST_DUE_NEVER') }}
                                        </a>
                                    </p>
                                @endif
                            @else
                                @if($wish->isOpen() || $wish->isAccepted())
                                    <p class="text-sm text-base-content/60 mb-3">
                                        {{ Lang::txt('COM_WISHLIST_PLAN_IS_ASSIGNED') }}
                                        {!! $assigned !!}
                                        {{ Lang::txt('COM_WISHLIST_PLAN_IS_DUE') }}
                                        <a class="link" href="{{ Route::url($wish->link('editplan'), false) }}">
                                            {{ ($wish->due() && $wish->due() != '0000-00-00 00:00:00')
                                                ? $wish->due()
                                                : Lang::txt('COM_WISHLIST_DUE_NEVER') }}
                                        </a>.
                                    </p>
                                @endif
                                <div class="prose prose-sm max-w-none">
                                    <p class="text-xs text-base-content/50">
                                        {{ Lang::txt('COM_WISHLIST_PLAN_LAST_EDIT') }}
                                        {{ $wish->plan->created('date') }}
                                        {{ Lang::txt('COM_WISHLIST_AT') }}
                                        {{ $wish->plan->created('time') }}
                                        {{ Lang::txt('COM_WISHLIST_BY') }}
                                        {{ $wish->plan->creator->get('name') }}
                                    </p>
                                    {!! $wish->plan->content !!}
                                </div>
                            @endif
                        </form>

                        @if($wish->get('action') == 'editplan')
                            <p class="text-sm text-base-content/60 mt-3">
                                {{ Lang::txt('COM_WISHLIST_PLAN_DEADLINE_EXPLANATION') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        @endif {{-- if not withdrawn --}}
    @endif {{-- reported/withdrawn/normal --}}
</x-page-container>
