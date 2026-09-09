@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$base = $member->link() . '&active=' . $name;

$__view->css()->js();
@endphp

<form method="get" action="{{ Route::url($base . '&task=followers') }}" id="collections">
    {!! $__view->view('_submenu', 'collection')
        ->set('option', $option)
        ->set('member', $member)
        ->set('params', $params)
        ->set('name', $name)
        ->set('active', 'followers')
        ->set('collections', $collections)
        ->set('posts', $posts)
        ->set('followers', $total)
        ->set('following', $following)
        ->loadTemplate() !!}

    @if ($rows->total() > 0)
        <div class="container">
            <table class="followers entries">
                <caption>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOWING_YOU') }}</caption>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                            $followerObj = $row->follower();
                            $followerImg = $followerObj->image();
                            $followerTitle = e(stripslashes($followerObj->title() ?: ''));
                            $profilePicAlt = Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $followerTitle);
                            $followerLink = Route::url($followerObj->link());
                            $createdDate = $row->get('created');
                            $formattedDate = Date::of($createdDate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                        @endphp
                        <tr class="{{ $row->get('follower_type') }}">
                            <th class="entry-img">
                                <img src="{{ $followerImg }}"
                                    width="40"
                                    height="40"
                                    alt="{{ $profilePicAlt }}" />
                            </th>
                            <td>
                                <a class="entry-title" href="{{ $followerLink }}">
                                    {{ $followerTitle }}
                                </a>
                                <br />
                                <span class="entry-details">
                                    <span class="follower count">
                                        {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWERS', $row->count('followers')) !!}
                                    </span>
                                    <span class="following count">
                                        {!! Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_FOLLOWING', $row->count('following')) !!}
                                    </span>
                                </span>
                            </td>
                            <td>
                                <time datetime="{{ $createdDate }}">{{ $formattedDate }}</time>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @php
                $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
                $pageNav->setAdditionalUrlParam('id', $member->get('id'));
                $pageNav->setAdditionalUrlParam('active', 'collections');
                $pageNav->setAdditionalUrlParam('task', 'followers');
            @endphp
            {!! $pageNav->render() !!}
        </div>
    @else
        <div id="collection-introduction">
            @if ($params->get('access-manage-collection'))
                <div class="instructions">
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOWING_YOU_NONE') }}</p>
                </div>
                <div class="questions">
                    <p><strong>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_ARE_FOLLOWERS') }}</strong></p>
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_WHAT_ARE_FOLLOWERS_EXPLANATION') }}</p>
                </div>
            @else
                <div class="instructions">
                    <p>{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_MEMBER_HAS_NO_FOLLOWERS') }}</p>
                </div>
            @endif
        </div>
    @endif
</form>
