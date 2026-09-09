@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$base = $member->link() . '&active=' . $name;

$__view->css()->js();
@endphp

<form method="get" action="{{ Route::url($base . '&task=following') }}" id="collections">
    {!! $__view->view('_submenu', 'collection')
        ->set('option', $option)
        ->set('member', $member)
        ->set('params', $params)
        ->set('name', $name)
        ->set('active', 'following')
        ->set('collections', $collections)
        ->set('posts', $posts)
        ->set('followers', $followers)
        ->set('following', $rows->total())
        ->loadTemplate() !!}

    @if ($rows->total() > 0)
        <div class="container">
            <table class="following entries">
                <tbody>
                    @foreach ($rows as $row)
                        @php
                            $followingObj = $row->following();
                            $followingImg = $followingObj->image();
                            $followingTitle = e(stripslashes($followingObj->title() ?: ''));
                            $profilePicAlt = Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $followingTitle);
                            $followingLink = Route::url($followingObj->link());
                            $followType = $row->get('following_type');
                            $followId = $row->get('following_id');
                        @endphp
                        <tr class="{{ $followType }}">
                            <th>
                                @if ($followingImg)
                                    <img src="{{ $followingImg }}"
                                        width="40"
                                        height="40"
                                        alt="{{ $profilePicAlt }}" />
                                @else
                                    <span class="entry-id">{{ $followId }}</span>
                                @endif
                            </th>
                            <td>
                                <a class="entry-title" href="{{ $followingLink }}">
                                    {{ $followingTitle }}
                                </a>
                                @if ($followType == 'collection')
                                    {{ Lang::txt('by %s', e(stripslashes($followingObj->creator('name') ?: ''))) }}
                                @endif
                                <br />
                                <span class="entry-details">
                                    <span class="follower count">
                                        {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWERS', $row->count('followers')) !!}
                                    </span>
                                    @if ($followType != 'collection')
                                        <span class="following count">
                                            {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWING', $row->count('following')) !!}
                                        </span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if ($params->get('access-manage-collection'))
                                    <a class="icon-unfollow unfollow btn"
                                        data-id="{{ $followId }}"
                                        data-text-follow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW') }}"
                                        data-text-unfollow="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW') }}"
                                        href="{{ Route::url($followingObj->link('unfollow')) }}">
                                        <span>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW') }}</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @php
                $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
                $pageNav->setAdditionalUrlParam('id', $member->get('id'));
                $pageNav->setAdditionalUrlParam('active', 'collections');
                $pageNav->setAdditionalUrlParam('task', 'following');
            @endphp
            {!! $pageNav->render() !!}
        </div>
    @else
        <div id="collection-introduction">
            @if ($params->get('access-manage-collection'))
                <div class="instructions">
                    <ol>
                        <li>{!! Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP1', Route::url('index.php?option=com_collections')) !!}</li>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP2') }}</li>
                        <li>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP3') }}</li>
                    </ol>
                </div>
                <div class="questions">
                    <p><strong>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_IS_FOLLOWING') }}</strong></p>
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING_EXPLANATION') }}</p>
                </div>
            @else
                <div class="instructions">
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_NOT_FOLLOWING_ANYONE') }}</p>
                </div>
            @endif
        </div>
    @endif
</form>
